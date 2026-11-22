<?php

namespace Tests\Feature;

use App\Models\Forward;
use App\Models\IncidentCategory;
use App\Models\ReportMessage;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\TicketCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Batch2M4Test extends TestCase
{
    use RefreshDatabase;

    public function test_two_way_report_messages_between_bk_and_reporter_are_encrypted_and_isolated(): void
    {
        $bk = User::factory()->bk()->create(['name' => 'Konselor A']);
        $kesiswaan = User::factory()->kesiswaan()->create(['name' => 'Staf Kesiswaan']);
        $walas = User::factory()->walas()->create(['name' => 'Wali Kelas 8A']);

        IncidentCategory::create([
            'label' => 'Bullying Fisik',
            'active' => true,
            'visible_to_kesiswaan' => true,
            'order_index' => 1,
        ]);

        $plainCode = TicketCodeService::generateCode(10);
        $report = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash($plainCode),
            'category' => 'Bullying Fisik',
            'location' => 'Kantin',
            'urgency' => 'Sedang',
            'reporter_role' => 'Siswa',
            'incident_time' => 'Istirahat',
            'story' => 'Laporan kejadian fisik di dekat kantin.',
            'is_anonymous' => false,
            'reporter_name' => 'Andi Rahasia',
            'reporter_class' => '8A',
            'reporter_contact' => '081122334455',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // 1. BK membalas via /bk/reports/{id}/reply
        $this->actingAs($bk)
            ->postJson("/bk/reports/{$report->id}/reply", ['counselor_reply' => 'Halo, Guru BK siap membantu.'])
            ->assertOk()
            ->assertJson(['success' => true]);

        // 2. Pastikan isi pesan di DB benar-benar terenkripsi
        $rawBody = DB::table('report_messages')->where('student_report_id', $report->id)->value('body');
        $this->assertNotEquals('Halo, Guru BK siap membantu.', $rawBody);

        // 3. Pelapor membalas via /s/lapor/track/reply memakai kode tiket asli
        $this->postJson('/s/lapor/track/reply', [
            'ticket_code' => $plainCode,
            'body' => 'Baik bu, saya ingin konsultasi besok.',
        ])->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'messages');

        // 4. Endpoint tracking publik hanya mengembalikan status & pesan (tanpa urgency/notes/hash)
        $trackRes = $this->getJson('/s/lapor/track?ticket_code='.$plainCode)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'ticket.messages');
        $ticketPayload = $trackRes->json('ticket');
        $this->assertArrayNotHasKey('urgency', $ticketPayload);
        $this->assertArrayNotHasKey('notes', $ticketPayload);
        $this->assertArrayNotHasKey('ticket_hash', $ticketPayload);

        // 5. Kesiswaan & Walas tidak bisa membalas ke rute BK maupun melihat pesan BK<->Pelapor
        $this->actingAs($kesiswaan)
            ->postJson("/bk/reports/{$report->id}/reply", ['reply' => 'Ilegal'])
            ->assertForbidden();
        $this->actingAs($walas)
            ->postJson("/bk/reports/{$report->id}/reply", ['reply' => 'Ilegal'])
            ->assertForbidden();

        $kesiswaanDetail = $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}");
        $kesiswaanDetail->assertOk();
        $kesiswaanDetail->assertDontSee('Halo, Guru BK siap membantu.');
        $kesiswaanDetail->assertDontSee('Baik bu, saya ingin konsultasi besok.');
        $kesiswaanDetail->assertDontSee('Andi Rahasia');
        $kesiswaanDetail->assertDontSee('081122334455');

        $this->assertSame(2, ReportMessage::where('student_report_id', $report->id)->count());
    }

    public function test_srs_status_transitions_archive_and_statistics_pages(): void
    {
        $bk = User::factory()->bk()->create();

        $r1 = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Verbal',
            'location' => 'Koridor Kelas',
            'story' => 'Laporan untuk uji alur status dan arsip selesai.',
            'status' => 'belum_dibaca',
            'urgency' => 'Sedang',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $r2 = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Verbal',
            'location' => 'Kantin',
            'story' => 'Laporan uji coba iseng untuk penandaan arsip iseng.',
            'status' => 'belum_dibaca',
            'urgency' => 'Rendah',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // Membuka detail oleh BK otomatis mengubah status belum_dibaca -> dibaca
        $this->actingAs($bk)->get("/bk/reports/{$r1->id}")->assertOk()->assertSee($r1->case_label);
        $this->assertSame('dibaca', $r1->fresh()->status);

        // BK ubah ke ditindaklanjuti lalu arsipkan sebagai selesai
        $this->actingAs($bk)->postJson("/bk/reports/{$r1->id}/status", ['status' => 'ditindaklanjuti'])->assertOk();
        $this->assertSame('ditindaklanjuti', $r1->fresh()->status);

        $this->actingAs($bk)->postJson("/bk/reports/{$r1->id}/archive", ['archive_reason' => 'selesai'])->assertOk();
        $r1->refresh();
        $this->assertNotNull($r1->archived_at);
        $this->assertSame('selesai', $r1->archive_reason);
        $this->assertSame('selesai', $r1->status);

        // Tandai iseng untuk r2
        $this->actingAs($bk)->postJson("/bk/reports/{$r2->id}/iseng")->assertOk();
        $r2->refresh();
        $this->assertNotNull($r2->archived_at);
        $this->assertSame('iseng', $r2->archive_reason);

        // Halaman arsip & statistik BK tampil dengan data nyata DB
        $archivePage = $this->actingAs($bk)->get('/bk/archive');
        $archivePage->assertOk();
        $archivePage->assertSee($r1->case_label);
        $archivePage->assertSee($r2->case_label);

        $this->actingAs($bk)->get('/bk/statistics')->assertOk()->assertSee('kurang dari 5');
    }

    public function test_kesiswaan_visibility_policy_and_bk_category_correction(): void
    {
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create(['name' => 'Waka Kesiswaan']);

        IncidentCategory::create([
            'label' => 'Bullying Fisik',
            'active' => true,
            'visible_to_kesiswaan' => true,
            'order_index' => 1,
        ]);

        IncidentCategory::create([
            'label' => 'Masalah Pribadi / Keluarga',
            'active' => true,
            'visible_to_kesiswaan' => false,
            'order_index' => 2,
        ]);

        $report = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Masalah Pribadi / Keluarga',
            'location' => 'Ruang Kelas',
            'story' => 'Cerita sensitif mengenai kondisi rumah dan kejadian di kelas.',
            'is_anonymous' => false,
            'reporter_name' => 'Budi Santoso',
            'reporter_class' => '8C',
            'reporter_contact' => '081234567890',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        // Kesiswaan TIDAK boleh melihat laporan yang kategorinya visible_to_kesiswaan = false
        $this->actingAs($kesiswaan)->get('/kesiswaan')->assertOk()->assertDontSee($report->case_label);
        $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}")->assertForbidden();
        $this->actingAs($kesiswaan)->post("/kesiswaan/reports/{$report->id}/status", ['status' => 'dibaca'])->assertForbidden();

        // Kesiswaan TIDAK bisa mengoreksi kategori
        $this->actingAs($kesiswaan)->postJson("/bk/reports/{$report->id}/category", ['category' => 'Bullying Fisik'])->assertForbidden();

        // Guru BK mengoreksi kategori -> menjadi terlihat untuk Kesiswaan tanpa PII
        $this->actingAs($bk)->postJson("/bk/reports/{$report->id}/category", ['category' => 'Bullying Fisik'])->assertOk();
        $this->assertSame('Bullying Fisik', $report->fresh()->category);

        $detail = $this->actingAs($kesiswaan)->get("/kesiswaan/reports/{$report->id}");
        $detail->assertOk()
            ->assertSee($report->case_label)
            ->assertDontSee('Budi Santoso')
            ->assertDontSee('081234567890')
            ->assertDontSee('8C');

        $this->actingAs($kesiswaan)->post("/kesiswaan/reports/{$report->id}/status", ['status' => 'ditindaklanjuti'])->assertRedirect();
        $this->assertSame('ditindaklanjuti', $report->fresh()->status);

        // Bulk mark iseng oleh Kesiswaan hanya memproses laporan yang diizinkan
        $hiddenReport = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Masalah Pribadi / Keluarga',
            'location' => 'Ruang Kelas',
            'story' => 'Laporan tersembunyi tidak boleh ikut di-isengkan oleh kesiswaan.',
            'urgency' => 'Sedang',
            'status' => 'belum_dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'siang',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $this->actingAs($kesiswaan)->post('/kesiswaan/reports/bulk-iseng', [
            'ids' => [$report->id, $hiddenReport->id],
        ])->assertRedirect(route('kesiswaan.dashboard'));

        $this->assertSame('iseng', $report->fresh()->archive_reason);
        $this->assertNull($hiddenReport->fresh()->archived_at);

        // Halaman pendukung Kesiswaan juga 200 OK serta profil bisa diperbarui
        $this->actingAs($kesiswaan)->get('/kesiswaan/archive')->assertOk();
        $this->actingAs($kesiswaan)->get('/kesiswaan/statistics')->assertOk();
        $this->actingAs($kesiswaan)->get('/kesiswaan/profile')->assertOk()->assertDontSee('Sarah Jenkins');

        $this->actingAs($kesiswaan)->post('/kesiswaan/profile/update', ['name' => 'Waka Kesiswaan Baru'])->assertRedirect();
        $this->assertSame('Waka Kesiswaan Baru', $kesiswaan->fresh()->name);

        $this->actingAs($kesiswaan)->post('/kesiswaan/profile/password', [
            'current_password' => 'StrongPass#2026',
            'password' => 'Kesiswaan!9901',
            'password_confirmation' => 'Kesiswaan!9901',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('Kesiswaan!9901', $kesiswaan->fresh()->password));
    }

    public function test_walas_forward_policy_isolation_encryption_and_toggle_read(): void
    {
        $bk = User::factory()->bk()->create(['name' => 'BK Koordinator']);
        $walasA = User::factory()->walas()->create(['name' => 'Ibu Rina 7A']);
        $walasB = User::factory()->walas()->create(['name' => 'Pak Joko 7B']);

        $report = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash(TicketCodeService::generateCode(10)),
            'category' => 'Pengucilan',
            'location' => 'Lapangan',
            'story' => 'Detail panjang rahasia pelapor yang tidak boleh bocor ke walas.',
            'is_anonymous' => false,
            'reporter_name' => 'Siti Aminah',
            'reporter_class' => '7A',
            'reporter_contact' => '089876543210',
            'urgency' => 'Sedang',
            'status' => 'dibaca',
            'submitted_date' => now('Asia/Jakarta')->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        $this->actingAs($bk)->postJson("/bk/reports/{$report->id}/forward", [
            'to_user_id' => $walasA->id,
            'summary' => 'Mohon observasi interaksi kelompok di kelas 7A.',
        ])->assertOk()->assertJson(['success' => true]);

        $forward = Forward::firstOrFail();
        $rawSummary = DB::table('forwards')->where('id', $forward->id)->value('summary_text');
        $this->assertNotEquals('Mohon observasi interaksi kelompok di kelas 7A.', $rawSummary);

        // Walas A hanya melihat ringkasan yang dikirim untuknya tanpa PII / cerita asli
        $this->actingAs($walasA)->get('/wali-kelas')->assertOk()->assertSee('Mohon observasi interaksi kelompok di kelas 7A.');
        $summaryPage = $this->actingAs($walasA)->get("/wali-kelas/ringkasan/{$forward->id}");
        $summaryPage->assertOk()
            ->assertSee('Mohon observasi interaksi kelompok di kelas 7A.')
            ->assertDontSee('Siti Aminah')
            ->assertDontSee('Detail panjang rahasia pelapor')
            ->assertDontSee('Sarah Jenkins');

        // Walas B TIDAK boleh membuka atau mengubah ringkasan milik Walas A
        $this->actingAs($walasB)->get("/wali-kelas/ringkasan/{$forward->id}")->assertForbidden();
        $this->actingAs($walasB)->post("/wali-kelas/ringkasan/{$forward->id}/toggle-read")->assertForbidden();

        // Walas A menandai sudah dibaca
        $this->actingAs($walasA)->postJson("/wali-kelas/ringkasan/{$forward->id}/toggle-read")
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'read']);
        $this->assertNotNull($forward->fresh()->read_at);
    }
}
