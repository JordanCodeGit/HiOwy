<?php

namespace Tests\Feature;

use App\Jobs\ClassifyStudentReportJob;
use App\Mail\EmergencyReportAlertMail;
use App\Models\AccessLog;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\SchoolSetting;
use App\Models\StudentReport;
use App\Models\User;
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

    public function test_password_min_10_chars_login_lockout_after_5_failures_and_30min_session(): void
    {
        $this->assertSame(30, (int) config('session.lifetime'));

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

        // Aktifkan training_mode & nlp_enabled lewat panel Kelola BK
        $this->actingAs($bk)->postJson('/bk/manage/settings', [
            'training_mode' => true,
            'nlp_enabled' => true,
        ])->assertOk()->assertJson([
            'training_mode' => true,
            'nlp_enabled' => true,
        ]);

        $this->assertSame('1', SchoolSetting::get('training_mode'));
        $this->assertTrue(AccessLog::where('action', 'toggle_training_mode')->exists());

        // Kirim aduan saat Mode Latihan aktif (bahkan urgensi Darurat)
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Darurat',
            'story' => 'Simulasi pelatihan guru: laporan uji coba.',
        ])->assertOk()->assertJson(['is_training' => true]);

        Mail::assertNothingQueued();
        Queue::assertNothingPushed();

        $report = StudentReport::firstOrFail();
        $this->assertTrue($report->is_training);

        // Tidak muncul pada dashboard asli BK maupun Kesiswaan
        $this->actingAs($bk)->get('/bk')->assertOk()->assertDontSee('#'.$report->case_label)->assertDontSee('Simulasi pelatihan guru');
        $this->actingAs($bk)->get('/bk/statistics')->assertOk()->assertSee('Total 0 Kasus');
        $this->actingAs($kesiswaan)->get('/kesiswaan')->assertOk()->assertDontSee('#'.$report->case_label)->assertDontSee('Simulasi pelatihan guru');
        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}")->assertForbidden();

        // Muncul khusus pada /bk?mode=latihan
        $this->actingAs($bk)->get('/bk?mode=latihan')->assertOk()->assertSee('#'.$report->case_label);
    }

    public function test_ml_job_only_dispatched_when_nlp_enabled_and_preserves_darurat_on_success_or_failure(): void
    {
        $this->seedCategoryAndLocation();

        // Default: NLP nonaktif -> job tidak dijalankan
        $this->assertSame('0', SchoolSetting::get('nlp_enabled', '0'));

        Queue::fake();
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Sedang',
            'story' => 'Laporan saat modul NLP belum diaktifkan.',
        ])->assertOk();

        Queue::assertNothingPushed();

        // Aktifkan NLP -> job ML didispatch ke antrean
        SchoolSetting::set('nlp_enabled', '1');
        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Darurat',
            'story' => 'Saya terus didorong hingga terjatuh di koridor.',
        ])->assertOk();

        Queue::assertPushed(ClassifyStudentReportJob::class);

        $daruratReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('belum_diklasifikasi', $daruratReport->ml_status);

        // Sukses ML dengan label=0 (ringan) TIDAK BOLEH menurunkan urgensi Darurat (FR-46)
        Http::fake([
            '*/v1/classify' => Http::response([
                'label' => 0,
                'confidence' => 0.875,
                'model_version' => 'indobert-v1.0',
            ], 200),
        ]);

        (new ClassifyStudentReportJob($daruratReport))->handle();
        $daruratReport->refresh();

        $this->assertSame(0, $daruratReport->ml_label);
        $this->assertSame(0.875, (float) $daruratReport->ml_confidence);
        $this->assertSame('indobert-v1.0', $daruratReport->ml_model_version);
        $this->assertSame('selesai', $daruratReport->ml_status);
        $this->assertSame('Darurat', $daruratReport->urgency);

        // Kegagalan layanan ML: laporan tetap "belum_diklasifikasi" dan /s/lapor tidak error 500
        Queue::swap(app('queue'));
        Http::fake([
            '*/v1/classify' => Http::response(['detail' => 'Service Unavailable'], 500),
        ]);

        $this->postJson('/s/lapor', [
            'category' => 'Bullying Fisik',
            'location' => 'Koridor Kelas',
            'urgency' => 'Sedang',
            'story' => 'Laporan tetap tersimpan aman walau layanan ML sedang gagal.',
        ])->assertOk()->assertJson(['success' => true]);

        $fallbackReport = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('belum_diklasifikasi', $fallbackReport->ml_status);
        $this->assertNull($fallbackReport->ml_label);
    }
}
