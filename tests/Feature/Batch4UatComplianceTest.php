<?php

namespace Tests\Feature;

use App\Mail\StaffInvitationMail;
use App\Models\AccessLog;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\ReportMessage;
use App\Models\RetentionSummary;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\TicketCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
