<?php

namespace App\Http\Controllers;

use App\Models\StaffMember;
use App\Models\StudentReport;
use App\Support\WaliKelasPreview;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkReportController extends Controller
{
    /**
     * Dashboard Utama Laporan BK (/bk)
     */
    public function dashboard(Request $request): View
    {
        // Ambil semua laporan yang tidak berstatus iseng untuk dashboard utama
        $reports = StudentReport::where('status', '!=', 'iseng')
            ->where('is_archived', false)
            ->orderByRaw("CASE 
                WHEN status IN ('belum_dibaca', 'unread') THEN 1 
                WHEN status IN ('sedang_diproses', 'in_progress', 'read') THEN 2 
                ELSE 3 END")
            ->orderByDesc('id')
            ->get();

        // Hitung metrik statistik
        $unreadCount = StudentReport::whereIn('status', ['belum_dibaca', 'unread'])->where('is_archived', false)->count();
        $urgentUnreadCount = StudentReport::whereIn('status', ['belum_dibaca', 'unread'])
            ->whereIn('urgency', ['Darurat', 'Urgent', 'Tinggi'])
            ->where('is_archived', false)
            ->count();
        
        // Tanpa update > 3 hari
        $threeDaysAgo = Carbon::now()->subDays(3);
        $staleCount = StudentReport::whereNotIn('status', ['selesai', 'resolved', 'iseng'])
            ->where('is_archived', false)
            ->where('updated_at', '<=', $threeDaysAgo)
            ->count();

        // Hitung per tab
        $tabBelumDibaca = $unreadCount;
        $tabSedangDitangani = StudentReport::whereIn('status', ['sedang_diproses', 'in_progress', 'read'])
            ->where('is_archived', false)
            ->count();
        $tabSelesai = StudentReport::whereIn('status', ['selesai', 'resolved'])
            ->where('is_archived', false)
            ->count();

        return view('bk.dashboard', compact(
            'reports',
            'unreadCount',
            'urgentUnreadCount',
            'staleCount',
            'tabBelumDibaca',
            'tabSedangDitangani',
            'tabSelesai'
        ));
    }

    /**
     * Tampilkan detail laporan spesifik (/bk/reports/{id})
     */
    public function show(Request $request, $id): View
    {
        $report = is_numeric($id) 
            ? StudentReport::find($id) 
            : StudentReport::where('ticket_code', $id)->first();

        if (!$report) {
            $report = StudentReport::first();
        }

        if (!$report) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Jika status masih belum_dibaca, tandai otomatis menjadi dibaca (read)
        if (in_array($report->status, ['belum_dibaca', 'unread'])) {
            $report->update(['status' => 'read']);
        }

        // Ambil daftar konselor untuk dropdown
        $counselors = [
            'Sarah Jenkins, M.Pd. (Konselor Inti)',
            'Budi Santoso, S.Psi. (Konselor Kelas 7 & 8)',
            'Ratna Kusuma, M.Psi. (Psikolog)',
        ];
        if (Auth::check() && Auth::user()->name) {
            $userName = Auth::user()->name;
            if (!in_array($userName, $counselors)) {
                array_unshift($counselors, $userName . ' (Konselor Aktif)');
            }
        }

        // Ambil daftar wali kelas untuk penerusan
        $waliKelasList = $this->getWaliKelasList();

        return view('bk.report', compact('report', 'counselors', 'waliKelasList'));
    }

    /**
     * Fallback route untuk /bk/reports/preview
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $first = StudentReport::orderByDesc('id')->first();
        if (!$first) {
            return redirect()->route('bk.dashboard')->with('status', 'Belum ada data laporan.');
        }
            
        return $this->show($request, $first->id);
    }

    /**
     * Update status, urgensi, konselor penanggung jawab
     */
    public function updateStatus(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);

        $validated = $request->validate([
            'status'             => ['nullable', 'string', 'in:unread,belum_dibaca,read,sedang_diproses,in_progress,selesai,resolved,iseng'],
            'urgency'            => ['nullable', 'string', 'in:Darurat,Urgent,Sedang,Medium,Rendah,Easy'],
            'assigned_counselor' => ['nullable', 'string', 'max:120'],
        ]);

        if (isset($validated['status'])) {
            $statusMap = [
                'unread'      => 'belum_dibaca',
                'read'        => 'sedang_diproses',
                'in_progress' => 'sedang_diproses',
                'resolved'    => 'selesai',
            ];
            $validated['status'] = $statusMap[$validated['status']] ?? $validated['status'];
        }

        if (isset($validated['urgency'])) {
            $urgencyMap = [
                'Urgent' => 'Darurat',
                'Medium' => 'Sedang',
                'Easy'   => 'Rendah',
            ];
            $validated['urgency'] = $urgencyMap[$validated['urgency']] ?? $validated['urgency'];
        }

        $report->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status dan penugasan laporan berhasil diperbarui.',
                'report'  => $report,
            ]);
        }

        return back()->with('status', 'Status dan penugasan laporan berhasil diperbarui.');
    }

    /**
     * Balasan Konselor untuk Siswa
     */
    public function storeCounselorReply(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);

        $validated = $request->validate([
            'counselor_reply' => ['required', 'string', 'max:2000'],
        ]);

        $report->update([
            'counselor_reply'      => $validated['counselor_reply'],
            'counselor_replied_at' => now(),
            'status'               => in_array($report->status, ['belum_dibaca', 'unread']) ? 'sedang_diproses' : $report->status,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan resmi konselor berhasil dikirimkan.',
                'reply'   => $report->counselor_reply,
                'time'    => now()->format('H:i') . ' WIB',
            ]);
        }

        return back()->with('status', 'Balasan konselor berhasil dikirimkan.');
    }

    /**
     * Simpan Catatan Rahasia Antar Konselor
     */
    public function storeInternalNote(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $author = Auth::user()->name ?? 'Konselor BK';
        $time = now()->format('d M, H:i') . ' WIB';
        $newNoteEntry = "[{$time}] {$author}: " . $validated['note'];

        $existing = $report->internal_notes ? $report->internal_notes . "\n\n" : '';
        $report->update([
            'internal_notes' => $existing . $newNoteEntry,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'message'        => 'Catatan rahasia berhasil disimpan.',
                'internal_notes' => $report->internal_notes,
            ]);
        }

        return back()->with('status', 'Catatan rahasia berhasil disimpan.');
    }

    /**
     * Teruskan Ringkasan Aman ke Wali Kelas
     */
    public function forwardToWalas(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);

        $validated = $request->validate([
            'summary'     => ['required', 'string', 'max:2000'],
            'walas_class' => ['nullable', 'string', 'max:32'],
        ]);

        $targetClass = $validated['walas_class'] ?: ($report->reporter_class ?: '7B');

        // Tambahkan ke sistem WaliKelasPreview
        $walasService = app(WaliKelasPreview::class);
        $allSummaries = $walasService->all();
        
        $newId = count($allSummaries) > 0 ? (max(array_column($allSummaries, 'id')) + 1) : 1;
        $author = Auth::user()->name ?? 'Konselor BK Inti';

        $newSummary = [
            'id'        => $newId,
            'school'    => 'smakita',
            'class'     => $targetClass,
            'counselor' => $author,
            'role'      => 'Konselor BK',
            'time'      => 'Hari ini, ' . now()->format('H:i') . ' WIB',
            'status'    => 'unread',
            'content'   => $validated['summary'],
            'excerpt'   => Str::limit($validated['summary'], 140),
            'read_at'   => null,
            'detail'    => $validated['summary'],
        ];

        array_unshift($allSummaries, $newSummary);
        @file_put_contents(storage_path('framework/walas_summaries.json'), json_encode($allSummaries, JSON_PRETTY_PRINT));

        $report->update([
            'forwarded_to_walas' => true,
            'forwarded_at'       => now(),
            'notes'              => ($report->notes ? $report->notes . "\n" : "") . "Diteruskan ke Wali Kelas {$targetClass}: " . Str::limit($validated['summary'], 80),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Ringkasan aman berhasil diteruskan ke Wali Kelas {$targetClass}.",
            ]);
        }

        return back()->with('status', "Ringkasan aman berhasil diteruskan ke Wali Kelas {$targetClass}.");
    }

    /**
     * Tandai Laporan Iseng (Single)
     */
    public function markIseng(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        $report->update([
            'status' => 'iseng',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan telah ditandai sebagai laporan iseng dan dipindahkan ke arsip.',
            ]);
        }

        return redirect()->route('bk.dashboard')->with('status', 'Laporan telah ditandai sebagai laporan iseng.');
    }

    /**
     * Bulk Action: Tandai Iseng untuk beberapa laporan
     */
    public function bulkMarkIseng(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required'],
        ]);

        $ids = $validated['ids'];
        $count = StudentReport::whereIn('id', $ids)->update(['status' => 'iseng']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} laporan berhasil ditandai sebagai iseng.",
                'count'   => $count,
            ]);
        }

        return back()->with('status', "{$count} laporan berhasil ditandai sebagai iseng.");
    }

    /**
     * Arsipkan Laporan
     */
    public function archive(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = StudentReport::findOrFail($id);
        $report->update([
            'is_archived' => true,
            'status'      => 'selesai',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diarsipkan.',
            ]);
        }

        return redirect()->route('bk.dashboard')->with('status', 'Laporan berhasil diarsipkan.');
    }

    /**
     * Helper untuk mengambil daftar wali kelas
     */
    private function getWaliKelasList(): array
    {
        try {
            if (Schema::hasTable('staff_members')) {
                $staff = StaffMember::whereIn('role', ['walas', 'walikelas'])->get();
                if ($staff->count() > 0) {
                    return $staff->map(function ($s) {
                        return [
                            'name'  => $s->name,
                            'class' => $s->class_name ?: '7B',
                            'label' => "{$s->name} (Wali {$s->class_name})",
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return [];
    }
}
