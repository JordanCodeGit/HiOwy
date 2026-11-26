<?php

namespace Tests\Feature;

use App\Mail\StaffInvitationMail;
use App\Models\AccessLog;
use App\Models\Forward;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\ReportMessage;
use App\Models\RetentionSummary;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\AcademicYearService;
use App\Support\TicketCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class Batch4UatComplianceTest extends TestCase
{
    use RefreshDatabase;

    private function seedDefaults(): void
    {
        IncidentCategory::firstOrCreate(
            ['label' => 'Perundungan Fisik & Pemukulan'],
            [
                'permendikbud_type' => 'Kekerasan Fisik (Permendikbudristek 46/2023)',
                'active' => true,
                'visible_to_kesiswaan' => true,
                'order_index' => 1,
            ]
        );
        IncidentCategory::firstOrCreate(
            ['label' => 'Masalah Pribadi & Lainnya'],
            [
                'permendikbud_type' => 'Pendampingan Konseling / Lainnya',
                'active' => true,
                'permanent' => true,
                'visible_to_kesiswaan' => false,
                'order_index' => 2,
            ]
        );
        IncidentLocation::firstOrCreate(
            ['label' => 'Ruang Kelas'],
            ['active' => true, 'order_index' => 1]
        );
    }

    public function test_public_form_privacy_notice_nocache_quick_exit_and_school_config_visibility(): void
    {
        $this->seedDefaults();
        SchoolSetting::set('reading_hours', 'Senin-Jumat 07.30-15.00 WIB');
        SchoolSetting::set('emergency_contact', '081299887766');
        SchoolSetting::set('school_website_url', 'https://smptelkompwt.sch.id');

        $res = $this->get('/s/lapor');
        $res->assertOk();

        // TS-042 & TS-043: cache-control no-store prevents back/bfcache restore after Quick Exit
        $cacheControl = (string) $res->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);

        // TS-002: Owy privacy notice present, warns about story, never promises 100% anonim
        $res->assertSee('Pemberitahuan Privasi Sebelum Melapor')
            ->assertSee('isi ceritamu sendiri dapat mengungkap identitasmu')
            ->assertDontSee('100% anonim');

        // TS-003 & TS-045: Jam baca, Kontak Darurat BK, Website Sekolah, Keluar Cepat
        $res->assertSee('Senin-Jumat 07.30-15.00 WIB')
            ->assertSee('081299887766')
            ->assertSee('https://smptelkompwt.sch.id')
            ->assertSee('Keluar Cepat');
    }

    public function test_custom_location_incident_date_and_conditional_contact_validation(): void
    {
        $this->seedDefaults();

        // TS-011: mode ingin dihubungi (is_anonymous=0) WAJIB Nama & Kelas, kontak opsional
        $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan uji tanpa nama saat ingin dihubungi.',
            'is_anonymous' => '0',
            'reporter_name' => '',
            'reporter_class' => '',
        ])->assertStatus(422);

        // TS-007 & TS-008 & TS-011: Lokasi "Lainnya", tanggal kejadian diketahui, tanpa nomor kontak -> 200 OK
        $res1 = $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => '__other__',
            'custom_location' => 'Belakang Kantin Lama',
            'incident_date' => now('Asia/Jakarta')->toDateString(),
            'incident_date_unknown' => '0',
            'incident_time' => 'Siang / Jam Istirahat',
            'story' => 'Dilaporkan terjadi dorongan keras di area contoh.',
            'is_anonymous' => '0',
            'reporter_name' => 'Citra Lestari',
            'reporter_class' => '8B',
            'reporter_contact' => '',
        ]);
        $res1->assertOk()->assertJsonPath('success', true);

        $r1 = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('Lainnya: Belakang Kantin Lama', $r1->location);
        $this->assertNotNull($r1->incident_date);
        $this->assertSame('Citra Lestari', $r1->reporter_name);
        $this->assertSame('8B', $r1->reporter_class);

        // TS-007: tanggal "tidak ingat pasti" -> incident_date null
        $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'incident_date_unknown' => '1',
            'incident_time' => 'Tidak ingat pasti',
            'story' => 'Saya lupa tanggal pastinya namun sering diejek.',
            'is_anonymous' => '1',
        ])->assertOk();

        $r2 = StudentReport::latest('id')->firstOrFail();
        $this->assertNull($r2->incident_date);
    }

    public function test_opsi_a_bk_read_hour_and_whatsapp_checkmarks_in_two_way_chat(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create(['name' => 'Konselor Utama']);

        $submit = $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Saya butuh bantuan konsultasi BK mengenai teman.',
            'is_anonymous' => '1',
        ])->assertOk();
        $code = $submit->json('ticket_code');
        $report = StudentReport::latest('id')->firstOrFail();

        // Sebelum dibaca BK: bk_read_at null
        $trackUnread = $this->getJson('/s/lapor/track?ticket_code='.$code)->assertOk();
        $this->assertNull($trackUnread->json('ticket.bk_read_time'));

        // Pelapor mengirim pesan lanjutan -> pesan pelapor TIDAK boleh menyimpan jam kirim H:i, read_at masih null (centang 1)
        $this->postJson('/s/lapor/track/reply', [
            'ticket_code' => $code,
            'body' => 'Pesan tambahan dari pelapor tanpa jam kirim.',
        ])->assertOk()
            ->assertJsonPath('sent.time', null)
            ->assertJsonPath('sent.is_read', false);

        // BK membuka laporan -> bk_read_at tercatat & pesan pelapor menjadi terbaca (centang 2)
        $this->actingAs($bk)->get('/bk/reports/'.$report->id)->assertOk();
        $report->refresh();
        $this->assertNotNull($report->bk_read_at);
        $reporterMsg = ReportMessage::where('student_report_id', $report->id)->where('sender', 'reporter')->firstOrFail();
        $this->assertNotNull($reporterMsg->read_at);

        // BK membalas -> menyimpan bk_sent_time (H:i), read_at masih null sebelum pelapor melacak
        $this->actingAs($bk)->postJson("/bk/reports/{$report->id}/reply", [
            'counselor_reply' => 'Silakan datang ke ruang BK besok pagi ya.',
        ])->assertOk();

        $bkMsg = ReportMessage::where('student_report_id', $report->id)->where('sender', 'bk')->firstOrFail();
        $this->assertNotEmpty($bkMsg->bk_sent_time);
        $this->assertNull($bkMsg->read_at);

        // Pelapor membuka Lacak Aduan -> tampil bk_read_time, jam pesan BK, dan menandai pesan BK dibaca (centang 2)
        $trackAfterBk = $this->getJson('/s/lapor/track?ticket_code='.$code)->assertOk();
        $this->assertNotNull($trackAfterBk->json('ticket.bk_read_time'));
        $messages = $trackAfterBk->json('ticket.messages');
        $this->assertCount(2, $messages);
        $this->assertNull($messages[0]['time']);
        $this->assertTrue($messages[0]['is_read']);
        $this->assertNotEmpty($messages[1]['time']);
        $this->assertTrue($messages[1]['is_read']);

        $this->assertNotNull($bkMsg->fresh()->read_at);
    }

    public function test_training_route_latihan_is_isolated_from_production_and_alerts(): void
    {
        Mail::fake();
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();

        $this->get('/latihan')->assertOk()->assertSee('Mode Latihan');

        $res = $this->postJson('/latihan', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'urgency' => 'Darurat',
            'story' => 'Simulasi melalui route khusus latihan.',
            'is_anonymous' => '1',
        ]);
        $res->assertOk()->assertJsonPath('is_training', true);

        Mail::assertNothingQueued();

        $report = StudentReport::latest('id')->firstOrFail();
        $this->assertTrue($report->is_training);

        $this->actingAs($bk)->get('/bk')->assertOk()->assertDontSee('#'.$report->case_label);
        $this->actingAs($bk)->get('/bk?mode=latihan')->assertOk()->assertSee('#'.$report->case_label);
    }

    public function test_kesiswaan_can_write_separate_encrypted_notes_and_walas_and_archive_bugs_are_fixed(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create(['name' => 'Waka Kesiswaan']);
        $walas = User::factory()->walas()->create(['name' => 'Wali Kelas 8A']);

        $report = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash($plainCode = TicketCodeService::generateCode(10)),
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan untuk catatan kesiswaan dan uji arsip.',
            'internal_notes' => 'Catatan rahasia BK saja.',
            'urgency' => 'Sedang',
            'status' => 'dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // TS-035: Kesiswaan menulis catatan terpisah (internal_notes BK tidak bocor)
        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}")
            ->assertOk()
            ->assertDontSee('Catatan rahasia BK saja.');

        $this->actingAs($kesiswaan)->post("/kesiswaan/reports/{$report->id}/note", [
            'note' => 'Koordinasi pembinaan ketertiban kelas.',
        ])->assertRedirect();

        $report->refresh();
        $this->assertStringContainsString('Koordinasi pembinaan ketertiban kelas.', (string) $report->kesiswaan_notes);
        $this->assertStringNotContainsString('Koordinasi pembinaan ketertiban kelas.', (string) $report->internal_notes);

        // Pelapor TIDAK pernah melihat catatan Kesiswaan
        $track = $this->getJson('/s/lapor/track?ticket_code='.$plainCode)->assertOk();
        $this->assertStringNotContainsString('Koordinasi pembinaan', $track->getContent());

        // Bug 1: /bk/archive wajib memiliki data-id="{id}" pada <tr> untuk Select All
        $report->update(['archived_at' => now(), 'archive_reason' => 'selesai', 'status' => 'selesai']);
        $this->actingAs($bk)->get('/bk/archive')
            ->assertOk()
            ->assertSee('data-id="'.$report->id.'"', false);

        // Bug 2: /wali-kelas hanya memiliki 1 kolom search (#summary-search), tanpa search bar topbar ganda
        $walasRes = $this->actingAs($walas)->get('/wali-kelas')->assertOk();
        $walasRes->assertSee('summary-search')
            ->assertDontSee('placeholder="Pencarian cepat..."', false);
    }

    public function test_staff_deactivation_reset_access_and_reset_password_min_10_text(): void
    {
        Mail::fake();
        $bk = User::factory()->bk()->create(['email' => 'koordinator@sekolah.sch.id']);
        $targetUser = User::factory()->kesiswaan()->create(['email' => 'staff.uji@sekolah.sch.id']);
        $staff = StaffMember::create([
            'name' => 'Staf Uji',
            'email' => 'staff.uji@sekolah.sch.id',
            'role' => 'kesiswaan',
            'status' => 'aktif',
            'invited_at' => now(),
        ]);

        // TS-047: UI reset password menampilkan Minimal 10 karakter
        $resetToken = Password::broker()->createToken($targetUser);
        $this->get("/reset-password/{$resetToken}?email=staff.uji@sekolah.sch.id")
            ->assertOk()
            ->assertSee('Minimal 10 karakter')
            ->assertDontSee('Minimal 8 karakter');

        // TS-025: Reset Akses menghapus 2FA dan mengirim ulang undangan
        $this->actingAs($bk)->postJson("/bk/manage/staff/{$staff->id}/reset-access")
            ->assertOk()
            ->assertJsonPath('success', true);
        $this->assertNull($targetUser->fresh()->two_factor_confirmed_at);
        $this->assertSame('pending', $staff->fresh()->status);
        Mail::assertSent(StaffInvitationMail::class);

        // TS-025: Menonaktifkan staf memblokir akses login & portal
        $this->actingAs($bk)->putJson("/bk/manage/staff/{$staff->id}", [
            'name' => 'Staf Uji',
            'email' => 'staff.uji@sekolah.sch.id',
            'role' => 'kesiswaan',
            'status' => 'nonaktif',
        ])->assertOk();

        $this->actingAs($targetUser->fresh())->get('/kesiswaan')->assertRedirect(route('login'));

        $this->post('/login', [
            'email' => 'staff.uji@sekolah.sch.id',
            'password' => 'StrongPass#2026',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_statistics_mask_small_groups_export_csv_pdf_retention_and_server_delete(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();

        // Buat 2 laporan selesai/diarsipkan + 1 laporan masih terbuka, + 5 laporan untuk kategori besar (total 8)
        for ($i = 1; $i <= 5; $i++) {
            StudentReport::create([
                'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
                'category' => 'Perundungan Fisik & Pemukulan',
                'location' => 'Ruang Kelas',
                'reporter_role' => 'Siswa (Korban Langsung)',
                'urgency' => 'Sedang',
                'story' => "Laporan kelima ke-{$i} bersifat rahasia sekali.",
                'status' => 'ditindaklanjuti',
                'bk_read_at' => now('Asia/Jakarta'),
                'submitted_date' => now('Asia/Jakarta')->toDateString(),
                'submitted_daypart' => 'pagi',
                'status_updated_date' => now('Asia/Jakarta')->toDateString(),
            ]);
        }

        $archivedSmall = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Masalah Pribadi & Lainnya',
            'location' => 'Kantin',
            'reporter_role' => 'Orang Tua / Wali Siswa',
            'urgency' => 'Darurat',
            'story' => 'Cerita sensitif arsip yang akan diretensi.',
            'status' => 'selesai',
            'archived_at' => now(),
            'archive_reason' => 'selesai',
            'bk_read_at' => now('Asia/Jakarta'),
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // TS-053 & TS-054: Statistik menyamarkan angka 1-4 ("kurang dari 5") dan menampilkan angka >= 5 (6)
        $statsPage = $this->actingAs($bk)->get('/bk/statistics');
        $statsPage->assertOk()
            ->assertSee('kurang dari 5')
            ->assertSee('Menurut Peran Pelapor')
            ->assertSee('Menurut Tempat Kejadian')
            ->assertSee('Menurut Tingkat Urgensi')
            ->assertDontSee('bersifat rahasia sekali');

        // TS-057: Ekspor CSV & PDF tanpa memuat isi cerita
        $csv = $this->actingAs($bk)->get('/bk/statistics/export/csv');
        $csv->assertOk();
        $csvContent = $csv->streamedContent();
        $this->assertStringContainsString('kurang dari 5', $csvContent);
        $this->assertStringNotContainsString('bersifat rahasia sekali', $csvContent);

        $this->actingAs($bk)->get('/bk/statistics/export/pdf')
            ->assertOk()
            ->assertSee('kurang dari 5')
            ->assertDontSee('bersifat rahasia sekali');

        // TS-038: Halaman Pelajari menyediakan materi pengertian, bentuk, korban, saksi, teman, orang tua
        $this->get('/s/pelajari')
            ->assertOk()
            ->assertSee('Pengertian')
            ->assertSee('Bagi Korban')
            ->assertSee('Saksi')
            ->assertSee('Orang Tua');

        // TS-056 FR-65: Retensi menghapus & meringkas laporan arsip/selesai sambil mempertahankan laporan terbuka
        SchoolSetting::set('retention_date', now('Asia/Jakarta')->toDateString());
        $this->artisan('hiowy:run-retention')->assertExitCode(0);

        $this->assertNull(StudentReport::find($archivedSmall->id));
        $this->assertSame(5, StudentReport::active()->count());
        $this->assertSame(1, RetentionSummary::count());

        // TS-041 FR-64: Penghapusan manual server butuh --approval dan --reason
        $openTarget = StudentReport::firstOrFail();
        $this->artisan("hiowy:delete-report {$openTarget->id}")->assertExitCode(1);
        $this->assertNotNull(StudentReport::find($openTarget->id));

        $this->artisan("hiowy:delete-report {$openTarget->id} --approval=SK-KEPSEK-01 --reason=Uji_Hapus")
            ->assertExitCode(0);
        $this->assertNull(StudentReport::find($openTarget->id));
        $this->assertTrue(AccessLog::where('action', 'server_delete_report')->exists());
    }

    public function test_bk_and_kesiswaan_can_unarchive_reports_with_audit_log_and_role_isolation(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create();

        $rVisible = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan terarsip yang dapat dipulihkan.',
            'urgency' => 'Sedang',
            'status' => 'selesai',
            'archived_at' => now(),
            'archive_reason' => 'iseng',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $rPrivate = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Masalah Pribadi & Lainnya',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan privat BK yang terarsip.',
            'urgency' => 'Sedang',
            'status' => 'selesai',
            'archived_at' => now(),
            'archive_reason' => 'selesai',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // Kesiswaan TIDAK boleh unarchive laporan privat BK
        $this->actingAs($kesiswaan)->post("/kesiswaan/reports/{$rPrivate->id}/unarchive")->assertForbidden();
        $this->assertNotNull($rPrivate->fresh()->archived_at);

        // Kesiswaan BOLEH unarchive laporan dalam kategori yang diizinkan
        $this->actingAs($kesiswaan)->post("/kesiswaan/reports/{$rVisible->id}/unarchive")->assertRedirect();
        $this->assertNull($rVisible->fresh()->archived_at);
        $this->assertNull($rVisible->fresh()->archive_reason);
        $this->assertSame('dibaca', $rVisible->fresh()->status);

        // Arsipkan lagi lalu uji bulk unarchive BK
        $rVisible->refresh()->update(['archived_at' => now(), 'archive_reason' => 'selesai']);
        $this->actingAs($bk)->postJson('/bk/reports/bulk-unarchive', [
            'ids' => [$rVisible->id, $rPrivate->id],
        ])->assertOk()->assertJsonPath('count', 2);

        $this->assertNull($rVisible->fresh()->archived_at);
        $this->assertNull($rPrivate->fresh()->archived_at);
        $this->assertGreaterThanOrEqual(3, AccessLog::where('action', 'unarchive_report')->count());
    }

    public function test_revision_batch_timezone_categories_schedule_poster_back_and_wizard_ui(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create();
        $walas = User::factory()->walas()->create();

        // 1. Timezone WIB konsisten (tidak dobel +7 jam saat BK membaca laporan)
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $submit = $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Uji akurasi jam baca BK pada tab Lacak Aduan Saya.',
            'is_anonymous' => '1',
        ])->assertOk();
        $code = $submit->json('ticket_code');
        $report = StudentReport::latest('id')->firstOrFail();

        $expectedHourMin = now('Asia/Jakarta')->format('H:i');
        $this->actingAs($bk)->get("/bk/reports/{$report->id}")->assertOk();
        $track = $this->getJson('/s/lapor/track?ticket_code='.$code)->assertOk();
        $this->assertStringContainsString($expectedHourMin.' WIB', (string) $track->json('ticket.bk_read_time'));

        // 2. Kategori otomatis aktif & otomatis tampil ke Kesiswaan kecuali Masalah Pribadi & Teman Cerita
        $normalCatRes = $this->actingAs($bk)->postJson('/bk/manage/categories', [
            'label' => 'Pemerasan di Lingkungan Sekolah',
            'permendikbud_type' => 'Kekerasan Psikis (Permendikbudristek 46/2023)',
        ])->assertOk();
        $this->assertTrue((bool) $normalCatRes->json('category.active'));
        $this->assertTrue((bool) $normalCatRes->json('category.visible_to_kesiswaan'));

        $privateCatRes = $this->actingAs($bk)->postJson('/bk/manage/categories', [
            'label' => 'Teman Cerita Pribadi',
        ])->assertOk();
        $this->assertTrue((bool) $privateCatRes->json('category.active'));
        $this->assertFalse((bool) $privateCatRes->json('category.visible_to_kesiswaan'));

        $rNormal = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Pemerasan di Lingkungan Sekolah',
            'location' => 'Kantin Sekolah',
            'story' => 'Laporan kategori baru langsung masuk ke Kesiswaan.',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);
        $rCurhat = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Teman Cerita Pribadi',
            'location' => 'Ruang Kelas',
            'story' => 'Cerita pribadi yang khusus konselor BK.',
            'urgency' => 'Rendah',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$rNormal->id}")->assertOk();
        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$rCurhat->id}")->assertForbidden();

        // 3. Pengaturan jam baca BK dibagi 2 rentang (hari & jam) + redirect ke #sekolah
        $this->actingAs($bk)->post('/bk/manage/settings', [
            'school_name' => 'SMP Telkom Purwokerto',
            'reading_day_start' => 'Senin',
            'reading_day_end' => 'Sabtu',
            'reading_time_start' => '07:30',
            'reading_time_end' => '14:30',
        ])->assertRedirect(route('bk.manage').'#sekolah');
        $this->assertSame('Senin–Sabtu, 07.30–14.30 WIB', SchoolSetting::get('reading_hours'));

        // 4. Tombol Kembali pada halaman cetak poster tidak 403 untuk BK maupun Wali Kelas
        $this->actingAs($bk)->get('/bk/manage/poster')
            ->assertOk()
            ->assertSee(route('bk.manage').'#qr', false)
            ->assertSee('Kembali ke Kelola Sekolah');

        $this->actingAs($walas)->get('/wali-kelas/poster')
            ->assertOk()
            ->assertSee(route('walas.dashboard'), false)
            ->assertSee('Kembali ke Dashboard Wali Kelas')
            ->assertDontSee('Kembali ke Kelola Sekolah');

        // 5. Halaman /bk/manage & /s/lapor memuat revisi UI & bebas tulisan FR-xx
        $manageHtml = $this->actingAs($bk)->get('/bk/manage')->assertOk();
        $manageHtml->assertSee('favicon-owy.png')
            ->assertSee('Poster Panduan Pelaporan Siswa')
            ->assertSee('modal-hapus-kategori')
            ->assertSee('reading_day_start')
            ->assertSee('reading_time_start')
            ->assertSee('Unggah Logo')
            ->assertDontSee('Terlihat Tim Kesiswaan')
            ->assertDontSee('>Urutan<', false)
            ->assertDontSee('FR-');

        $publicHtml = $this->get('/s/lapor')->assertOk();
        $publicHtml->assertSee('favicon-owy.png')
            ->assertSee('Langkah 1 dari 8')
            ->assertSee('maskot_owy')
            ->assertSee('hiowy_reporter_state_v1:')
            ->assertSee(':disabled="!ticketCopied || !ticketPasswordSaved"', false)
            ->assertSee('Lainnya (isi sendiri)')
            ->assertDontSee('FR-20')
            ->assertDontSee('FR-');
    }

    public function test_custom_incident_time_option_on_step_5(): void
    {
        $this->seedDefaults();

        $this->get('/s/lapor')
            ->assertOk()
            ->assertSee('Lainnya (isi sendiri)')
            ->assertSee('custom-incident-time-input');

        // Memilih opsi Lainnya tanpa mengisi teks kustom harus ditolak 422
        $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'incident_time' => '__other__',
            'custom_incident_time' => '',
            'story' => 'Uji validasi waktu kejadian lainnya kosong.',
            'is_anonymous' => '1',
        ])->assertStatus(422);

        // Memilih opsi Lainnya dengan teks kustom tersimpan sebagai "Lainnya: ..."
        $res = $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'incident_time' => '__other__',
            'custom_incident_time' => 'Saat pulang ekskul basket',
            'story' => 'Uji penyimpanan waktu kejadian kustom dari siswa.',
            'is_anonymous' => '1',
        ])->assertOk()->assertJsonPath('success', true);

        $report = StudentReport::latest('id')->firstOrFail();
        $this->assertSame('Lainnya: Saat pulang ekskul basket', $report->incident_time);
    }

    public function test_private_ticket_password_setup_tracking_reply_and_legacy_compatibility(): void
    {
        $this->seedDefaults();

        $submit = $this->postJson('/s/lapor', [
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan untuk uji perlindungan password tiket private.',
            'is_anonymous' => '1',
        ])->assertOk();
        $code = (string) $submit->json('ticket_code');
        $report = StudentReport::latest('id')->firstOrFail();

        // Password < 4 karakter atau konfirmasi tidak cocok ditolak 422
        $this->postJson('/s/lapor/ticket-password', [
            'ticket_code' => $code,
            'ticket_password' => '123',
            'ticket_password_confirmation' => '123',
        ])->assertStatus(422);

        $this->postJson('/s/lapor/ticket-password', [
            'ticket_code' => $code,
            'ticket_password' => 'rahasiaku',
            'ticket_password_confirmation' => 'berbeda123',
        ])->assertStatus(422);

        // Password valid >= 4 karakter berhasil disimpan sebagai hash & tersembunyi
        $this->postJson('/s/lapor/ticket-password', [
            'ticket_code' => $code,
            'ticket_password' => 'owy2026',
            'ticket_password_confirmation' => 'owy2026',
        ])->assertOk()->assertJsonPath('success', true);

        $report->refresh();
        $this->assertNotNull($report->ticket_password_hash);
        $this->assertNotSame('owy2026', $report->ticket_password_hash);
        $this->assertTrue(Hash::check('owy2026', $report->ticket_password_hash));
        $this->assertArrayNotHasKey('ticket_password_hash', $report->toArray());

        // Tidak boleh menimpa password tiket yang sudah diatur (409)
        $this->postJson('/s/lapor/ticket-password', [
            'ticket_code' => $code,
            'ticket_password' => 'hack9999',
            'ticket_password_confirmation' => 'hack9999',
        ])->assertStatus(409);

        // Melacak tanpa password atau dengan password salah ditolak 401
        $this->postJson('/s/lapor/track', [
            'ticket_code' => $code,
            'ticket_password' => '',
        ])->assertStatus(401)->assertJsonPath('password_required', true);

        $this->postJson('/s/lapor/track', [
            'ticket_code' => $code,
            'ticket_password' => 'salah123',
        ])->assertStatus(401)->assertJsonPath('password_required', true);

        // Melacak dengan password benar berhasil & tidak membocorkan hash
        $trackOk = $this->postJson('/s/lapor/track', [
            'ticket_code' => $code,
            'ticket_password' => 'owy2026',
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertStringNotContainsString('ticket_password_hash', $trackOk->getContent());

        // Membalas pesan wajib password yang benar
        $this->postJson('/s/lapor/track/reply', [
            'ticket_code' => $code,
            'ticket_password' => 'salah123',
            'body' => 'Balasan dengan password salah.',
        ])->assertStatus(401);

        $this->postJson('/s/lapor/track/reply', [
            'ticket_code' => $code,
            'ticket_password' => 'owy2026',
            'body' => 'Balasan sah dengan password benar.',
        ])->assertOk()->assertJsonPath('success', true);

        // Tiket lama tanpa ticket_password_hash tetap bisa dilacak & dibalas tanpa password
        $legacyCode = TicketCodeService::generateCode(10);
        StudentReport::create([
            'ticket_hash' => TicketCodeService::hash($legacyCode),
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Tiket lama sebelum fitur password private.',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $this->postJson('/s/lapor/track', [
            'ticket_code' => $legacyCode,
            'ticket_password' => '',
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_academic_year_dropdown_switching_and_filtering_for_bk_kesiswaan_and_walas(): void
    {
        $this->seedDefaults();
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create();
        $walas = User::factory()->walas()->create();

        $reportOld = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan tahun ajaran lama 2024/2025.',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => '2024-10-15',
            'submitted_daypart' => 'pagi',
            'status_updated_date' => '2024-10-15',
        ]);

        $reportCurrent = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Perundungan Fisik & Pemukulan',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan tahun ajaran berjalan sekarang.',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        Forward::create([
            'student_report_id' => $reportOld->id,
            'from_user_id' => $bk->id,
            'to_user_id' => $walas->id,
            'summary' => 'Ringkasan pendampingan TA 2024/2025.',
            'forwarded_at' => '2024-10-16 09:00:00',
        ]);

        Forward::create([
            'student_report_id' => $reportCurrent->id,
            'from_user_id' => $bk->id,
            'to_user_id' => $walas->id,
            'summary' => 'Ringkasan pendampingan TA berjalan.',
            'forwarded_at' => now('Asia/Jakarta'),
        ]);

        $currentAy = AcademicYearService::current();

        // 1. Default BK menampilkan tahun ajaran berjalan (tidak menampilkan TA 2024/2025) & memiliki dropdown header
        $this->actingAs($bk)->get('/bk')
            ->assertOk()
            ->assertSee('academicYearBtn')
            ->assertSee('Tahun Ajaran '.$currentAy)
            ->assertSee('#'.$reportCurrent->case_label)
            ->assertDontSee('#'.$reportOld->case_label);

        // 2. BK memilih Tahun Ajaran 2024/2025 melalui dropdown -> hanya laporan 2024/2025 tampil
        $this->actingAs($bk)->post('/staff/academic-year', ['academic_year' => '2024/2025'])
            ->assertRedirect();
        $this->actingAs($bk)->get('/bk')
            ->assertOk()
            ->assertSee('Tahun Ajaran 2024/2025')
            ->assertSee('#'.$reportOld->case_label)
            ->assertDontSee('#'.$reportCurrent->case_label);

        // 3. BK memilih "Semua Tahun Ajaran" -> kedua laporan tampil
        $this->actingAs($bk)->post('/staff/academic-year', ['academic_year' => AcademicYearService::ALL])
            ->assertRedirect();
        $this->actingAs($bk)->get('/bk')
            ->assertOk()
            ->assertSee('Semua Tahun Ajaran')
            ->assertSee('#'.$reportOld->case_label)
            ->assertSee('#'.$reportCurrent->case_label);

        // 4. Kesiswaan dapat mengganti tahun ajaran dari header dan data ikut tersaring
        $this->actingAs($kesiswaan)->post('/staff/academic-year', ['academic_year' => '2024/2025'])
            ->assertRedirect();
        $this->actingAs($kesiswaan)->get('/kesiswaan')
            ->assertOk()
            ->assertSee('academicYearBtn')
            ->assertSee('#'.$reportOld->case_label)
            ->assertDontSee('#'.$reportCurrent->case_label);

        // 5. Wali Kelas dapat mengganti tahun ajaran dari header dan ringkasan ikut tersaring
        $this->actingAs($walas)->post('/staff/academic-year', ['academic_year' => '2024/2025'])
            ->assertRedirect();
        $this->actingAs($walas)->get('/wali-kelas')
            ->assertOk()
            ->assertSee('academicYearBtn')
            ->assertSee('Ringkasan pendampingan TA 2024/2025.')
            ->assertDontSee('Ringkasan pendampingan TA berjalan.');

        // 6. Format tahun ajaran tidak valid ditolak
        $this->actingAs($bk)->post('/staff/academic-year', ['academic_year' => 'invalid-2099'])
            ->assertSessionHasErrors('academic_year');
    }
}
