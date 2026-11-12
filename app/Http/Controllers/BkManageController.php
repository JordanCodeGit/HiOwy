<?php

namespace App\Http\Controllers;

use App\Mail\StaffInvitationMail;
use App\Models\IncidentCategory;
use App\Models\IncidentLocation;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BkManageController extends Controller
{
    /**
     * Tampilkan halaman kelola jenis kejadian, titik lokasi, staf, dan QR sekolah.
     */
    public function index(Request $request): View
    {
        $hasTables = false;
        try {
            $hasTables = Schema::hasTable('incident_categories') &&
                         Schema::hasTable('incident_locations') &&
                         Schema::hasTable('staff_members') &&
                         Schema::hasTable('school_settings');
        } catch (\Throwable $e) {
            $hasTables = false;
        }

        if ($hasTables) {
            try {
                // Seed defaults jika tabel masih kosong
                if (IncidentCategory::count() === 0) {
                    $this->seedDefaultCategories();
                }
                if (IncidentLocation::count() === 0) {
                    $this->seedDefaultLocations();
                }
                if (StaffMember::count() === 0) {
                    $this->seedDefaultStaff();
                }
                $existingQrCount = SchoolSetting::where('key', 'qr_scans_count')->first();
                if (!$existingQrCount || (int)$existingQrCount->value === 379) {
                    SchoolSetting::set('qr_scans_count', 0);
                }

                $categories = IncidentCategory::orderBy('order_index')->orderBy('id')->get();
                $locations = IncidentLocation::orderBy('order_index')->orderBy('id')->get();
                $staffMembers = $this->getStaffMembersCollection(true);
            } catch (\Throwable $e) {
                $hasTables = false;
                $categories = collect($this->getDefaultCategoriesFallback());
                $locations = collect($this->getDefaultLocationsFallback());
                $staffMembers = $this->getStaffMembersCollection(false);
            }
        } else {
            // In-memory fallbacks sebelum migrasi dijalankan
            $categories = collect($this->getDefaultCategoriesFallback());
            $locations = collect($this->getDefaultLocationsFallback());
            $staffMembers = $this->getStaffMembersCollection(false);
        }

        $qrTotalScans = self::getQrScansCount();
        $schoolName = Auth::user()->school_name ?? 'SMP Telkom Purwokerto';
        $reportUrl = url('/s/lapor');
        $qrScanUrl = url('/s/qr');

        return view('bk.manage', [
            'categories'      => $categories,
            'locations'       => $locations,
            'staffMembers'    => $staffMembers,
            'qrTotalScans'    => $qrTotalScans,
            'reportUrl'       => $reportUrl,
            'qrScanUrl'       => $qrScanUrl,
            'schoolName'      => $schoolName,
            'hasTables'       => $hasTables,
            'categoriesCount' => $categories->count(),
            'locationsCount'  => $locations->count(),
            'staffCount'      => $staffMembers->count(),
        ]);
    }

    // =========================================================================
    // CRUD: JENIS KEJADIAN (CATEGORIES)
    // =========================================================================

    public function storeCategory(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'label'                => ['required', 'string', 'max:80'],
            'active'               => ['nullable', 'boolean'],
            'visible_to_kesiswaan' => ['nullable', 'boolean'],
        ]);

        $maxOrder = IncidentCategory::max('order_index') ?? 0;

        $category = IncidentCategory::create([
            'label'                => trim($validated['label']),
            'active'               => $request->boolean('active', true),
            'permanent'            => false,
            'visible_to_kesiswaan' => $request->boolean('visible_to_kesiswaan', true),
            'order_index'          => $maxOrder + 1,
        ]);

        $message = "Jenis aduan '{$category->label}' berhasil ditambahkan.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'category' => $category,
                'message'  => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $message);
    }

    public function updateCategory(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = IncidentCategory::findOrFail($id);

        $validated = $request->validate([
            'label'                => ['required', 'string', 'max:80'],
            'active'               => ['nullable', 'boolean'],
            'visible_to_kesiswaan' => ['nullable', 'boolean'],
        ]);

        $category->label = trim($validated['label']);
        if (!$category->permanent) {
            $category->active = $request->boolean('active', true);
        }
        if ($request->has('visible_to_kesiswaan')) {
            $category->visible_to_kesiswaan = $request->boolean('visible_to_kesiswaan');
        }
        $category->save();

        $message = "Jenis aduan '{$category->label}' berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'category' => $category,
                'message'  => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#jenis'])->with('frontend_notice', $message);
    }

    public function toggleCategory(Request $request, int $id): JsonResponse
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
            'success'  => true,
            'active'   => $category->active,
            'message'  => "Kategori '{$category->label}' berhasil {$statusStr}.",
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
        $order = $request->input('order', []); // array of IDs in new order
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
            'label'  => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]);

        $maxOrder = IncidentLocation::max('order_index') ?? 0;

        $location = IncidentLocation::create([
            'label'       => trim($validated['label']),
            'active'      => $request->boolean('active', true),
            'permanent'   => false,
            'order_index' => $maxOrder + 1,
        ]);

        $message = "Titik lokasi '{$location->label}' berhasil ditambahkan.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'location' => $location,
                'message'  => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#lokasi'])->with('frontend_notice', $message);
    }

    public function updateLocation(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $location = IncidentLocation::findOrFail($id);

        $validated = $request->validate([
            'label'  => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]);

        $location->label = trim($validated['label']);
        if (!$location->permanent && $request->has('active')) {
            $location->active = $request->boolean('active');
        }
        $location->save();

        $message = "Titik lokasi '{$location->label}' berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'location' => $location,
                'message'  => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#lokasi'])->with('frontend_notice', $message);
    }

    public function toggleLocation(Request $request, int $id): JsonResponse
    {
        $location = IncidentLocation::findOrFail($id);
        $location->active = ! $location->active;
        $location->save();

        $statusStr = $location->active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success'  => true,
            'active'   => $location->active,
            'message'  => "Titik lokasi '{$location->label}' berhasil {$statusStr}.",
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
        $order = $request->input('order', []); // array of IDs in new order
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
    // CRUD: UNDANG STAF & HAK AKSES (STAFF)
    // =========================================================================

    public function storeStaff(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['nullable', 'string', 'max:120'],
            'email'      => ['required', 'email', 'max:120'],
            'role'       => ['required', 'string', 'in:kesiswaan,walikelas'],
            'class_name' => ['nullable', 'string', 'max:20'],
        ]);

        $name = trim($validated['name'] ?? '');
        if (empty($name)) {
            $name = explode('@', $validated['email'])[0];
            $name = ucwords(str_replace(['.', '_', '-'], ' ', $name));
        }

        $staffData = [
            'name'       => $name,
            'email'      => strtolower(trim($validated['email'])),
            'role'       => $validated['role'],
            'class_name' => $validated['role'] === 'walikelas' ? ($validated['class_name'] ?? '7A') : null,
            'status'     => 'pending',
            'invited_at' => now()->toIso8601String(),
        ];

        $staff = null;
        try {
            if (Schema::hasTable('staff_members')) {
                $staff = StaffMember::create([
                    'name'       => $staffData['name'],
                    'email'      => $staffData['email'],
                    'role'       => $staffData['role'],
                    'class_name' => $staffData['class_name'],
                    'status'     => $staffData['status'],
                    'invited_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal menyimpan staf ke database, beralih ke fallback file: " . $e->getMessage());
        }

        if (!$staff) {
            $file = storage_path('framework/staff_members.json');
            $existing = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : $this->getDefaultStaffFallback();
            $newId = count($existing) > 0 ? (max(array_column($existing, 'id')) + 1) : 1;
            
            $words = preg_split('/\s+/', trim($name));
            $initials = count($words) === 1 ? strtoupper(substr($words[0], 0, 2)) : strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            
            $staff = (object) array_merge($staffData, [
                'id'          => $newId,
                'initials'    => $initials,
                'invited_fmt' => 'Hari Ini, ' . now()->format('H:i'),
            ]);
            array_unshift($existing, (array) $staff);
            @file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));
        }

        // Kirim email undangan via SMTP
        $schoolName = Auth::user()->school_name ?? 'SMP Telkom Purwokerto';
        $emailSent = false;
        try {
            $invitationUrl = route('register', ['email' => $staff->email]);
            Mail::to($staff->email)->send(new StaffInvitationMail($staff, $schoolName, $invitationUrl));
            $emailSent = true;
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email undangan staf ke {$staff->email}: " . $e->getMessage());
        }

        $message = $emailSent 
            ? "Undangan akses staf berhasil dikirim ke {$staff->email}."
            : "Data staf {$staff->name} berhasil ditambahkan dan siap digunakan.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'staff'   => [
                    'id'          => $staff->id,
                    'name'        => $staff->name,
                    'email'       => $staff->email,
                    'role'        => $staff->role,
                    'class_name'  => $staff->class_name,
                    'status'      => $staff->status,
                    'initials'    => $staff->initials ?? 'ST',
                    'invited_fmt' => 'Hari Ini, ' . now()->format('H:i'),
                ],
                'email_sent' => $emailSent,
                'message'    => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    public function updateStaff(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:120'],
            'email'      => ['required', 'email', 'max:120'],
            'role'       => ['required', 'string', 'in:kesiswaan,walikelas'],
            'class_name' => ['nullable', 'string', 'max:20'],
            'status'     => ['required', 'string', 'in:aktif,pending,nonaktif'],
        ]);

        $updatedStaff = null;
        try {
            if (Schema::hasTable('staff_members')) {
                $staff = StaffMember::find($id);
                if ($staff) {
                    $staff->name = trim($validated['name']);
                    $staff->email = strtolower(trim($validated['email']));
                    $staff->role = $validated['role'];
                    $staff->class_name = $validated['role'] === 'walikelas' ? ($validated['class_name'] ?? '7A') : null;
                    $staff->status = $validated['status'];
                    $staff->save();
                    $updatedStaff = $staff;
                }
            }
        } catch (\Throwable $e) {}

        if (!$updatedStaff) {
            $file = storage_path('framework/staff_members.json');
            $existing = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : $this->getDefaultStaffFallback();
            foreach ($existing as &$item) {
                if ($item['id'] == $id) {
                    $item['name'] = trim($validated['name']);
                    $item['email'] = strtolower(trim($validated['email']));
                    $item['role'] = $validated['role'];
                    $item['class_name'] = $validated['role'] === 'walikelas' ? ($validated['class_name'] ?? '7A') : null;
                    $item['status'] = $validated['status'];
                    $words = preg_split('/\s+/', $item['name']);
                    $item['initials'] = count($words) === 1 ? strtoupper(substr($words[0], 0, 2)) : strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    $updatedStaff = (object) $item;
                    break;
                }
            }
            @file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));
        }

        $message = "Data dan hak akses {$validated['name']} berhasil diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'staff'   => [
                    'id'          => $updatedStaff->id ?? $id,
                    'name'        => $updatedStaff->name ?? $validated['name'],
                    'email'       => $updatedStaff->email ?? $validated['email'],
                    'role'        => $updatedStaff->role ?? $validated['role'],
                    'class_name'  => $updatedStaff->class_name ?? null,
                    'status'      => $updatedStaff->status ?? $validated['status'],
                    'initials'    => $updatedStaff->initials ?? 'ST',
                    'invited_fmt' => isset($updatedStaff->invited_at) ? (is_object($updatedStaff->invited_at) ? $updatedStaff->invited_at->format('d M Y') : date('d M Y', strtotime($updatedStaff->invited_at))) : '-',
                ],
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    public function destroyStaff(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $name = 'Staf';
        try {
            if (Schema::hasTable('staff_members')) {
                $staff = StaffMember::find($id);
                if ($staff) {
                    $name = $staff->name;
                    $staff->delete();
                }
            }
        } catch (\Throwable $e) {}

        $file = storage_path('framework/staff_members.json');
        if (file_exists($file)) {
            $existing = json_decode(file_get_contents($file), true) ?: [];
            $filtered = [];
            foreach ($existing as $item) {
                if ($item['id'] == $id) {
                    $name = $item['name'];
                } else {
                    $filtered[] = $item;
                }
            }
            @file_put_contents($file, json_encode($filtered, JSON_PRETTY_PRINT));
        }

        $message = "Akses staf {$name} berhasil dicabut dan dihapus.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('bk.manage', ['#staf'])->with('frontend_notice', $message);
    }

    public function resendStaffInvite(Request $request, int $id): JsonResponse
    {
        $staff = null;
        try {
            if (Schema::hasTable('staff_members')) {
                $staff = StaffMember::find($id);
            }
        } catch (\Throwable $e) {}

        if (!$staff) {
            $file = storage_path('framework/staff_members.json');
            if (file_exists($file)) {
                $existing = json_decode(file_get_contents($file), true) ?: [];
                foreach ($existing as $item) {
                    if ($item['id'] == $id) {
                        $staff = (object) $item;
                        break;
                    }
                }
            }
        }

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Data staf tidak ditemukan.'], 404);
        }

        $schoolName = Auth::user()->school_name ?? 'SMP Telkom Purwokerto';
        try {
            $invitationUrl = route('register', ['email' => $staff->email]);
            Mail::to($staff->email)->send(new StaffInvitationMail($staff, $schoolName, $invitationUrl));
            return response()->json([
                'success' => true,
                'message' => "Undangan akses berhasil dikirim ulang ke {$staff->email}.",
            ]);
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim ulang email undangan staf: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Gagal mengirim email undangan ke {$staff->email}. Periksa koneksi internet / SMTP.",
            ], 200);
        }
    }

    // =========================================================================
    // QR SCAN & POSTER DISTRIBUTION
    // =========================================================================

    /**
     * Dapatkan total scan QR saat ini (dari database atau fallback storage).
     */
    public static function getQrScansCount(): int
    {
        try {
            if (Schema::hasTable('school_settings')) {
                $val = SchoolSetting::where('key', 'qr_scans_count')->value('value');
                if ($val !== null) {
                    return (int) $val;
                }
            }
        } catch (\Throwable $e) {
            // Fallback storage di bawah
        }

        $file = storage_path('framework/qr_scans.txt');
        if (file_exists($file)) {
            return (int) trim(file_get_contents($file));
        }

        return 0;
    }

    /**
     * Inkrementasi total scan QR (+1) dan simpan secara persisten.
     */
    public static function incrementQrScansCount(): int
    {
        $newCount = self::getQrScansCount() + 1;

        try {
            if (Schema::hasTable('school_settings')) {
                SchoolSetting::set('qr_scans_count', $newCount);
            }
        } catch (\Throwable $e) {
            // Abaikan koneksi database jika belum siap
        }

        $file = storage_path('framework/qr_scans.txt');
        @file_put_contents($file, (string) $newCount);

        return $newCount;
    }

    /**
     * Endpoint API untuk mengecek total scan QR terbaru (untuk reactive Alpine).
     */
    public function getQrStats(): JsonResponse
    {
        return response()->json([
            'success'     => true,
            'total_scans' => self::getQrScansCount(),
        ]);
    }

    /**
     * Endpoint pemindaian QR Code resmi sekolah.
     * Menginkrementasi counter pemindaian QR dan mengarahkan ke form pelaporan siswa.
     */
    public function handleQrScan(Request $request, ?string $code = null): RedirectResponse
    {
        self::incrementQrScansCount();

        return redirect()->route('public.report')
            ->with('from_qr', true);
    }

    /**
     * Tampilkan formulir pelaporan siswa publik (atau pratinjau).
     */
    public function showPublicReport(Request $request, ?string $code = null): View|RedirectResponse
    {
        $schoolName = Auth::user()->school_name ?? 'SMP Telkom Purwokerto';

        try {
            $categories = Schema::hasTable('incident_categories')
                ? IncidentCategory::where('active', true)->orderBy('order_index')->get()
                : collect($this->getDefaultCategoriesFallback())->where('active', true);
        } catch (\Throwable $e) {
            $categories = collect($this->getDefaultCategoriesFallback())->where('active', true);
        }

        try {
            $locations = Schema::hasTable('incident_locations')
                ? IncidentLocation::where('active', true)->orderBy('order_index')->get()
                : collect($this->getDefaultLocationsFallback())->where('active', true);
        } catch (\Throwable $e) {
            $locations = collect($this->getDefaultLocationsFallback())->where('active', true);
        }

        $fromQr = session('from_qr', false);
        if (!$fromQr && ($request->query('ref') === 'qr' || $request->query('scan') === '1' || $request->query('from') === 'qr')) {
            self::incrementQrScansCount();
            $fromQr = true;
        }

        return view('bk.report-public', [
            'schoolName' => $schoolName,
            'categories' => $categories,
            'locations'  => $locations,
            'fromQr'     => $fromQr,
        ]);
    }

    /**
     * Poster panduan format A4 siap cetak (300 DPI layout dengan browser print).
     */
    public function downloadPoster(): View
    {
        $schoolName = Auth::user()->school_name ?? 'SMP Telkom Purwokerto';

        return view('bk.poster-print', [
            'schoolName' => $schoolName,
            'reportUrl'  => url('/s/lapor'),
            'qrScanUrl'  => url('/s/qr'),
        ]);
    }

    // =========================================================================
    // SEEDERS & FALLBACK DATA
    // =========================================================================

    private function seedDefaultCategories(): void
    {
        $defaults = [
            ['label' => 'Ejekan & Intimidasi Verbal', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 1],
            ['label' => 'Perundungan Fisik & Pemukulan', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 2],
            ['label' => 'Pengucilan & Diskriminasi Sosial', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 3],
            ['label' => 'Pelecehan Melalui Chat & Medsos', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 4],
            ['label' => 'Sentuhan atau Perilaku Tidak Pantas', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 5],
            ['label' => 'Pemerasan & Tekanan Kelompok', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => false, 'order_index' => 6],
            ['label' => 'Masalah Pribadi & Lainnya', 'active' => true, 'permanent' => true, 'visible_to_kesiswaan' => false, 'order_index' => 7],
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

    private function seedDefaultStaff(): void
    {
        $defaults = [
            [
                'name'       => 'Dewi Rahmawati, M.Pd.',
                'email'      => 'dewi.rahma@sekolah.sch.id',
                'role'       => 'walikelas',
                'class_name' => '8A',
                'status'     => 'aktif',
                'invited_at' => now()->subDays(10),
            ],
            [
                'name'       => 'Hendra Kurniawan, M.Pd.',
                'email'      => 'hendra.kurniawan@sekolah.sch.id',
                'role'       => 'walikelas',
                'class_name' => '9B',
                'status'     => 'pending',
                'invited_at' => now(),
            ],
        ];

        foreach ($defaults as $staff) {
            StaffMember::firstOrCreate(['email' => $staff['email']], $staff);
        }
    }

    private function getDefaultCategoriesFallback(): array
    {
        return [
            (object)['id' => 1, 'label' => 'Ejekan & Intimidasi Verbal', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 1],
            (object)['id' => 2, 'label' => 'Perundungan Fisik & Pemukulan', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 2],
            (object)['id' => 3, 'label' => 'Pengucilan & Diskriminasi Sosial', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 3],
            (object)['id' => 4, 'label' => 'Pelecehan Melalui Chat & Medsos', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 4],
            (object)['id' => 5, 'label' => 'Sentuhan atau Perilaku Tidak Pantas', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => true, 'order_index' => 5],
            (object)['id' => 6, 'label' => 'Pemerasan & Tekanan Kelompok', 'active' => true, 'permanent' => false, 'visible_to_kesiswaan' => false, 'order_index' => 6],
            (object)['id' => 7, 'label' => 'Masalah Pribadi & Lainnya', 'active' => true, 'permanent' => true, 'visible_to_kesiswaan' => false, 'order_index' => 7],
        ];
    }

    private function getDefaultLocationsFallback(): array
    {
        return [
            (object)['id' => 1, 'label' => 'Ruang Kelas', 'active' => true, 'permanent' => false, 'order_index' => 1],
            (object)['id' => 2, 'label' => 'Kantin Sekolah', 'active' => true, 'permanent' => false, 'order_index' => 2],
            (object)['id' => 3, 'label' => 'Toilet & Kamar Mandi', 'active' => true, 'permanent' => false, 'order_index' => 3],
            (object)['id' => 4, 'label' => 'Lapangan & Area Olahraga', 'active' => true, 'permanent' => false, 'order_index' => 4],
            (object)['id' => 5, 'label' => 'Koridor & Area Tangga', 'active' => true, 'permanent' => false, 'order_index' => 5],
            (object)['id' => 6, 'label' => 'Perjalanan ke / dari Sekolah', 'active' => true, 'permanent' => false, 'order_index' => 6],
            (object)['id' => 7, 'label' => 'Daring (Chat & Media Sosial)', 'active' => true, 'permanent' => false, 'order_index' => 7],
        ];
    }

    private function getStaffMembersCollection(bool $hasTables): \Illuminate\Support\Collection
    {
        if ($hasTables) {
            try {
                if (StaffMember::count() === 0) {
                    $this->seedDefaultStaff();
                }
                return StaffMember::orderBy('id', 'desc')->get();
            } catch (\Throwable $e) {}
        }

        $file = storage_path('framework/staff_members.json');
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data) && count($data) > 0) {
                return collect($data)->map(fn($item) => (object) $item);
            }
        }

        $defaults = $this->getDefaultStaffFallback();
        @file_put_contents($file, json_encode($defaults, JSON_PRETTY_PRINT));
        return collect($defaults);
    }

    private function getDefaultStaffFallback(): array
    {
        return [
            (object)[
                'id'          => 1,
                'name'        => 'Dewi Rahmawati, M.Pd.',
                'email'       => 'dewi.rahma@sekolah.sch.id',
                'role'        => 'walikelas',
                'class_name'  => '8A',
                'status'      => 'aktif',
                'initials'    => 'DR',
                'invited_at'  => now()->subDays(10),
                'invited_fmt' => '02 Feb 2024',
            ],
            (object)[
                'id'          => 2,
                'name'        => 'Hendra Kurniawan, M.Pd.',
                'email'       => 'hendra.kurniawan@sekolah.sch.id',
                'role'        => 'walikelas',
                'class_name'  => '9B',
                'status'      => 'pending',
                'initials'    => 'HK',
                'invited_at'  => now(),
                'invited_fmt' => 'Hari Ini, 09:20',
            ],
        ];
    }
}
