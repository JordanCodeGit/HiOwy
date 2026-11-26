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
use App\Support\AcademicYearService;
use App\Support\AuditLogger;
use App\Support\SchoolConfigService;
use App\Support\StatisticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $baseQuery = fn () => AcademicYearService::applyToReports(
            StudentReport::active()->where('is_training', $isTrainingView)
        );

        $reports = $baseQuery()
            ->with('forwards')
            ->orderByRaw("CASE
                WHEN status = 'belum_dibaca' AND LOWER(COALESCE(urgency, '')) IN ('darurat', 'urgent') THEN 1
                WHEN status = 'belum_dibaca' AND ml_label = 1 THEN 2
                WHEN status = 'belum_dibaca' THEN 3
                WHEN status IN ('dibaca', 'ditindaklanjuti') THEN 4
                ELSE 5 END")
            ->orderByDesc('id')
            ->get();

        $unreadCount = $baseQuery()->where('status', 'belum_dibaca')->count();

        $urgentUnreadCount = $baseQuery()
            ->where('status', 'belum_dibaca')
            ->whereIn('urgency', ['Darurat', 'Urgent'])
            ->count();

        $threeDaysAgo = Carbon::now('Asia/Jakarta')->subDays(3)->toDateString();
        $staleCount = $baseQuery()
            ->where('status', '!=', 'selesai')
            ->where('status_updated_date', '<=', $threeDaysAgo)
            ->count();

        $tabBelumDibaca = $unreadCount;
        $tabSedangDitangani = $baseQuery()->whereIn('status', ['dibaca', 'ditindaklanjuti'])->count();
        $tabSelesai = $baseQuery()->where('status', 'selesai')->count();
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

        // Status belum_dibaca -> dibaca otomatis & catat jam baca BK (TS-003, TS-030)
        $updates = [];
        if ($report->status === 'belum_dibaca') {
            $updates['status'] = 'dibaca';
            $updates['status_updated_date'] = now('Asia/Jakarta')->toDateString();
        }
        if ($report->bk_read_at === null) {
            $updates['bk_read_at'] = now('Asia/Jakarta');
        }
        if ($updates !== []) {
            $report->update($updates);
        }

        // Tandai balasan pelapor yang belum dibaca sebagai terbaca oleh Guru BK (centang 2)
        $report->messages()
            ->where('sender', 'reporter')
            ->whereNull('read_at')
            ->update(['read_at' => now('Asia/Jakarta')]);

        $report->load('messages.user');

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
            'category' => ['nullable', 'string', 'max:120', Rule::exists('incident_categories', 'label')],
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

        $nowJkt = now('Asia/Jakarta');
        $today = $nowJkt->toDateString();

        $msg = ReportMessage::create([
            'student_report_id' => $report->id,
            'sender' => 'bk',
            'user_id' => Auth::id(),
            'body' => trim($validated['counselor_reply']),
            'sent_date' => $today,
            'bk_sent_time' => $nowJkt->format('H:i').' WIB',
        ]);

        $report->messages()
            ->where('sender', 'reporter')
            ->whereNull('read_at')
            ->update(['read_at' => $nowJkt]);

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
     * Keluarkan satu laporan dari arsip kembali ke antrean aktif BK.
     */
    public function unarchive(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        Gate::authorize('viewBk', $report);

        $nextStatus = $report->status === 'ditindaklanjuti' ? 'ditindaklanjuti' : 'dibaca';

        $report->update([
            'status' => $nextStatus,
            'archived_at' => null,
            'archive_reason' => null,
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'unarchive_report', $report, "Dipulihkan ke status: {$nextStatus}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikeluarkan dari arsip dan dikembalikan ke antrean aktif.',
            ]);
        }

        return redirect()->route('bk.reports.show', $report->id)
            ->with('status', 'Laporan berhasil dikeluarkan dari arsip.');
    }

    /**
     * Bulk Action: Keluarkan beberapa laporan dari arsip sekaligus.
     */
    public function bulkUnarchive(Request $request): JsonResponse|RedirectResponse
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $targets = StudentReport::archived()->whereIn('id', $validated['ids'])->get();
        $today = now('Asia/Jakarta')->toDateString();
        $count = 0;

        foreach ($targets as $targetReport) {
            $nextStatus = $targetReport->status === 'ditindaklanjuti' ? 'ditindaklanjuti' : 'dibaca';
            $targetReport->update([
                'status' => $nextStatus,
                'archived_at' => null,
                'archive_reason' => null,
                'status_updated_date' => $today,
            ]);
            AuditLogger::log(Auth::user(), 'unarchive_report', $targetReport, 'Bulk unarchive');
            $count++;
        }

        $message = "{$count} laporan berhasil dikeluarkan dari arsip ke antrean aktif.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Halaman Arsip Laporan BK (/bk/archive) membaca data nyata DB.
     */
    public function archiveIndex(): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $archivedReports = AcademicYearService::applyToReports(
            StudentReport::archived()->production()
        )->orderByDesc('id')->get();
        $totalArchived = $archivedReports->count();
        $resolvedCount = $archivedReports->where('archive_reason', 'selesai')->count();
        $isengCount = $archivedReports->where('archive_reason', 'iseng')->count();
        $unreadCount = AcademicYearService::applyToReports(
            StudentReport::active()->production()
        )->where('status', 'belum_dibaca')->count();

        return view('bk.archive', compact(
            'archivedReports',
            'totalArchived',
            'resolvedCount',
            'isengCount',
            'unreadCount'
        ));
    }

    /**
     * Halaman Statistik BK (/bk/statistics) membaca data agregat nyata DB (FR-59, FR-60, FR-61).
     */
    public function statistics(Request $request): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $selectedMonth = trim((string) $request->query('month', '')) ?: null;
        $stats = StatisticsService::buildFromReports(
            AcademicYearService::applyToReports(StudentReport::production())->get(),
            $selectedMonth,
            true
        );

        $unreadCount = AcademicYearService::applyToReports(
            StudentReport::active()->production()
        )->where('status', 'belum_dibaca')->count();

        return view('bk.statistics', [
            'stats' => $stats,
            'selectedMonth' => $selectedMonth,
            'total' => $stats['total'],
            'active' => $stats['active'],
            'completed' => $stats['completed'],
            'rate' => $stats['rate'],
            'unreadCount' => $unreadCount,
            'byCategory' => collect($stats['by_category'])->map(fn ($v) => $v['count']),
            'byStatus' => collect($stats['by_status'])->map(fn ($v) => $v['count'])->all(),
            'schoolConfig' => SchoolConfigService::getConfig(),
        ]);
    }

    /**
     * Ekspor statistik bulanan tanpa isi laporan dalam format CSV (FR-60, FR-61).
     */
    public function exportStatisticsCsv(Request $request): StreamedResponse
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $selectedMonth = trim((string) $request->query('month', '')) ?: null;
        $academicYearLabel = AcademicYearService::label();
        $stats = StatisticsService::buildFromReports(
            AcademicYearService::applyToReports(StudentReport::production())->get(),
            $selectedMonth,
            true
        );
        $schoolName = SchoolConfigService::getConfig()['school_name'];

        AuditLogger::log(Auth::user(), 'export_statistics_csv', null, $academicYearLabel.($selectedMonth ? " · Bulan: {$selectedMonth}" : ' · Semua bulan'));

        $filename = 'statistik-hiowy-'.($selectedMonth ?: 'semua').'-'.now('Asia/Jakarta')->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($stats, $schoolName, $selectedMonth, $academicYearLabel): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Laporan Statistik Agregat HiOwy', $schoolName]);
            fputcsv($out, ['Tahun Ajaran', $academicYearLabel]);
            fputcsv($out, ['Periode Filter', $selectedMonth ? ($stats['available_months'][$selectedMonth] ?? $selectedMonth) : 'Semua Bulan']);
            fputcsv($out, ['Catatan Privasi', 'Angka kelompok 1-4 disamarkan sebagai "kurang dari 5". Tidak memuat isi cerita maupun identitas pelapor.']);
            fputcsv($out, []);
            fputcsv($out, ['Ringkasan', 'Nilai']);
            fputcsv($out, ['Total Laporan Masuk', $stats['total_display']]);
            fputcsv($out, ['Laporan Aktif', $stats['active_display']]);
            fputcsv($out, ['Selesai Ditangani', $stats['completed_display']]);
            fputcsv($out, ['Tingkat Penyelesaian', $stats['rate'].'%']);
            fputcsv($out, ['Rata-rata Waktu Sampai Dibaca', $stats['avg_read_label']]);

            $sections = [
                'Tren Bulanan' => $stats['months'],
                'Jenis Kejadian' => $stats['by_category'],
                'Peran Pelapor' => $stats['by_role'],
                'Lokasi Kejadian' => $stats['by_location'],
                'Tingkat Urgensi' => $stats['by_urgency'],
                'Status Tindak Lanjut' => $stats['by_status'],
            ];

            foreach ($sections as $sectionTitle => $rows) {
                fputcsv($out, []);
                fputcsv($out, [$sectionTitle, 'Jumlah (Tersamarkan)', 'Persentase']);
                foreach ($rows as $row) {
                    fputcsv($out, [$row['label'], $row['display'], $row['pct'].'%']);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Halaman cetak PDF Statistik Bulanan tanpa isi cerita/PII (FR-60, FR-61).
     */
    public function exportStatisticsPdf(Request $request): View
    {
        Gate::authorize('viewAnyBk', StudentReport::class);

        $selectedMonth = trim((string) $request->query('month', '')) ?: null;
        $academicYearLabel = AcademicYearService::label();
        $stats = StatisticsService::buildFromReports(
            AcademicYearService::applyToReports(StudentReport::production())->get(),
            $selectedMonth,
            true
        );
        $schoolConfig = SchoolConfigService::getConfig();

        AuditLogger::log(Auth::user(), 'export_statistics_pdf', null, $academicYearLabel.($selectedMonth ? " · Bulan: {$selectedMonth}" : ' · Semua bulan'));

        return view('bk.statistics-print', [
            'stats' => $stats,
            'selectedMonth' => $selectedMonth,
            'academicYearLabel' => $academicYearLabel,
            'schoolConfig' => $schoolConfig,
            'schoolName' => $schoolConfig['school_name'],
        ]);
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
        $unreadCount = AcademicYearService::applyToReports(
            StudentReport::active()->production()
        )->where('status', 'belum_dibaca')->count();

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
