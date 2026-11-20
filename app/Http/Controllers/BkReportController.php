<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Forward;
use App\Models\IncidentCategory;
use App\Models\ReportMessage;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Models\User;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BkReportController extends Controller
{
    /**
     * Dashboard Utama Laporan BK (/bk).
     */
    public function dashboard(Request $request): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $isTrainingView = $request->query('mode') === 'latihan';
        $trainingSettingActive = SchoolSetting::get('training_mode', '0') === '1';

        $reports = StudentReport::active()
            ->where('is_training', $isTrainingView)
            ->with('forwards')
            ->orderByRaw("CASE
                WHEN status = 'belum_dibaca' AND LOWER(COALESCE(urgency, '')) IN ('darurat', 'urgent') THEN 1
                WHEN status = 'belum_dibaca' AND ml_label = 1 THEN 2
                WHEN status = 'belum_dibaca' THEN 3
                WHEN status IN ('dibaca', 'ditindaklanjuti') THEN 4
                ELSE 5 END")
            ->orderByDesc('id')
            ->get();

        $unreadCount = StudentReport::active()->where('is_training', $isTrainingView)->where('status', 'belum_dibaca')->count();

        $urgentUnreadCount = StudentReport::active()
            ->where('is_training', $isTrainingView)
            ->where('status', 'belum_dibaca')
            ->whereIn('urgency', ['Darurat', 'Urgent', 'Tinggi'])
            ->count();

        $threeDaysAgo = Carbon::now('Asia/Jakarta')->subDays(3)->toDateString();
        $staleCount = StudentReport::active()
            ->where('is_training', $isTrainingView)
            ->where('status', '!=', 'selesai')
            ->where('status_updated_date', '<=', $threeDaysAgo)
            ->count();

        $tabBelumDibaca = $unreadCount;
        $tabSedangDitangani = StudentReport::active()->where('is_training', $isTrainingView)->whereIn('status', ['dibaca', 'ditindaklanjuti'])->count();
        $tabSelesai = StudentReport::active()->where('is_training', $isTrainingView)->where('status', 'selesai')->count();
        $categories = IncidentCategory::where('active', true)->orderBy('order_index')->get();

        return view('bk.dashboard', compact(
            'reports',
            'unreadCount',
            'urgentUnreadCount',
            'staleCount',
            'tabBelumDibaca',
            'tabSedangDitangani',
            'tabSelesai',
            'isTrainingView',
            'trainingSettingActive',
            'categories'
        ));
    }

    /**
     * Tampilkan detail laporan spesifik (/bk/reports/{id}).
     */
    public function show(int $id): View
    {
        $report = StudentReport::with(['messages.user', 'forwards.toUser'])->findOrFail($id);
        Gate::authorize('viewBk', $report);

        // Status belum_dibaca -> dibaca otomatis saat Guru BK membuka laporan.
        if ($report->status === 'belum_dibaca') {
            $report->update([
                'status' => 'dibaca',
                'status_updated_date' => now('Asia/Jakarta')->toDateString(),
            ]);
        }

        AuditLogger::log(Auth::user(), 'view_report', $report);

        $counselors = User::where('role', 'bk')->pluck('name')->filter()->values()->all();
        if (Auth::check() && Auth::user()->name && ! in_array(Auth::user()->name, $counselors, true)) {
            array_unshift($counselors, Auth::user()->name);
        }
        if (empty($counselors)) {
            $counselors = ['Guru BK'];
        }

        $categories = IncidentCategory::where('active', true)->orderBy('order_index')->get();
        $waliKelasList = $this->getWaliKelasList();

        return view('bk.report', compact('report', 'counselors', 'categories', 'waliKelasList'));
    }

    /**
     * Fallback route untuk /bk/reports/preview.
     */
    public function preview(): View|RedirectResponse
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $first = StudentReport::active()->production()->orderByDesc('id')->first()
            ?? StudentReport::production()->orderByDesc('id')->first();
        if (! $first) {
            return redirect()->route('bk.dashboard')->with('status', 'Belum ada data laporan.');
        }

        return $this->show($first->id);
    }

    /**
     * Update status, urgensi, konselor penanggung jawab.
     */
    public function updateStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('viewBk', $report);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:unread,belum_dibaca,read,dibaca,ditindaklanjuti,sedang_diproses,in_progress,selesai,resolved'],
            'urgency' => ['nullable', 'string', 'in:Darurat,Urgent,Sedang,Medium,Rendah,Easy'],
            'assigned_counselor' => ['nullable', 'string', 'max:120'],
        ]);

        if (isset($validated['status'])) {
            $statusMap = [
                'unread' => 'belum_dibaca',
                'read' => 'dibaca',
                'in_progress' => 'ditindaklanjuti',
                'sedang_diproses' => 'ditindaklanjuti',
                'resolved' => 'selesai',
            ];
            $validated['status'] = $statusMap[$validated['status']] ?? $validated['status'];
            $validated['status_updated_date'] = now('Asia/Jakarta')->toDateString();
        }

        if (isset($validated['urgency'])) {
            $urgencyMap = [
                'Urgent' => 'Darurat',
                'Medium' => 'Sedang',
                'Easy' => 'Rendah',
            ];
            $validated['urgency'] = $urgencyMap[$validated['urgency']] ?? $validated['urgency'];
        }

        $report->update($validated);

        AuditLogger::log(
            Auth::user(),
            'update_status',
            $report,
            "Status: {$report->status}, Urgensi: {$report->urgency}"
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status dan penugasan laporan berhasil diperbarui.',
                'report' => $report,
            ]);
        }

        return back()->with('status', 'Status dan penugasan laporan berhasil diperbarui.');
    }

    /**
     * Koreksi jenis kejadian laporan oleh Guru BK (memengaruhi visibilitas Kesiswaan).
     */
    public function updateCategory(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('correctCategory', $report);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:120', Rule::exists('incident_categories', 'label')],
        ]);

        $oldCategory = $report->category;
        $report->update([
            'category' => $validated['category'],
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(
            Auth::user(),
            'correct_category',
            $report,
            "{$oldCategory} -> {$report->category}"
        );

        $message = "Jenis kejadian berhasil dikoreksi menjadi '{$report->category}'.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'category' => $report->category,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Balasan Konselor untuk Siswa (menyimpan ke tabel report_messages terenkripsi).
     */
    public function storeCounselorReply(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('reply', $report);

        $validated = $request->validate([
            'counselor_reply' => ['required', 'string', 'max:2000'],
        ]);

        $today = now('Asia/Jakarta')->toDateString();

        $msg = ReportMessage::create([
            'student_report_id' => $report->id,
            'sender' => 'bk',
            'user_id' => Auth::id(),
            'body' => trim($validated['counselor_reply']),
            'sent_date' => $today,
        ]);

        $nextStatus = in_array($report->status, ['belum_dibaca', 'dibaca'], true)
            ? 'ditindaklanjuti'
            : $report->status;

        $report->update([
            'status' => $nextStatus,
            'status_updated_date' => $today,
        ]);

        AuditLogger::log(Auth::user(), 'reply_report', $report);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan resmi konselor berhasil dikirimkan.',
                'reply' => $msg->body,
                'date' => $msg->sent_date->translatedFormat('d M Y'),
            ]);
        }

        return back()->with('status', 'Balasan konselor berhasil dikirimkan.');
    }

    /**
     * Simpan Catatan Rahasia Antar Konselor.
     */
    public function storeInternalNote(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('viewBk', $report);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $author = Auth::user()->name ?? 'Konselor BK';
        $date = now('Asia/Jakarta')->translatedFormat('d M Y');
        $newNoteEntry = "[{$date}] {$author}: ".$validated['note'];

        $existing = $report->internal_notes ? $report->internal_notes."\n\n" : '';
        $report->update([
            'internal_notes' => $existing.$newNoteEntry,
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'add_note', $report);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan rahasia berhasil disimpan.',
                'internal_notes' => $report->internal_notes,
            ]);
        }

        return back()->with('status', 'Catatan rahasia berhasil disimpan.');
    }

    /**
     * Teruskan Ringkasan Aman ke Wali Kelas melalui tabel forwards (terenkripsi).
     */
    public function forwardToWalas(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('forward', $report);

        $validated = $request->validate([
            'summary' => ['required', 'string', 'max:2000'],
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $toUser = User::findOrFail((int) $validated['to_user_id']);
        if ($toUser->normalizedRole() !== 'walas') {
            return response()->json([
                'success' => false,
                'message' => 'Penerima terusan harus merupakan akun Wali Kelas aktif.',
            ], 422);
        }

        Forward::create([
            'student_report_id' => $report->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $toUser->id,
            'summary' => trim($validated['summary']),
            'forwarded_at' => now(),
        ]);

        $report->update([
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'forward_report', $report, "Diteruskan ke {$toUser->name}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Ringkasan aman berhasil diteruskan kepada {$toUser->name}.",
            ]);
        }

        return back()->with('status', "Ringkasan aman berhasil diteruskan kepada {$toUser->name}.");
    }

    /**
     * Tandai Laporan Iseng (Single) -> arsip dengan archive_reason = iseng.
     */
    public function markIseng(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('viewBk', $report);

        $report->update([
            'archived_at' => now(),
            'archive_reason' => 'iseng',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'mark_iseng', $report);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan telah ditandai iseng dan dipindahkan ke arsip.',
            ]);
        }

        return redirect()->route('bk.dashboard')->with('status', 'Laporan telah ditandai iseng.');
    }

    /**
     * Bulk Action: Tandai Iseng untuk beberapa laporan.
     */
    public function bulkMarkIseng(Request $request): JsonResponse|RedirectResponse
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ]);

        $targets = StudentReport::whereIn('id', $validated['ids'])->get();
        $count = StudentReport::whereIn('id', $validated['ids'])->update([
            'archived_at' => now(),
            'archive_reason' => 'iseng',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        foreach ($targets as $targetReport) {
            AuditLogger::log(Auth::user(), 'mark_iseng', $targetReport, 'Bulk action');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} laporan berhasil ditandai iseng dan diarsipkan.",
                'count' => $count,
            ]);
        }

        return back()->with('status', "{$count} laporan berhasil ditandai iseng.");
    }

    /**
     * Arsipkan Laporan dengan alasan (selesai / iseng).
     */
    public function archive(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('viewBk', $report);

        $validated = $request->validate([
            'archive_reason' => ['nullable', 'string', 'in:selesai,iseng'],
        ]);

        $reason = $validated['archive_reason'] ?? 'selesai';

        $report->update([
            'status' => $reason === 'selesai' ? 'selesai' : $report->status,
            'archived_at' => now(),
            'archive_reason' => $reason,
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'archive_report', $report, "Alasan: {$reason}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diarsipkan.',
            ]);
        }

        return redirect()->route('bk.dashboard')->with('status', 'Laporan berhasil diarsipkan.');
    }

    /**
     * Halaman Arsip Laporan BK (/bk/archive) membaca data nyata DB.
     */
    public function archiveIndex(): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $archivedReports = StudentReport::archived()->production()->orderByDesc('id')->get();
        $totalArchived = $archivedReports->count();
        $resolvedCount = $archivedReports->where('archive_reason', 'selesai')->count();
        $isengCount = $archivedReports->where('archive_reason', 'iseng')->count();
        $unreadCount = StudentReport::active()->production()->where('status', 'belum_dibaca')->count();

        return view('bk.archive', compact(
            'archivedReports',
            'totalArchived',
            'resolvedCount',
            'isengCount',
            'unreadCount'
        ));
    }

    /**
     * Halaman Statistik BK (/bk/statistics) membaca data agregat nyata DB.
     */
    public function statistics(): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $all = StudentReport::production()->get();
        $total = $all->count();
        $active = $all->whereNull('archived_at')->count();
        $completed = $all->filter(fn (StudentReport $r) => $r->status === 'selesai' || $r->archive_reason === 'selesai')->count();
        $rate = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        $unreadCount = $all->whereNull('archived_at')->where('status', 'belum_dibaca')->count();

        $byCategory = $all->groupBy('category')->map->count()->sortDesc();
        $byStatus = [
            'belum_dibaca' => $all->where('status', 'belum_dibaca')->count(),
            'dibaca' => $all->where('status', 'dibaca')->count(),
            'ditindaklanjuti' => $all->where('status', 'ditindaklanjuti')->count(),
            'selesai' => $all->where('status', 'selesai')->count(),
            'iseng' => $all->where('archive_reason', 'iseng')->count(),
        ];

        return view('bk.statistics', compact(
            'total',
            'active',
            'completed',
            'rate',
            'unreadCount',
            'byCategory',
            'byStatus'
        ));
    }

    /**
     * Halaman Log Akses & Audit Keamanan BK (/bk/access-log).
     */
    public function accessLog(): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $logs = AccessLog::with(['user', 'report'])->orderByDesc('id')->limit(200)->get();
        $totalLogs = AccessLog::count();
        $sensitiveViewsCount = AccessLog::whereIn('action', ['view_report', 'reply_report', 'add_note', 'forward_report'])->count();
        $failedAuthCount = AccessLog::whereIn('action', ['login_failed', 'login_locked', '2fa_failed'])->count();
        $unreadCount = StudentReport::active()->production()->where('status', 'belum_dibaca')->count();

        return view('bk.access-log', compact(
            'logs',
            'totalLogs',
            'sensitiveViewsCount',
            'failedAuthCount',
            'unreadCount'
        ));
    }

    /**
     * Helper untuk mengambil daftar user Wali Kelas aktif untuk penerusan.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getWaliKelasList(): array
    {
        $walasUsers = User::whereIn('role', ['walas', 'wali_kelas', 'walikelas'])->get();
        if ($walasUsers->isEmpty()) {
            return [];
        }

        $staffByEmail = Schema::hasTable('staff_members')
            ? StaffMember::all()->keyBy(fn (StaffMember $s) => strtolower($s->email))
            : collect([]);

        return $walasUsers->map(function (User $u) use ($staffByEmail): array {
            $staff = $staffByEmail->get(strtolower($u->email));
            $class = $staff?->class_name ?: '-';

            return [
                'id' => $u->id,
                'name' => $u->name,
                'class' => $class,
                'label' => $class !== '-' ? "{$u->name} (Wali Kelas {$class})" : "{$u->name} (Wali Kelas)",
            ];
        })->values()->all();
    }
}
