<?php

namespace Tests\Feature;

use App\Mail\StaffInvitationMail;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\TicketCodeService;
use App\Support\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class Batch1SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_on_all_staff_routes(): void
    {
        $this->get('/bk')->assertRedirect(route('login'));
        $this->get('/bk/manage')->assertRedirect(route('login'));
        $this->get('/bk/reports/1')->assertRedirect(route('login'));
        $this->get('/kesiswaan')->assertRedirect(route('login'));
        $this->get('/wali-kelas')->assertRedirect(route('login'));
    }

    public function test_unconfirmed_2fa_user_cannot_access_staff_routes(): void
    {
        $bkPending2fa = User::factory()->bk()->withoutTwoFactor()->create();

        $this->actingAs($bkPending2fa)
            ->get('/bk')
            ->assertRedirect(route('two-factor.setup'));
    }

    public function test_role_isolation_enforced_across_staff_portals(): void
    {
        $bk = User::factory()->bk()->create();
        $kesiswaan = User::factory()->kesiswaan()->create();
        $walas = User::factory()->walas()->create();

        $this->actingAs($bk)->get('/bk')->assertOk();
        $this->actingAs($bk)->get('/kesiswaan')->assertForbidden();
        $this->actingAs($bk)->get('/wali-kelas')->assertForbidden();

        $this->actingAs($kesiswaan)->get('/kesiswaan')->assertOk();
        $this->actingAs($kesiswaan)->get('/bk')->assertForbidden();
        $this->actingAs($kesiswaan)->get('/wali-kelas')->assertForbidden();

        $this->actingAs($walas)->get('/wali-kelas')->assertOk();
        $this->actingAs($walas)->get('/bk')->assertForbidden();
        $this->actingAs($walas)->get('/kesiswaan')->assertForbidden();
    }

    public function test_public_signup_google_and_onboarding_routes_are_removed(): void
    {
        $this->get('/signup')->assertNotFound();
        $this->post('/signup', [])->assertNotFound();
        $this->get('/auth/google')->assertNotFound();
        $this->get('/auth/google/callback')->assertNotFound();
        $this->get('/onboarding/role')->assertNotFound();
        $this->get('/onboarding/school')->assertNotFound();
    }

    public function test_users_table_no_longer_has_google_id_school_name_or_bk_default_role(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'google_id'));
        $this->assertFalse(Schema::hasColumn('users', 'school_name'));

        $user = User::factory()->create(['role' => null]);
        $this->assertNull($user->fresh()->role);
        $this->assertNotEquals('bk', $user->fresh()->role);
    }

    public function test_staff_invitation_uses_signed_url_and_requires_password_plus_2fa(): void
    {
        Mail::fake();
        $bkAdmin = User::factory()->bk()->create();

        $response = $this->actingAs($bkAdmin)
            ->postJson('/bk/manage/staff', [
                'name' => 'Bu Dina',
                'email' => 'dina@smptelkompwt.sch.id',
                'role' => 'walikelas',
                'class_name' => '7B',
            ]);

        $response->assertOk()->assertJsonPath('success', true);

        Mail::assertSent(StaffInvitationMail::class, function (StaffInvitationMail $mail) {
            return str_contains($mail->invitationUrl, '/undangan-staf/')
                && str_contains($mail->invitationUrl, 'signature=');
        });

        $staff = StaffMember::where('email', 'dina@smptelkompwt.sch.id')->firstOrFail();

        // Unsigned access rejected
        $this->get('/undangan-staf/'.$staff->id)->assertForbidden();

        // Signed link opens setup page
        $signedShowUrl = URL::temporarySignedRoute('staff.invitation.show', now()->addDay(), ['staff' => $staff->id]);
        $this->get($signedShowUrl)->assertOk()->assertSee('Bu Dina');

        // Accept invitation sets strong password and redirects to 2FA setup (still unauthenticated!)
        $signedAcceptUrl = URL::temporarySignedRoute('staff.invitation.accept', now()->addHour(), ['staff' => $staff->id]);
        $this->post($signedAcceptUrl, [
            'name' => 'Bu Dina',
            'password' => 'StrongPass#2026',
            'password_confirmation' => 'StrongPass#2026',
            'terms' => '1',
        ])->assertRedirect(route('two-factor.setup'));

        $this->assertGuest();

        $invitedUser = User::where('email', 'dina@smptelkompwt.sch.id')->firstOrFail();
        $this->assertEquals('wali_kelas', $invitedUser->role);
        $this->assertNull($invitedUser->two_factor_confirmed_at);

        // Confirm 2FA finishes login & activates StaffMember
        $totpCode = (new TotpService)->getCode($invitedUser->two_factor_secret, time(), 60);
        $this->post('/two-factor', ['code' => $totpCode])
            ->assertRedirect(route('walas.dashboard'));

        $this->assertAuthenticatedAs($invitedUser);
        $this->assertEquals('aktif', $staff->fresh()->status);
    }

    public function test_ticket_code_is_10_chars_safe_alphabet_hashed_and_rate_limited(): void
    {
        IncidentCategory::create(['label' => 'Bullying Verbal', 'active' => true, 'visible_to_kesiswaan' => true]);
        IncidentLocation::create(['label' => 'Ruang Kelas', 'active' => true]);

        $res = $this->postJson('/s/lapor', [
            'category' => 'Bullying Verbal',
            'location' => 'Ruang Kelas',
            'involved_parties' => 'Teman sekelas',
            'urgency' => 'Darurat',
            'reporter_role' => 'Siswa (Korban Langsung)',
            'story' => 'Saya diejek terus menerus di koridor sekolah.',
            'is_anonymous' => '0',
            'reporter_name' => 'Budi',
            'reporter_class' => '7B',
            'reporter_contact' => '08123456789',
        ]);

        $res->assertOk()->assertJsonPath('success', true);
        $code = $res->json('ticket_code');

        $this->assertMatchesRegularExpression('/^[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{10}$/', $code);

        $rawRow = DB::table('student_reports')->first();
        $this->assertNotNull($rawRow);
        $this->assertEquals(TicketCodeService::hash($code), $rawRow->ticket_hash);
        $this->assertFalse(Schema::hasColumn('student_reports', 'ticket_code'));
        $this->assertFalse(Schema::hasColumn('student_reports', 'ip_address'));
        $this->assertFalse(Schema::hasColumn('student_reports', 'attachment_path'));
        $this->assertFalse(Schema::hasColumn('student_reports', 'from_qr'));
        $this->assertFalse(Schema::hasColumn('student_reports', 'created_at'));

        // Raw DB columns must be encrypted ciphertext
        $this->assertNotEquals('Saya diejek terus menerus di koridor sekolah.', $rawRow->story);
        $this->assertNotEquals('Budi', $rawRow->reporter_name);
        $this->assertNotEquals('7B', $rawRow->reporter_class);
        $this->assertNotEquals('08123456789', $rawRow->reporter_contact);

        // No JSON/TXT fallback written
        $this->assertFileDoesNotExist(storage_path('framework/student_reports.json'));
        $this->assertFileDoesNotExist(storage_path('framework/qr_scans.txt'));

        // Valid tracking response must NOT contain urgency, notes, H:i, or fake default text
        $track = $this->postJson('/s/lapor/track', ['ticket_code' => $code]);
        $track->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('ticket.status', 'belum_dibaca')
            ->assertJsonMissingPath('ticket.urgency')
            ->assertJsonMissingPath('ticket.notes');
        $this->assertStringNotContainsString('observasi tertutup', $track->getContent());

        // 5 failed attempts trigger rate limit (429) on 6th attempt
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/s/lapor/track', ['ticket_code' => '2222222222'])->assertStatus(404);
        }
        $this->postJson('/s/lapor/track', ['ticket_code' => '2222222222'])->assertStatus(429);
    }

    public function test_guest_opening_report_does_not_mark_it_read_and_qr_stats_route_removed(): void
    {
        $report = StudentReport::create([
            'ticket_hash' => TicketCodeService::hash('23456789AB'),
            'category' => 'Bullying Verbal',
            'location' => 'Kelas',
            'urgency' => 'Sedang',
            'story' => 'Laporan rahasia siswa',
            'is_anonymous' => true,
            'status' => 'belum_dibaca',
            'submitted_date' => now()->toDateString(),
            'submitted_daypart' => 'pagi',
            'status_updated_date' => now()->toDateString(),
        ]);

        // Guest blocked and status remains belum_dibaca
        $this->get('/bk/reports/'.$report->id)->assertRedirect(route('login'));
        $this->assertEquals('belum_dibaca', $report->fresh()->status);

        // QR stats removed
        $bk = User::factory()->bk()->create();
        $this->actingAs($bk)->get('/bk/manage/qr/stats')->assertNotFound();

        // /s/qr redirects without saving from_qr or incrementing count
        $this->get('/s/qr')->assertRedirect(route('public.report'));
        $this->assertNull(DB::table('school_settings')->where('key', 'qr_scans_count')->value('value'));

        // Authenticated BK opening report DOES mark it dibaca
        $this->actingAs($bk)->get('/bk/reports/'.$report->id)->assertOk();
        $this->assertEquals('dibaca', $report->fresh()->status);
    }
}
