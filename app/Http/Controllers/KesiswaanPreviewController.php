<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\IncidentCategory;
use App\Models\SchoolSetting;
use App\Models\StudentReport;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class KesiswaanPreviewController extends Controller
{
    /**
     * Ambil daftar nama kategori kejadian yang diizinkan untuk Kesiswaan.
     *
     * @return array<int, string>
     */
    private function allowedCategoryLabels(): array
    {
        return IncidentCategory::where('visible_to_kesiswaan', true)
            ->pluck('label')
            ->all();
    }

    /**
     * Query laporan dasar yang boleh dilihat Kesiswaan.
     */
    private function visibleReportsCollection(): Collection
    {
        $allowedLabels = $this->allowedCategoryLabels();
        if (empty($allowedLabels)) {
            return collect([]);
        }

        return StudentReport::production()
            ->whereIn('category', $allowedLabels)
            ->orderByDesc('id')
            ->get()
            ->map(fn (StudentReport $r) => $this->toSafeDto($r));
    }

    /**
     * Proyeksikan laporan tanpa menyertakan identitas, kelas, kontak pelapor, maupun catatan rahasia BK.
     *
     * @return array<string, mixed>
     */
    private function toSafeDto(StudentReport $report): array
    {
        return [
            'id' => $report->id,
            'ticket' => $report->case_label,
            'type' => $report->category_normalized,
            'type_label' => $report->category,
            'status' => $report->status,
            'archived' => $report->archived_at !== null,
            'archived_at' => $report->archived_at ? $report->archived_at->translatedFormat('d M Y') : null,
            'reason' => $report->archive_reason === 'iseng' ? 'Laporan Iseng' : ($report->archive_reason === 'selesai' ? 'Selesai Ditangani' : ''),
            'archive_reason' => $report->archive_reason,
            'role' => $report->reporter_role ?: 'Siswa',
            'urgency' => $report->urgency ?: 'Sedang',
            'danger_flag' => $report->urgency_normalized === 'urgent',
            'date' => $report->submitted_date ? $report->submitted_date->format('Y-m-d') : now()->toDateString(),
            'status_date' => $report->status_updated_date ? $report->status_updated_date->format('Y-m-d') : now()->toDateString(),
            'received' => $report->formatted_time,
            'incident_time' => $report->incident_time ?: $report->formatted_time,
            'location' => $report->location ?: 'Area Sekolah',
            'parties' => 'Dirahasiakan',
            'wants_contact' => $report->wants_contact,
            'assigned' => $report->assigned_counselor ?: 'Belum Ditugaskan',
            'snippet' => Str::limit((string) $report->story, 85),
            'story' => $report->story,
            'notes' => [],
            'activities' => [
                ['time' => $report->formatted_time, 'label' => 'Laporan diterima sistem'],
                ['time' => $report->status_updated_date ? $report->status_updated_date->translatedFormat('d M Y') : '-', 'label' => 'Status: '.ucfirst(str_replace('_', ' ', $report->status))],
            ],
        ];
    }

    private function buildSummary(Collection $reports): array
    {
        $active = $reports->where('archived', false);
        $threeDaysAgo = Carbon::now('Asia/Jakarta')->subDays(3)->toDateString();

        return [
            'unread' => $active->where('status', 'belum_dibaca')->count(),
            'handling' => $active->whereIn('status', ['dibaca', 'ditindaklanjuti'])->count(),
            'completed' => $active->where('status', 'selesai')->count(),
            'danger' => $active->where('status', 'belum_dibaca')->where('danger_flag', true)->count(),
            'overdue' => $active->where('status', '!=', 'selesai')->where('status_date', '<=', $threeDaysAgo)->count(),
        ];
    }

    public function dashboard(): View
    {
        $all = $this->visibleReportsCollection();
        $active = $all->where('archived', false)->values();

        return $this->page('dashboard', ['reports' => $active], $all);
    }

    public function report(int $report): View
    {
        $model = StudentReport::findOrFail($report);
        Gate::authorize('viewAsKesiswaan', $model);

        AuditLogger::log(Auth::user(), 'view_report', $model);

        $all = $this->visibleReportsCollection();

        return $this->page('report', ['report' => $this->toSafeDto($model)], $all);
    }

    public function updateStatus(Request $request, int $report): JsonResponse|RedirectResponse
    {
        $model = StudentReport::findOrFail($report);
        Gate::authorize('manageAsKesiswaan', $model);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:belum_dibaca,dibaca,ditindaklanjuti,selesai'],
        ]);

        $model->update([
            'status' => $validated['status'],
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'update_status', $model, "Status: {$model->status}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status laporan berhasil diperbarui.']);
        }

        return back()->with('status', 'Status laporan berhasil diperbarui.');
    }

    public function archiveReport(Request $request, int $report): JsonResponse|RedirectResponse
    {
        $model = StudentReport::findOrFail($report);
        Gate::authorize('manageAsKesiswaan', $model);

        $reason = $request->input('archive_reason', 'selesai');
        if (! in_array($reason, ['selesai', 'iseng'], true)) {
            $reason = 'selesai';
        }

        $model->update([
            'status' => $reason === 'selesai' ? 'selesai' : $model->status,
            'archived_at' => now(),
            'archive_reason' => $reason,
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'archive_report', $model, "Alasan: {$reason}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Laporan berhasil diarsipkan.']);
        }

        return redirect()->route('kesiswaan.dashboard')->with('status', 'Laporan berhasil diarsipkan.');
    }

    public function markIseng(Request $request, int $report): JsonResponse|RedirectResponse
    {
        $model = StudentReport::findOrFail($report);
        Gate::authorize('manageAsKesiswaan', $model);

        $model->update([
            'archived_at' => now(),
            'archive_reason' => 'iseng',
            'status_updated_date' => now('Asia/Jakarta')->toDateString(),
        ]);

        AuditLogger::log(Auth::user(), 'mark_iseng', $model);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Laporan ditandai iseng dan dipindahkan ke arsip.']);
        }

        return redirect()->route('kesiswaan.dashboard')->with('status', 'Laporan ditandai iseng.');
    }

    public function bulkMarkIseng(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $targets = StudentReport::production()
            ->whereIn('id', $validated['ids'])
            ->get();

        $count = 0;
        $today = now('Asia/Jakarta')->toDateString();

        foreach ($targets as $target) {
            if (! Gate::allows('manageAsKesiswaan', $target)) {
                continue;
            }

            $target->update([
                'archived_at' => now(),
                'archive_reason' => 'iseng',
                'status_updated_date' => $today,
            ]);

            AuditLogger::log(Auth::user(), 'mark_iseng', $target, 'Bulk action Kesiswaan');
            $count++;
        }

        $message = "{$count} laporan berhasil ditandai iseng dan dipindahkan ke arsip.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return redirect()->route('kesiswaan.dashboard')->with('status', $message);
    }

    public function archive(): View
    {
        $all = $this->visibleReportsCollection();
        $archived = $all->where('archived', true)->values();

        return $this->page('archive', ['reports' => $archived], $all);
    }

    public function statistics(): View
    {
        $reports = $this->visibleReportsCollection();
        $total = $reports->count();
        $completed = $reports->filter(fn (array $r) => $r['status'] === 'selesai' || $r['archive_reason'] === 'selesai')->count();

        $statistics = [
            'total' => $total,
            'active' => $reports->where('archived', false)->count(),
            'completed' => $completed,
            'rate' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'types' => $reports->countBy('type')->all(),
            'statuses' => [
                'belum_dibaca' => $reports->where('status', 'belum_dibaca')->count(),
                'dibaca' => $reports->where('status', 'dibaca')->count(),
                'ditindaklanjuti' => $reports->where('status', 'ditindaklanjuti')->count(),
                'selesai' => $reports->where('status', 'selesai')->count(),
                'iseng' => $reports->where('archive_reason', 'iseng')->count(),
            ],
            'months' => $reports->groupBy(fn (array $r) => substr($r['date'], 0, 7))->map->count()->all(),
        ];

        return $this->page('statistics', ['statistics' => $statistics], $reports);
    }

    public function accessLog(): View
    {
        $all = $this->visibleReportsCollection();
        $allowedIds = StudentReport::production()
            ->whereIn('category', $this->allowedCategoryLabels())
            ->pluck('id')
            ->flip();

        $logs = AccessLog::with(['user', 'report'])
            ->orderByDesc('id')
            ->limit(150)
            ->get()
            ->map(function (AccessLog $log) use ($allowedIds): array {
                $ticket = '-';
                if ($log->student_report_id) {
                    $ticket = ($log->report && $allowedIds->has($log->student_report_id))
                        ? '#'.$log->report->case_label
                        : 'Laporan lain';
                }

                return [
                    'time' => $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s').' WIB' : '-',
                    'user' => "{$log->user_name} ({$log->role_label})",
                    'action' => $log->action_label,
                    'ticket' => $ticket,
                    'device' => trim(($log->ip_address ?: '-').($log->user_agent ? ' · '.Str::limit($log->user_agent, 40) : '')),
                ];
            });

        return $this->page('access-log', ['logs' => $logs], $all);
    }

    public function profile(): View
    {
        $all = $this->visibleReportsCollection();
        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');

        return $this->page('profile', [
            'user' => Auth::user(),
            'schoolName' => $schoolName,
        ], $all);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('status', 'Kata sandi berhasil diperbarui.');
    }

    private function page(string $name, array $data = [], ?Collection $all = null): View
    {
        $reports = $all ?? $this->visibleReportsCollection();
        $types = IncidentCategory::where('visible_to_kesiswaan', true)
            ->orderBy('order_index')
            ->get()
            ->mapWithKeys(fn (IncidentCategory $cat) => [$this->normalizeType($cat->label) => $cat->label])
            ->all();

        return view('kesiswaan.'.$name, $data + [
            'summary' => $this->buildSummary($reports),
            'types' => $types,
        ]);
    }

    private function normalizeType(string $label): string
    {
        $c = strtolower($label);
        if (str_contains($c, 'fisik')) {
            return 'fisik';
        }
        if (str_contains($c, 'verbal') || str_contains($c, 'ejekan')) {
            return 'verbal';
        }
        if (str_contains($c, 'kucil') || str_contains($c, 'pengucilan')) {
            return 'dikucilkan';
        }
        if (str_contains($c, 'cyber')) {
            return 'cyber';
        }
        if (str_contains($c, 'pelecehan')) {
            return 'pelecehan';
        }

        return 'lainnya';
    }
}
