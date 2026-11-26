<?php

namespace Tests\Feature;

use App\Mail\EmergencyReportAlertMail;
use App\Models\AccessLog;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\BullyingNlpClassifier;
use App\Support\TicketCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class Batch3M5M6Test extends TestCase
{
    use RefreshDatabase;

    private function seedCategoryAndLocation(): void
    {
        IncidentCategory::firstOrCreate(
            ['label' => 'Bullying Fisik'],
            ['active' => true, 'visible_to_kesiswaan' => true, 'order_index' => 1]
        );
        IncidentCategory::firstOrCreate(
            ['label' => 'Masalah Pribadi / Keluarga'],
            ['active' => true, 'visible_to_kesiswaan' => false, 'order_index' => 2]
        );
        IncidentLocation::firstOrCreate(
            ['label' => 'Koridor Kelas'],
            ['active' => true, 'order_index' => 1]
        );
    }

    public function test_access_log_is_append_only_records_staff_actions_and_masks_hidden_reports_for_kesiswaan(): void
    {
        $this->seedCategoryAndLocation();

        $bk = User::factory()->bk()->create(['name' => 'Guru BK Utama', 'email' => 'bk1@telkom.sch.id']);
        $kesiswaan = User::factory()->kesiswaan()->create(['name' => 'Tim Kesiswaan']);

        $visibleReport = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'story' => 'Laporan yang boleh dilihat kesiswaan.',
            'status' => 'belum_dibaca',
            'urgency' => 'Sedang',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $hiddenReport = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Masalah Pribadi / Keluarga',
            'location' => 'Koridor Kelas',
            'story' => 'Laporan privat khusus BK.',
            'status' => 'belum_dibaca',
            'urgency' => 'Sedang',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $this->actingAs($bk)->get("/bk/reports/{$visibleReport->id}")->assertOk();
        $this->actingAs($bk)->get("/bk/reports/{$hiddenReport->id}")->assertOk();
        $this->actingAs($bk)->postJson("/bk/reports/{$visibleReport->id}/status", ['status' => 'ditindaklanjuti'])->assertOk();
        $this->actingAs($bk)->postJson("/bk/reports/{$visibleReport->id}/reply", ['counselor_reply' => 'Tindak lanjut segera.'])->assertOk();

        $this->assertGreaterThanOrEqual(4, AccessLog::count());

        // Append-only: update & delete harus ditolak
        $log = AccessLog::firstOrFail();
        $updatedRejected = false;
        try {
            $log->update(['action' => 'tampered']);
        } catch (\LogicException) {
            $updatedRejected = true;
        }
        $this->assertTrue($updatedRejected);

        $deletedRejected = false;
        try {
            $log->delete();
        } catch (\LogicException) {
            $deletedRejected = true;
        }
        $this->assertTrue($deletedRejected);

        // BK melihat seluruh kasus pada /bk/access-log tanpa "Sarah Jenkins"
        $bkLogView = $this->actingAs($bk)->get('/bk/access-log');
        $bkLogView->assertOk()
            ->assertSee($visibleReport->case_label)
            ->assertSee($hiddenReport->case_label)
            ->assertDontSee('Sarah Jenkins');

        // Kesiswaan hanya melihat kasus yang diizinkan; kasus tersembunyi disamarkan sebagai "Laporan lain"
        $kesiswaanLogView = $this->actingAs($kesiswaan)->get('/kesiswaan/access-log');
        $kesiswaanLogView->assertOk()
            ->assertSee($visibleReport->case_label)
            ->assertSee('Laporan lain')
            ->assertDontSee($hiddenReport->case_label);
    }

    public function test_emergency_alert_mail_sent_to_all_bk_strictly_without_story_or_identity(): void
    {
        Mail::fake();
        $this->seedCategoryAndLocation();

        $bk1 = User::factory()->bk()->create(['email' => 'bk1@sekolah.sch.id']);
        $bk2 = User::factory()->bk()->create(['email' => 'bk2@sekolah.sch.id']);
        $kesiswaan = User::factory()->kesiswaan()->create(['email' => 'kesiswaan@sekolah.sch.id']);
        $walas = User::factory()->walas()->create(['email' => 'walas@sekolah.sch.id']);

        // Laporan non-darurat tidak memicu email darurat
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Sedang',
            'story' => 'Cerita non darurat minimal lima huruf.',
            'is_anonymous' => true,
        ])->assertOk();

        Mail::assertNothingQueued();

        // Laporan Darurat memicu email antrean ke semua BK tanpa cerita atau identitas pelapor
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Darurat',
            'story' => 'Ada pemukulan keras di belakang laboratorium.',
            'is_anonymous' => false,
            'reporter_name' => 'Rina Rahasia',
            'reporter_class' => '8B',
            'reporter_contact' => '081234560000',
        ])->assertOk();

        Mail::assertQueued(EmergencyReportAlertMail::class, 2);
        Mail::assertQueued(EmergencyReportAlertMail::class, fn (EmergencyReportAlertMail $m) => $m->hasTo($bk1->email));
        Mail::assertQueued(EmergencyReportAlertMail::class, fn (EmergencyReportAlertMail $m) => $m->hasTo($bk2->email));
        Mail::assertNotQueued(EmergencyReportAlertMail::class, fn (EmergencyReportAlertMail $m) => $m->hasTo($kesiswaan->email));
        Mail::assertNotQueued(EmergencyReportAlertMail::class, fn (EmergencyReportAlertMail $m) => $m->hasTo($walas->email));

        $report = StudentReport::latest('id')->firstOrFail();
        $rendered = (new EmergencyReportAlertMail($report, 'SMP Telkom Purwokerto'))->render();

        $this->assertStringContainsString($report->case_label, $rendered);
        $this->assertStringContainsString('Bullying Fisik', $rendered);
        $this->assertStringNotContainsString('pemukulan keras', $rendered);
        $this->assertStringNotContainsString('Rina Rahasia', $rendered);
        $this->assertStringNotContainsString('081234560000', $rendered);
    }

    public function test_turnstile_and_honeypot_block_spam_submissions(): void
    {
        $this->seedCategoryAndLocation();

        // Honeypot terisi -> ditolak 422 tanpa menyimpan
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'story' => 'Isi spam otomatis dari bot.',
            'website' => 'https://bot-spam.example',
        ])->assertStatus(422);

        $this->assertSame(0, StudentReport::count());

        // Turnstile aktif -> gagal jika respons Turnstile false, lolos jika true
        config(['services.turnstile.secret_key' => 'secret-test-key']);

        Http::fake([
            'challenges.cloudflare.com/*' => Http::sequence()
                ->push(['success' => false], 200)
                ->push(['success' => true], 200),
        ]);

        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'story' => 'Percobaan gagal token Turnstile.',
            'cf-turnstile-response' => 'invalid-token',
        ])->assertStatus(422);

        $this->assertSame(0, StudentReport::count());

        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'story' => 'Percobaan dengan token Turnstile sah.',
            'cf-turnstile-response' => 'valid-token',
        ])->assertOk();

        $this->assertSame(1, StudentReport::count());
    }

    public function test_max_5_reports_per_device_per_day_not_blocked_by_shared_school_ip(): void
    {
        $this->seedCategoryAndLocation();

        $deviceA = '11111111-1111-4111-8111-111111111111';
        $deviceB = '22222222-2222-4222-8222-222222222222';

        for ($i = 1; $i <= 5; $i++) {
            $this->withCredentials()
                ->withCookie('hiowy_device_id', $deviceA)
                ->postJson('/s/lapor', [
                    'category' => 'Bullying Fisik',
                    'location' => 'Koridor Kelas',
                    'story' => "Laporan harian ke-{$i} dari perangkat A.",
                ])->assertOk();
        }

        // Kiriman ke-6 di hari yang sama dari perangkat A -> ditolak 429
        $this->withCredentials()
            ->withCookie('hiowy_device_id', $deviceA)
            ->postJson('/s/lapor', [
                'category' => 'Bullying Fisik',
                'location' => 'Koridor Kelas',
                'story' => 'Laporan ke-6 dari perangkat A melebihi kuota.',
            ])->assertStatus(429);

        // Perangkat B dari IP yang SAMA (127.0.0.1) tetap boleh melapor!
        $this->withCredentials()
            ->withCookie('hiowy_device_id', $deviceB)
            ->postJson('/s/lapor', [
                'category' => 'Bullying Fisik',
                'location' => 'Koridor Kelas',
                'story' => 'Laporan pertama dari perangkat B pada Wi-Fi sekolah yang sama.',
            ])->assertOk();

        $this->assertSame(6, StudentReport::count());
    }

    public function test_password_min_10_chars_login_lockout_after_5_failures_and_60min_session(): void
    {
        $this->assertSame(60, (int) config('session.lifetime'));

        $bk = User::factory()->bk()->create([
            'email' => 'bk-lock@sekolah.sch.id',
        ]);

        // Password kurang dari 10 karakter ditolak saat update profil password BK
        $this->actingAs($bk)->post('/bk/profile/password', [
            'current_password' => 'StrongPass#2026',
            'password' => 'Abc1!234',
            'password_confirmation' => 'Abc1!234',
        ])->assertSessionHasErrors('password');

        // 5 kali percobaan gagal login memicu penguncian sementara
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/login', [
                'email' => 'bk-lock@sekolah.sch.id',
                'password' => 'SalahTerus!123',
            ])->assertSessionHasErrors('email');
        }

        $res = $this->post('/login', [
            'email' => 'bk-lock@sekolah.sch.id',
            'password' => 'StrongPass#2026',
        ]);
        $res->assertSessionHasErrors('email');
        $this->assertTrue(AccessLog::where('action', 'login_locked')->exists());
    }

    public function test_training_mode_isolates_simulation_reports_and_skips_emergency_mail_and_ml(): void
    {
        Mail::fake();
        Queue::fake();
        $this->seedCategoryAndLocation();

        $bk = User::factory()->bk()->create(['email' => 'bk-train@sekolah.sch.id']);
        $kesiswaan = User::factory()->kesiswaan()->create();

        // Toggle on/off Mode Latihan & Modul NLP sudah dihapus dari halaman Kelola & Dashboard BK
        $this->actingAs($bk)->get('/bk/manage')
            ->assertOk()
            ->assertDontSee('Mode Latihan')
            ->assertDontSee('Modul NLP AI')
            ->assertDontSee("toggleSetting('training_mode'", false)
            ->assertDontSee("toggleSetting('nlp_enabled'", false);

        $this->actingAs($bk)->get('/bk')
            ->assertOk()
            ->assertDontSee('Mode Latihan');

        // Rute khusus /latihan tetap mengisolasi simulasi tanpa email darurat & tanpa NLP
        $this->postJson('/latihan', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Darurat',
            'story' => 'Simulasi pelatihan guru: laporan uji coba.',
        ])->assertOk()->assertJson(['is_training' => true]);

        Mail::assertNothingQueued();
        Queue::assertNothingPushed();

        $report = StudentReport::firstOrFail();
        $this->assertTrue($report->is_training);
        $this->assertSame('belum_diklasifikasi', $report->ml_status);

        // Tidak muncul pada dashboard asli BK maupun Kesiswaan
        $this->actingAs($bk)->get('/bk')->assertOk()->assertDontSee('#'.$report->case_label)->assertDontSee('Simulasi pelatihan guru');
        $this->actingAs($bk)->get('/bk/statistics')->assertOk()->assertSee('Total 0 Kasus');
        $this->actingAs($kesiswaan)->get('/kesiswaan')->assertOk()->assertDontSee('#'.$report->case_label)->assertDontSee('Simulasi pelatihan guru');
        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}")->assertForbidden();

        // Muncul khusus pada /bk?mode=latihan
        $this->actingAs($bk)->get('/bk?mode=latihan')->assertOk()->assertSee('#'.$report->case_label);
    }

    public function test_nlp_is_auto_active_predicts_urgency_preserves_darurat_and_supports_http_fallback(): void
    {
        Mail::fake();
        $this->seedCategoryAndLocation();
        $bk = User::factory()->bk()->create(['email' => 'bk-nlp@sekolah.sch.id']);

        // 1. Model NLP Linear SVM auto-aktif langsung saat submit /s/lapor tanpa perlu toggle on/off
        // Cerita berat (meski pelapor memilih 'Rendah') -> diprediksi Dampak Berat (>= 80%) & naik ke 'Darurat'
        $severeStory = 'kak aku mau cerita, udah beberapa hari ini tiap mau berangkat sekolah perutku rasanya mules terus. soalnya ada anak kelas 9 yg suka nungguin aku di tangga belakang buat minta uang. pernah aku nolak terus didorong sampe jatuh. sekarang kalo denger suara mereka aja tanganku suka gemeter. kemarin pas ulangan aku susah konsen karena kepikiran mereka bakal nungguin lagi pas pulang';
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Rendah',
            'story' => $severeStory,
        ])->assertOk();

        $severeReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('selesai', $severeReport->ml_status);
        $this->assertSame(1, $severeReport->ml_label);
        $this->assertSame('linear-svm-bullying-v2-hybrid', $severeReport->ml_model_version);
        $this->assertGreaterThanOrEqual(0.95, (float) $severeReport->ml_confidence);
        $this->assertSame('Darurat', $severeReport->urgency);
        Mail::assertQueued(EmergencyReportAlertMail::class, fn (EmergencyReportAlertMail $m) => $m->hasTo($bk->email));

        // 1b. Kasus krisis kalimat pendek/OOV (kekerasan seksual berat, KSBE/sextortion, luka fisik, self-harm, ancaman mematikan, senjata) -> WAJIB Darurat
        $crisisCases = [
            'Aku diperkosa dipukuli setiap hari sampai aku hamil',
            'aku kemarin dipukulin sampe berdarah',
            'aku mau bunuh diri tidak kuat lagi',
            'aku diancam pakai pisau dan dipalak tiap hari',
            'aku dicekik dan ditendang sampai memar di toilet',
            'kelamin saya dipegang paksa dan difoto tanpa baju',
            'Permisi ibu, saya mau curhat kemarin waktu saya jadi cheerleader saya melihat ada satu cowok inisial Jordan yang dari kemarin memotret area sensitif saya. Kemudian, diupload di status ig untuk bahan teman temannya. Saya trauma dengan hal tersebut dan saya ingin balas dendam dengan cara memotong nadinya Jordan.',
            'Saya membuka HP anak saya dan menemukan salah satu temannya melakukan ancaman melalui Whatsapp. Untuk melakukan foto tidak senonoh. Ancaman tersebut diikuti dengan teror, sampai anak saya trauma dan tidak ingin berangkat ke sekolah.',
        ];
        foreach ($crisisCases as $crisisText) {
            $crisisRes = BullyingNlpClassifier::classify($crisisText);
            $this->assertSame(1, $crisisRes['label'], "Gagal label berat untuk: {$crisisText}");
            $this->assertSame('Darurat', $crisisRes['predicted_urgency'], "Gagal Darurat untuk: {$crisisText}");
        }

        // 1c. Pengiriman laporan kekerasan seksual berat melalui /s/lapor otomatis menjadi Darurat
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Rendah',
            'story' => 'Aku diperkosa dipukuli setiap hari sampai aku hamil',
        ])->assertOk();
        $sexualCrisisReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('Darurat', $sexualCrisisReport->urgency);
        $this->assertSame(1, $sexualCrisisReport->ml_label);

        // 1d. Kasus kekerasan tunggal/pemalakan/verbal berat sampai menangis -> Sedang; kalimat penyangkalan/ringan -> tetap Rendah
        $moderateRes = BullyingNlpClassifier::classify('tadi uang jajanku dipalak kakak kelas di kantin');
        $this->assertSame('Sedang', $moderateRes['predicted_urgency']);

        $severeVerbalRes = BullyingNlpClassifier::classify('Kemarin saya melihat Jordan ngejekin denny dan saya mendengar Jordan mengejek "DASAR MISKIN, BUSUNG LAPAR, GONORE, SIFILIS, HIV, GILA KAMU. RUMAH KARDUS, FAKIR, BODOH, GAGAR OTAK". Denny sampai menangis');
        $this->assertSame(1, $severeVerbalRes['label']);
        $this->assertSame('Sedang', $severeVerbalRes['predicted_urgency']);

        $negatedMildRes = BullyingNlpClassifier::classify('aku tidak dipukul sih, tadi cuma diejek sepatuku jelek tapi yaudah biasa aja');
        $this->assertSame('Rendah', $negatedMildRes['predicted_urgency']);

        // 2. Cerita ringan -> diprediksi Dampak Ringan & urgensi 'Rendah'
        $mildStory = 'Dicuekin waktu pertama kali kenalan, tapi masih bisa main dan belajar biasa.';
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Sedang',
            'story' => $mildStory,
        ])->assertOk();

        $mildReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('selesai', $mildReport->ml_status);
        $this->assertSame(0, $mildReport->ml_label);
        $this->assertSame('Rendah', $mildReport->urgency);

        // 3. Cerita yang sejak awal ditandai 'Darurat' oleh pelapor TIDAK BOLEH diturunkan meski NLP memprediksi Dampak Ringan
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Darurat',
            'story' => 'Dicuekin waktu pertama kali kenalan namun saya merasa sangat terancam.',
        ])->assertOk();

        $daruratReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('selesai', $daruratReport->ml_status);
        $this->assertSame('Darurat', $daruratReport->urgency);

        // 4. Verifikasi mapping probabilitas ke 3 tingkat urgensi (Darurat / Sedang / Rendah)
        $this->assertSame('Darurat', BullyingNlpClassifier::mapProbabilityToUrgency(0.85));
        $this->assertSame('Sedang', BullyingNlpClassifier::mapProbabilityToUrgency(0.62));
        $this->assertSame('Rendah', BullyingNlpClassifier::mapProbabilityToUrgency(0.25));
        $this->assertSame('Darurat', BullyingNlpClassifier::resolveUrgency(0.10, 'Darurat'));

        // 5. Mode HTTP opsional: kegagalan layanan HTTP tidak menyebabkan /s/lapor error 500
        config(['services.ml.driver' => 'http']);
        Http::fake([
            '*/v1/classify' => Http::response(['detail' => 'Service Unavailable'], 500),
        ]);

        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Sedang',
            'story' => 'Laporan tetap tersimpan aman walau layanan ML HTTP sedang gagal.',
        ])->assertOk()->assertJson(['success' => true]);

        $fallbackReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('belum_diklasifikasi', $fallbackReport->ml_status);
        $this->assertSame('Sedang', $fallbackReport->urgency);
    }
}
