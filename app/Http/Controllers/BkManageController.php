<?php

namespace App\Http\Controllers;

use App\Jobs\ClassifyStudentReportJob;
use App\Mail\EmergencyReportAlertMail;
use App\Mail\StaffInvitationMail;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\ReportMessage;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\RetentionService;
use App\Support\SchoolConfigService;
use App\Support\TicketCodeService;
use App\Support\TurnstileVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkManageController extends Controller
{
    /**
     * Tampilkan halaman kelola jenis kejadian, titik lokasi, staf, konfigurasi sekolah, dan QR sekolah.
     */
    public function index(Request $request): View
    {
        if (IncidentCategory::count() === 0) {
            $this->seedDefaultCategories();
        }
        if (IncidentLocation::count() === 0) {
            $this->seedDefaultLocations();
        }

        $categories = IncidentCategory::orderBy('order_index')->orderBy('id')->get();
        $locations = IncidentLocation::orderBy('order_index')->orderBy('id')->get();
        $staffMembers = StaffMember::orderByDesc('id')->get();

        $schoolConfig = SchoolConfigService::getConfig();
        $schoolName = $schoolConfig['school_name'];
        $reportUrl = url('/s/lapor');
        $trainingMode = $schoolConfig['training_mode'];
        $nlpEnabled = $schoolConfig['nlp_enabled'];

        return view('bk.manage', [
            'categories' => $categories,
            'locations' => $locations,
            'staffMembers' => $staffMembers,
            'reportUrl' => $reportUrl,
            'qrScanUrl' => $reportUrl,
            'schoolName' => $schoolName,
            'schoolConfig' => $schoolConfig,
            'permendikbudTypes' => SchoolConfigService::permendikbudTypes(),
            'trainingMode' => $trainingMode,
            'nlpEnabled' => $nlpEnabled,
            'categoriesCount' => $categories->count(),
            'locationsCount' => $locations->count(),
            'staffCount' => $staffMembers->count(),
        ]);
    }

    /**
     * Perbarui pengaturan sekolah (FR-57), Mode Latihan (FR-68), dan Modul NLP ML (FR-42, FR-48).
     */
    public function updateSettings(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'training_mode' => ['nullable', 'boolean'],
            'nlp_enabled' => ['nullable', 'boolean'],
            'school_name' => ['nullable', 'string', 'max:120'],
            'reading_hours' => ['nullable', 'string', 'max:160'],
            'emergency_contact' => ['nullable', 'string', 'max:64'],
            'school_website_url' => ['nullable', 'url', 'max:255'],
            'retention_date' => ['nullable', 'date'],
            'story_max_length' => ['nullable', 'integer', 'min:200', 'max:10000'],
            'school_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if (array_key_exists('training_mode', $validated)) {
            $trainingValue = $validated['training_mode'] ? '1' : '0';
            SchoolSetting::set('training_mode', $trainingValue);
            AuditLogger::log(
                Auth::user(),
                'toggle_training_mode',
                null,
                $trainingValue === '1' ? 'Mode latihan diaktifkan' : 'Mode latihan dinonaktifkan'
            );
        }

        if (array_key_exists('nlp_enabled', $validated)) {
            $nlpValue = $validated['nlp_enabled'] ? '1' : '0';
            SchoolSetting::set('nlp_enabled', $nlpValue);
            AuditLogger::log(
                Auth::user(),
                'toggle_nlp_mode',
                null,
                $nlpValue === '1' ? 'Modul NLP diaktifkan' : 'Modul NLP dinonaktifkan'
            );
        }

        $schoolFields = ['school_name', 'reading_hours', 'emergency_contact', 'school_website_url', 'retention_date', 'story_max_length'];
        $updatedSchoolFields = [];

        foreach ($schoolFields as $field) {
            if ($request->has($field)) {
                $val = trim((string) ($validated[$field] ?? ''));
                SchoolSetting::set($field, $val);
                $updatedSchoolFields[] = $field;
            }
        }

        if ($request->hasFile('school_logo')) {
            $file = $request->file('school_logo');
            $uploadDir = public_path('uploads');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'school-logo-'.time().'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            SchoolSetting::set('school_logo', 'uploads/'.$filename);
            $updatedSchoolFields[] = 'school_logo';
        }

        if ($updatedSchoolFields !== []) {
            AuditLogger::log(
                Auth::user(),
                'update_school_settings',
                null,
                'Field: '.implode(', ', $updatedSchoolFields)
            );
        }

        $config = SchoolConfigService::getConfig();
        $message = 'Pengaturan sekolah dan sistem berhasil diperbarui.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'training_mode' => $config['training_mode'],
                'nlp_enabled' => $config['nlp_enabled'],
                'config' => $config,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#sekolah'])->with('frontend_notice', $message);
    }

    /**
     * Jalankan atau simulasikan retensi arsip akhir tahun ajaran (FR-65).
     */
    public function runRetention(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'test_date' => ['nullable', 'date'],
        ]);

        $result = RetentionService::run($validated['test_date'] ?? null, Auth::user());

        $message = $result['executed']
            ? "Retensi berhasil dijalankan ({$result['retention_date']}). {$result['deleted_count']} laporan arsip/selesai diringkas & dihapus; laporan aktif tetap aman."
            : ($result['skipped_reason'] ?? 'Retensi belum dijalankan.');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $result['executed'],
                'result' => $result,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#sekolah'])->with('frontend_notice', $message);
    }

    // =========================================================================
    // CRUD: JENIS KEJADIAN (CATEGORIES)
    // =========================================================================

    public function storeCategory(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'permendikbud_type' => ['nullable', 'string', 'max:120'],
            'active' => ['nullable', 'boolean'],
            'visible_to_kesiswaan' => ['nullable', 'boolean'],
        ]);

        $maxOrder = IncidentCategory::max('order_index') ?? 0;

        $category = IncidentCategory::create([
            'label' => trim($validated['label']),
            'permendikbud_type' => trim((string) ($validated['permendikbud_type'] ?? '')) ?: 'Pendampingan Konseling / Lainnya',
            'active' => $request->boolean('active', true),
            'permanent' => false,
            'visible_to_kesiswaan' => $request->boolean('visible_to_kesiswaan', true),
            'order_index' => $maxOrder + 1,
        ]);

        $message = "Jenis aduan '{$category->label}' berhasil ditambahkan.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $message);
    }

    public function updateCategory(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = IncidentCategory::findOrFail($id);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'permendikbud_type' => ['nullable', 'string', 'max:120'],
            'active' => ['nullable', 'boolean'],
            'visible_to_kesiswaan' => ['nullable', 'boolean'],
        ]);

        $category->label = trim($validated['label']);
        if ($request->has('permendikbud_type')) {
            $category->permendikbud_type = trim((string) ($validated['permendikbud_type'] ?? '')) ?: null;
        }
        if (! $category->permanent) {
            $category->active = $request->boolean('active', true);
        }
        if ($request->has('visible_to_kesiswaan')) {
            $category->visible_to_kesiswaan = $request->boolean('visible_to_kesiswaan');
        }
        $category->save();

        $message = "Jenis aduan '{$category->label}' berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $message);
    }

    public function toggleCategory(int $id): JsonResponse
    {
        $category = IncidentCategory::findOrFail($id);
        if ($category->permanent) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tetap dilindungi sistem dan tidak dapat dinonaktifkan.',
            ], 422);
        }

        $category->active = ! $category->active;
        $category->save();

        $statusStr = $category->active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'active' => $category->active,
            'message' => "Kategori '{$category->label}' berhasil {$statusStr}.",
        ]);
    }

    public function destroyCategory(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = IncidentCategory::findOrFail($id);
        if ($category->permanent) {
            $msg = 'Kategori tetap dilindungi sistem dan tidak dapat dihapus.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $msg);
        }

        $label = $category->label;
        $category->delete();

        $message = "Kategori '{$label}' berhasil dihapus.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $message);
    }

    public function reorderCategories(Request $request): JsonResponse
    {
        $order = $request->input('order', []);
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                IncidentCategory::where('id', $id)->update(['order_index' => $index + 1]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan jenis aduan berhasil disimpan.',
        ]);
    }

    // =========================================================================
    // CRUD: TITIK LOKASI KEJADIAN (LOCATIONS)
    // =========================================================================

    public function storeLocation(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]);

        $maxOrder = IncidentLocation::max('order_index') ?? 0;

        $location = IncidentLocation::create([
            'label' => trim($validated['label']),
            'active' => $request->boolean('active', true),
            'permanent' => false,
            'order_index' => $maxOrder + 1,
        ]);

        $message = "Titik lokasi '{$location->label}' berhasil ditambahkan.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'location' => $location,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#lokasi'])->with('frontend_notice', $message);
    }

    public function updateLocation(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $location = IncidentLocation::findOrFail($id);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]);

        $location->label = trim($validated['label']);
        if (! $location->permanent && $request->has('active')) {
            $location->active = $request->boolean('active');
        }
        $location->save();

        $message = "Titik lokasi '{$location->label}' berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'location' => $location,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#lokasi'])->with('frontend_notice', $message);
    }

    public function toggleLocation(int $id): JsonResponse
    {
        $location = IncidentLocation::findOrFail($id);
        $location->active = ! $location->active;
        $location->save();

        $statusStr = $location->active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'active' => $location->active,
            'message' => "Titik lokasi '{$location->label}' berhasil {$statusStr}.",
        ]);
    }

    public function destroyLocation(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $location = IncidentLocation::findOrFail($id);
        $label = $location->label;
        $location->delete();

        $message = "Titik lokasi '{$label}' berhasil dihapus.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#lokasi'])->with('frontend_notice', $message);
    }

    public function reorderLocations(Request $request): JsonResponse
    {
        $order = $request->input('order', []);
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                IncidentLocation::where('id', $id)->update(['order_index' => $index + 1]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan titik lokasi berhasil disimpan.',
        ]);
    }

    // =========================================================================
    // CRUD: UNDANG STAF & HAK AKSES (STAFF) - SIGNED INVITATION FLOW
    // =========================================================================

    public function storeStaff(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'role' => ['required', 'string', 'in:bk,kesiswaan,walikelas'],
            'class_name' => ['nullable', 'string', 'max:20'],
        ]);

        $email = strtolower(trim($validated['email']));
        $name = trim($validated['name'] ?? '');
        if ($name === '') {
            $name = ucwords(str_replace(['.', '_', '-'], ' ', explode('@', $email)[0]));
        }

        $staff = StaffMember::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $validated['role'],
                'class_name' => $validated['role'] === 'walikelas' ? ($validated['class_name'] ?? '7A') : null,
                'status' => 'pending',
                'invited_at' => now(),
            ]
        );

        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $invitationUrl = URL::temporarySignedRoute(
            'staff.invitation.show',
            now()->addDays(7),
            ['staff' => $staff->id]
        );

        $emailSent = false;
        try {
            Mail::to($staff->email)->send(new StaffInvitationMail($staff, $schoolName, $invitationUrl));
            $emailSent = true;
        } catch (\Throwable $e) {
            report($e);
        }

        AuditLogger::log(Auth::user(), 'invite_staff', null, "{$staff->email} ({$staff->role})");

        $message = $emailSent
            ? "Undangan akses staf berhasil dikirim ke {$staff->email}."
            : "Data staf {$staff->name} disimpan, namun pengiriman email gagal.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'staff' => [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'role' => $staff->role,
                    'class_name' => $staff->class_name,
                    'status' => $staff->status,
                    'initials' => $staff->initials ?? 'ST',
                    'invited_fmt' => 'Hari Ini, '.now()->format('H:i'),
                ],
                'email_sent' => $emailSent,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    public function updateStaff(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'role' => ['required', 'string', 'in:bk,kesiswaan,walikelas'],
            'class_name' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:aktif,pending,nonaktif'],
        ]);

        $staff = StaffMember::findOrFail($id);
        $oldEmail = strtolower((string) $staff->email);
        $staff->name = trim($validated['name']);
        $staff->email = strtolower(trim($validated['email']));
        $staff->role = $validated['role'];
        $staff->class_name = $validated['role'] === 'walikelas' ? ($validated['class_name'] ?? '7A') : null;
        $staff->status = $validated['status'];
        $staff->save();

        // Sinkronisasi peran & sesi akun User terkait saat status nonaktif / peran berubah (FR-27)
        $linkedUser = User::where('email', $oldEmail)->orWhere('email', $staff->email)->first();
        if ($linkedUser) {
            $mappedRole = match ($staff->role) {
                'walikelas' => 'wali_kelas',
                'kesiswaan' => 'kesiswaan',
                default => 'bk',
            };
            $linkedUser->name = $staff->name;
            $linkedUser->role = $mappedRole;
            $linkedUser->save();

            if ($staff->status === 'nonaktif' && Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $linkedUser->id)->delete();
            }
        }

        AuditLogger::log(Auth::user(), 'update_staff', null, "{$staff->email} ({$staff->role}: {$staff->status})");

        $message = "Data dan hak akses {$staff->name} berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'staff' => [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'role' => $staff->role,
                    'class_name' => $staff->class_name,
                    'status' => $staff->status,
                    'initials' => $staff->initials ?? 'ST',
                    'invited_fmt' => $staff->invited_at ? $staff->invited_at->format('d M Y') : '-',
                ],
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    /**
     * Reset kata sandi & 2FA akun staf terundang lalu kirim tautan aktivasi baru (FR-27).
     */
    public function resetStaffAccess(int $id): JsonResponse
    {
        $staff = StaffMember::findOrFail($id);
        $email = strtolower(trim((string) $staff->email));

        $linkedUser = User::where('email', $email)->first();
        if ($linkedUser && $linkedUser->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mereset akun sendiri yang sedang aktif.',
            ], 422);
        }

        if ($linkedUser) {
            $linkedUser->forceFill([
                'password' => Hash::make(Str::random(40)),
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $linkedUser->id)->delete();
            }
        }

        $staff->status = 'pending';
        $staff->invited_at = now();
        $staff->save();

        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $invitationUrl = URL::temporarySignedRoute(
            'staff.invitation.show',
            now()->addDays(7),
            ['staff' => $staff->id]
        );

        $emailSent = false;
        try {
            Mail::to($staff->email)->send(new StaffInvitationMail($staff, $schoolName, $invitationUrl));
            $emailSent = true;
        } catch (\Throwable $e) {
            report($e);
        }

        AuditLogger::log(Auth::user(), 'reset_staff_access', null, $staff->email);

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'status' => $staff->status,
            ],
            'message' => $emailSent
                ? "Kata sandi & 2FA {$staff->name} telah direset dan tautan aktivasi baru dikirim ke {$staff->email}."
                : "Kata sandi & 2FA {$staff->name} direset ke status pending (email gagal terkirim).",
        ]);
    }

    public function destroyStaff(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $staff = StaffMember::findOrFail($id);
        $name = $staff->name;
        $email = strtolower((string) $staff->email);

        $linkedUser = User::where('email', $email)->first();
        if ($linkedUser && $linkedUser->id !== Auth::id()) {
            $linkedUser->forceFill(['role' => null])->save();
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $linkedUser->id)->delete();
            }
        }

        $staff->delete();

        AuditLogger::log(Auth::user(), 'revoke_staff', null, $email);

        $message = "Akses staf {$name} berhasil dicabut dan dihapus.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    public function resendStaffInvite(int $id): JsonResponse
    {
        $staff = StaffMember::findOrFail($id);

        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $invitationUrl = URL::temporarySignedRoute(
            'staff.invitation.show',
            now()->addDays(7),
            ['staff' => $staff->id]
        );

        try {
            Mail::to($staff->email)->send(new StaffInvitationMail($staff, $schoolName, $invitationUrl));

            return response()->json([
                'success' => true,
                'message' => "Undangan bertanda tangan berhasil dikirim ulang ke {$staff->email}.",
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => "Gagal mengirim email undangan ke {$staff->email}. Periksa konfigurasi SMTP.",
            ], 500);
        }
    }

    // =========================================================================
    // PUBLIC QR REDIRECT & REPORTER FORM
    // =========================================================================

    /**
     * Redirect tautan QR lama tanpa mencatat pemindaian (FR-58).
     */
    public function handleQrScan(): RedirectResponse
    {
        return redirect()->route('public.report');
    }

    /**
     * Tampilkan formulir pelaporan siswa publik.
     */
    public function showPublicReport(Request $request): View
    {
        return $this->renderPublicReportView($request, false);
    }

    /**
     * Tampilkan formulir Mode Latihan (/latihan) dengan alur identik dan badge simulasi (FR-68).
     */
    public function showTrainingReport(Request $request): View
    {
        return $this->renderPublicReportView($request, true);
    }

    private function renderPublicReportView(Request $request, bool $forceTraining): View
    {
        $config = SchoolConfigService::getConfig();

        $categories = IncidentCategory::where('active', true)->orderBy('order_index')->get();
        if ($categories->isEmpty()) {
            $this->seedDefaultCategories();
            $categories = IncidentCategory::where('active', true)->orderBy('order_index')->get();
        }

        $locations = IncidentLocation::where('active', true)->orderBy('order_index')->get();
        if ($locations->isEmpty()) {
            $this->seedDefaultLocations();
            $locations = IncidentLocation::where('active', true)->orderBy('order_index')->get();
        }

        $trainingMode = $forceTraining
            || $request->query('mode') === 'latihan'
            || $config['training_mode'];

        return view('bk.report-public', [
            'schoolName' => $config['school_name'],
            'schoolConfig' => $config,
            'categories' => $categories,
            'locations' => $locations,
            'trainingMode' => $trainingMode,
            'storyMaxLength' => $config['story_max_length'],
            'turnstileSiteKey' => (string) config('services.turnstile.site_key', ''),
        ]);
    }

    /**
     * Simpan laporan dari rute /latihan secara terisolasi (FR-68).
     */
    public function storeTrainingReport(Request $request): JsonResponse|RedirectResponse
    {
        $request->merge(['is_training' => '1']);

        return $this->storePublicReport($request);
    }

    /**
     * Menyimpan aduan siswa publik secara minimal & terenkripsi.
     */
    public function storePublicReport(Request $request): JsonResponse|RedirectResponse
    {
        // Honeypot check (FR-54)
        if ($request->filled('website')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak valid.',
                ], 422);
            }

            return back()->withErrors(['story' => 'Permintaan tidak valid.']);
        }

        // Cloudflare Turnstile verification when configured (FR-54)
        $turnstileToken = $request->input('cf-turnstile-response')
            ?? $request->input('cf_turnstile_response');
        if (! TurnstileVerifier::verify(is_string($turnstileToken) ? $turnstileToken : null, $request->ip())) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi keamanan (Turnstile) gagal. Silakan muat ulang dan coba lagi.',
                ], 422);
            }

            return back()->withInput()->withErrors([
                'turnstile' => 'Verifikasi keamanan gagal. Silakan coba lagi.',
            ]);
        }

        $config = SchoolConfigService::getConfig();
        $storyMax = $config['story_max_length'];
        $isAnonymous = filter_var($request->input('is_anonymous', true), FILTER_VALIDATE_BOOLEAN);

        $request->validate([
            'category' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'custom_location' => ['nullable', 'string', 'max:120'],
            'involved_parties' => ['nullable', 'string', 'max:255'],
            'story' => ['required', 'string', 'min:5', 'max:'.$storyMax],
            'urgency' => ['nullable', 'string', 'max:32'],
            'reporter_role' => ['nullable', 'string', 'max:64'],
            'incident_time' => ['nullable', 'string', 'max:120'],
            'incident_date' => ['nullable', 'date', 'before_or_equal:today'],
            'incident_date_unknown' => ['nullable'],
            'is_anonymous' => ['nullable'],
            'is_training' => ['nullable'],
            'reporter_name' => [$isAnonymous ? 'nullable' : 'required', 'string', 'max:120'],
            'reporter_class' => [$isAnonymous ? 'nullable' : 'required', 'string', 'max:32'],
            'reporter_contact' => ['nullable', 'string', 'max:64'],
        ], [
            'category.required' => 'Pilih jenis kejadian yang terjadi.',
            'location.required' => 'Pilih titik lokasi kejadian.',
            'story.required' => 'Ceritakan apa yang terjadi pada kolom cerita.',
            'story.min' => 'Ceritakan kejadian dengan sedikit lebih rinci (minimal 5 karakter).',
            'story.max' => "Panjang cerita maksimal {$storyMax} karakter.",
            'reporter_name.required' => 'Jika ingin dihubungi Guru BK, nama wajib diisi.',
            'reporter_class.required' => 'Jika ingin dihubungi Guru BK, kelas wajib dipilih.',
            'incident_date.before_or_equal' => 'Tanggal kejadian tidak boleh melebihi hari ini.',
        ]);

        $rawLocation = trim((string) $request->input('location'));
        $customLocation = trim((string) $request->input('custom_location', ''));
        if (in_array(strtolower($rawLocation), ['__other__', 'lainnya'], true)) {
            if ($customLocation === '') {
                $err = 'Tuliskan nama tempat kejadian jika memilih opsi Lainnya.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $err], 422);
                }

                return back()->withInput()->withErrors(['location' => $err]);
            }
            $resolvedLocation = 'Lainnya: '.$customLocation;
        } else {
            $resolvedLocation = $rawLocation;
        }

        $dateUnknown = filter_var($request->input('incident_date_unknown', false), FILTER_VALIDATE_BOOLEAN);
        $incidentDate = $dateUnknown ? null : ($request->input('incident_date') ?: null);

        $today = now('Asia/Jakarta')->toDateString();
        $isTraining = filter_var($request->input('is_training'), FILTER_VALIDATE_BOOLEAN)
            || $config['training_mode'];

        // Per-device daily quota (max 5/day, NOT per-IP) via encrypted HttpOnly cookie (FR-53)
        $deviceId = (string) $request->cookie('hiowy_device_id', '');
        if (strlen($deviceId) < 16) {
            $deviceId = (string) Str::uuid();
        }
        $pepper = (string) (config('app.ticket_pepper') ?: config('app.key'));
        $deviceRateKey = 'report-device-daily:'.hash_hmac('sha256', $deviceId.'|'.$today, $pepper);

        if (! $isTraining && RateLimiter::tooManyAttempts($deviceRateKey, 5)) {
            $msg = 'Batas maksimal 5 laporan per perangkat hari ini telah tercapai. Hubungi langsung Guru BK jika mendesak.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 429)
                    ->withCookie(cookie('hiowy_device_id', $deviceId, 60 * 24 * 365, '/', null, null, true));
            }

            return back()->withErrors(['limit' => $msg])
                ->withCookie(cookie('hiowy_device_id', $deviceId, 60 * 24 * 365, '/', null, null, true));
        }

        do {
            $plainTicketCode = TicketCodeService::generateCode(10);
            $ticketHash = TicketCodeService::hash($plainTicketCode);
        } while (StudentReport::where('ticket_hash', $ticketHash)->exists());

        $report = StudentReport::create([
            'ticket_hash' => $ticketHash,
            'category' => $request->input('category'),
            'location' => $resolvedLocation,
            'involved_parties' => $request->input('involved_parties'),
            'urgency' => $request->input('urgency', 'Sedang'),
            'reporter_role' => $request->input('reporter_role', 'Siswa (Korban Langsung)'),
            'incident_time' => $request->input('incident_time', 'Tidak ingat pasti'),
            'incident_date' => $incidentDate,
            'story' => $request->input('story'),
            'is_anonymous' => $isAnonymous,
            'reporter_name' => $isAnonymous ? null : $request->input('reporter_name'),
            'reporter_class' => $isAnonymous ? null : $request->input('reporter_class'),
            'reporter_contact' => $isAnonymous ? null : $request->input('reporter_contact'),
            'status' => 'belum_dibaca',
            'submitted_date' => $today,
            'submitted_daypart' => TicketCodeService::daypart(),
            'status_updated_date' => $today,
            'is_training' => $isTraining,
            'ml_status' => 'belum_diklasifikasi',
        ]);

        if (! $isTraining) {
            RateLimiter::hit($deviceRateKey, 86400);
        }

        // Immediate emergency alert email without story or reporter identity to all BK counselors (FR-51)
        $isDarurat = in_array(strtolower(trim((string) $report->urgency)), ['darurat', 'urgent'], true);
        if (! $isTraining && $isDarurat) {
            $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
            $bkUsers = User::where('role', 'bk')->whereNotNull('email')->get();
            foreach ($bkUsers as $bkUser) {
                try {
                    Mail::to($bkUser->email)->send(new EmergencyReportAlertMail($report, $schoolName));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        // Async ML classification job only when NLP module is enabled and not in training mode (FR-42, FR-48)
        if (! $isTraining && SchoolSetting::get('nlp_enabled', '0') === '1') {
            try {
                ClassifyStudentReportJob::dispatch($report);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $message = $isTraining
            ? 'Aduan simulasi (Mode Latihan) tersimpan terpisah dari data asli.'
            : 'Aduanmu telah berhasil dikirimkan ke Guru BK. Simpan kode laporanmu baik-baik.';

        $cookie = cookie('hiowy_device_id', $deviceId, 60 * 24 * 365, '/', null, null, true);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'ticket_code' => $plainTicketCode,
                'is_training' => $isTraining,
                'data' => [
                    'ticket_code' => $plainTicketCode,
                    'category' => $report->category,
                    'location' => $report->location,
                    'is_anonymous' => $isAnonymous,
                ],
            ])->withCookie($cookie);
        }

        return redirect()->route('public.report')->with([
            'submitted_ticket' => $plainTicketCode,
            'submitted_category' => $report->category,
            'submitted_location' => $report->location,
            'status' => $message,
        ])->withCookie($cookie);
    }

    /**
     * Cek status tindak lanjut aduan siswa berdasarkan kode laporan 10 karakter (rate limit 5 gagal / 15 menit).
     */
    public function trackReport(Request $request): JsonResponse
    {
        $rateKey = 'track-report:'.sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $minutes = max(1, (int) ceil(RateLimiter::availableIn($rateKey) / 60));

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan kode laporan salah. Silakan coba lagi dalam {$minutes} menit.",
            ], 429);
        }

        $rawCode = (string) $request->input('ticket_code', '');
        $normalized = TicketCodeService::normalize($rawCode);

        if (strlen($normalized) !== 10) {
            RateLimiter::hit($rateKey, 900);

            return response()->json([
                'success' => false,
                'message' => 'Kode laporan harus terdiri dari 10 karakter yang valid.',
            ], 404);
        }

        $ticketHash = TicketCodeService::hash($normalized);
        $report = StudentReport::where('ticket_hash', $ticketHash)->first();

        if (! $report) {
            RateLimiter::hit($rateKey, 900);

            return response()->json([
                'success' => false,
                'message' => 'Kode laporan tidak ditemukan. Periksa kembali kode laporanmu.',
            ], 404);
        }

        RateLimiter::clear($rateKey);

        $publicStatus = match ($report->status) {
            'belum_dibaca', 'unread' => 'belum_dibaca',
            'dibaca', 'read' => 'dibaca',
            'ditindaklanjuti', 'sedang_diproses', 'in_progress' => 'ditindaklanjuti',
            'selesai', 'resolved' => 'selesai',
            default => 'dibaca',
        };

        $statusLabels = [
            'belum_dibaca' => 'Belum Dibaca',
            'dibaca' => 'Dibaca Konselor',
            'ditindaklanjuti' => 'Ditindaklanjuti',
            'selesai' => 'Selesai',
        ];

        // Tandai balasan dari Guru BK sebagai terbaca oleh pelapor (centang 2 di portal BK)
        $report->messages()
            ->where('sender', 'bk')
            ->whereNull('read_at')
            ->update(['read_at' => now('Asia/Jakarta')]);

        $messages = $report->messages()
            ->get()
            ->map(fn (ReportMessage $msg): array => $this->formatPublicMessageDto($msg))
            ->values()
            ->all();

        $config = SchoolConfigService::getConfig();

        return response()->json([
            'success' => true,
            'ticket' => [
                'status' => $publicStatus,
                'status_label' => $statusLabels[$publicStatus],
                'archived' => $report->archived_at !== null,
                'is_training' => (bool) $report->is_training,
                'bk_read_time' => $report->bk_read_at
                    ? $report->bk_read_at->copy()->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB'
                    : null,
                'reading_hours' => $config['reading_hours'],
                'emergency_contact' => $config['emergency_contact'],
                'emergency_wa_url' => $config['emergency_wa_url'],
                'emergency_tel_url' => $config['emergency_tel_url'],
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Balasan dari pelapor ke Guru BK melalui kode laporan 10 karakter (rate limited).
     */
    public function replyReport(Request $request): JsonResponse
    {
        $rateKey = 'track-report:'.sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $minutes = max(1, (int) ceil(RateLimiter::availableIn($rateKey) / 60));

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan kode laporan salah. Silakan coba lagi dalam {$minutes} menit.",
            ], 429);
        }

        $validated = $request->validate([
            'ticket_code' => ['required', 'string', 'max:64'],
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'body.required' => 'Tulis pesan balasan terlebih dahulu.',
            'body.min' => 'Pesan balasan minimal 2 karakter.',
        ]);

        $normalized = TicketCodeService::normalize($validated['ticket_code']);
        if (strlen($normalized) !== 10) {
            RateLimiter::hit($rateKey, 900);

            return response()->json([
                'success' => false,
                'message' => 'Kode laporan tidak valid.',
            ], 404);
        }

        $report = StudentReport::where('ticket_hash', TicketCodeService::hash($normalized))->first();

        if (! $report) {
            RateLimiter::hit($rateKey, 900);

            return response()->json([
                'success' => false,
                'message' => 'Kode laporan tidak ditemukan.',
            ], 404);
        }

        RateLimiter::clear($rateKey);

        if ($report->archived_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan telah selesai atau diarsipkan sehingga percakapan ditutup.',
            ], 422);
        }

        $today = now('Asia/Jakarta')->toDateString();

        // Pesan dari pelapor sengaja TIDAK menyimpan jam-menit H:i demi privasi FR-14 (Opsi A)
        $msg = ReportMessage::create([
            'student_report_id' => $report->id,
            'sender' => 'reporter',
            'user_id' => null,
            'body' => trim($validated['body']),
            'sent_date' => $today,
        ]);

        $report->update([
            'status_updated_date' => $today,
        ]);

        $messages = $report->messages()->get()
            ->map(fn (ReportMessage $m): array => $this->formatPublicMessageDto($m))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Balasanmu berhasil dikirim ke Guru BK.',
            'messages' => $messages,
            'sent' => $this->formatPublicMessageDto($msg),
        ]);
    }

    /**
     * Format DTO pesan dua arah sesuai Opsi A: jam H:i hanya dicatat/ditampilkan pada aksi Guru BK (FR-14).
     *
     * @return array<string, mixed>
     */
    private function formatPublicMessageDto(ReportMessage $msg): array
    {
        $isBk = $msg->sender === 'bk';
        $bkReadTime = (! $isBk && $msg->read_at)
            ? $msg->read_at->copy()->timezone('Asia/Jakarta')->format('H:i').' WIB'
            : null;

        return [
            'sender' => $msg->sender,
            'sender_label' => $isBk ? 'Guru BK' : 'Kamu (Pelapor)',
            'body' => $msg->body,
            'date' => $msg->sent_date ? $msg->sent_date->translatedFormat('d M Y') : '-',
            'time' => $isBk ? ($msg->bk_sent_time ?: null) : null,
            'is_read' => $msg->read_at !== null,
            'read_time' => $bkReadTime,
        ];
    }

    /**
     * Poster panduan format A4 landscape siap cetak.
     */
    public function downloadPoster(): View
    {
        $config = SchoolConfigService::getConfig();
        $reportUrl = url('/s/lapor');

        return view('bk.poster-print', [
            'schoolName' => $config['school_name'],
            'schoolConfig' => $config,
            'reportUrl' => $reportUrl,
            'qrScanUrl' => $reportUrl,
        ]);
    }

    // =========================================================================
    // DEFAULT CATEGORY & LOCATION INITIALIZERS (UI SELECT OPTIONS)
    // =========================================================================

    private function seedDefaultCategories(): void
    {
        $defaults = [
            ['label' => 'Ejekan & Intimidasi Verbal', 'permendikbud_type' => 'Perundungan Verbal / Kekerasan Psikis (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 1],
            ['label' => 'Perundungan Fisik & Pemukulan', 'permendikbud_type' => 'Kekerasan Fisik (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 2],
            ['label' => 'Pengucilan & Diskriminasi Sosial', 'permendikbud_type' => 'Diskriminasi & Intoleransi (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 3],
            ['label' => 'Pelecehan Melalui Chat & Medsos', 'permendikbud_type' => 'Perundungan Daring (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 4],
            ['label' => 'Sentuhan atau Perilaku Tidak Pantas', 'permendikbud_type' => 'Kekerasan Seksual (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 5],
            ['label' => 'Pemerasan & Tekanan Kelompok', 'permendikbud_type' => 'Kekerasan Psikis / Pemerasan (Permendikbudristek 46/2023)', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => false, 'order_index' => 6],
            ['label' => 'Masalah Pribadi & Lainnya', 'permendikbud_type' => 'Pendampingan Konseling / Lainnya', 'active' => true, 'permanent' => true, 'visible_to_kesiswaan' => false, 'order_index' => 7],
        ];

        foreach ($defaults as $cat) {
            IncidentCategory::firstOrCreate(['label' => $cat['label']], $cat);
        }
    }

    private function seedDefaultLocations(): void
    {
        $defaults = [
            ['label' => 'Ruang Kelas', 'active' => true, 'permanent' => false, 'order_index' => 1],
            ['label' => 'Kantin Sekolah', 'active' => true, 'permanent' => false, 'order_index' => 2],
            ['label' => 'Toilet & Kamar Mandi', 'active' => true, 'permanent' => false, 'order_index' => 3],
            ['label' => 'Lapangan & Area Olahraga', 'active' => true, 'permanent' => false, 'order_index' => 4],
            ['label' => 'Koridor & Area Tangga', 'active' => true, 'permanent' => false, 'order_index' => 5],
            ['label' => 'Perjalanan ke / dari Sekolah', 'active' => true, 'permanent' => false, 'order_index' => 6],
            ['label' => 'Daring (Chat & Media Sosial)', 'active' => true, 'permanent' => false, 'order_index' => 7],
        ];

        foreach ($defaults as $loc) {
            IncidentLocation::firstOrCreate(['label' => $loc['label']], $loc);
        }
    }
}
