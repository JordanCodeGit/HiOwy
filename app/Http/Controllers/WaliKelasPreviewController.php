<?php

namespace App\Http\Controllers;

use App\Models\Forward;
use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Support\AcademicYearService;
use App\Support\AuditLogger;
use App\Support\SchoolConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class WaliKelasPreviewController extends Controller
{
    private function teacherClass(): string
    {
        if (Auth::check() && Schema::hasTable('staff_members')) {
            $staff = DB::table('staff_members')
                ->where('email', strtolower((string) Auth::user()->email))
                ->first();

            if ($staff && ! empty($staff->class_name)) {
                return $staff->class_name;
            }
        }

        return '-';
    }

    /**
     * Proyeksikan Forward DB menjadi format tampilan Wali Kelas tanpa membocorkan identitas pelapor.
     *
     * @return array<string, mixed>
     */
    private function toSummaryArray(Forward $forward): array
    {
        $isRead = $forward->read_at !== null;
        $counselorName = $forward->fromUser?->name ?? 'Guru BK';

        return [
            'id' => $forward->id,
            'counselor' => $counselorName,
            'role' => 'Konselor BK',
            'time' => $forward->forwarded_at ? $forward->forwarded_at->translatedFormat('d M Y, H:i').' WIB' : '-',
            'status' => $isRead ? 'read' : 'unread',
            'content' => $forward->summary,
            'excerpt' => Str::limit($forward->summary, 140),
            'read_at' => $isRead ? $forward->read_at->translatedFormat('d M Y, H:i').' WIB' : null,
            'detail' => $forward->summary,
        ];
    }

    private function userSummaries(): Collection
    {
        return AcademicYearService::applyToForwards(
            Forward::with('fromUser')
                ->where('to_user_id', Auth::id())
                ->whereHas('report', fn ($q) => $q->where('is_training', false))
        )
            ->orderByDesc('id')
            ->get()
            ->map(fn (Forward $f) => $this->toSummaryArray($f));
    }

    public function dashboard(): View
    {
        return $this->page('dashboard');
    }

    public function summary(int $summary): View
    {
        $forward = Forward::with(['fromUser', 'report'])->findOrFail($summary);
        Gate::authorize('view', $forward);

        AuditLogger::log(Auth::user(), 'view_forward', $forward->report);

        return $this->page('summary', ['summary' => $this->toSummaryArray($forward)]);
    }

    public function toggleRead(Request $request, int $summary): JsonResponse|RedirectResponse
    {
        $forward = Forward::with(['fromUser', 'report'])->findOrFail($summary);
        Gate::authorize('markRead', $forward);

        $forward->read_at = $forward->read_at ? null : now();
        $forward->save();

        AuditLogger::log(
            Auth::user(),
            'toggle_forward_read',
            $forward->report,
            $forward->read_at ? 'Dibaca' : 'Belum dibaca'
        );

        $unreadCount = AcademicYearService::applyToForwards(
            Forward::where('to_user_id', Auth::id())
                ->whereNull('read_at')
                ->whereHas('report', fn ($q) => $q->where('is_training', false))
        )->count();
        $isRead = $forward->read_at !== null;

        $message = $isRead
            ? 'Ringkasan berhasil ditandai telah dibaca.'
            : 'Ringkasan dikembalikan ke status belum dibaca.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $isRead ? 'read' : 'unread',
                'read_at' => $forward->read_at?->translatedFormat('d M Y, H:i').($isRead ? ' WIB' : ''),
                'unreadCount' => $unreadCount,
            ]);
        }

        return back()->with('status', $message);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        AcademicYearService::applyToForwards(
            Forward::where('to_user_id', Auth::id())
                ->whereNull('read_at')
                ->whereHas('report', fn ($q) => $q->where('is_training', false))
        )->update(['read_at' => now()]);

        $message = 'Seluruh ringkasan telah ditandai dibaca.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'unreadCount' => 0,
            ]);
        }

        return back()->with('status', $message);
    }

    public function downloadPoster(): View
    {
        $config = SchoolConfigService::getConfig();
        $schoolName = $config['school_name'];
        $className = $this->teacherClass();
        $reportUrl = url('/s/lapor');

        return view('bk.poster-print', [
            'schoolName' => $schoolName.($className !== '-' ? ' — Kelas '.$className : ''),
            'schoolConfig' => $config,
            'reportUrl' => $reportUrl,
            'qrScanUrl' => $reportUrl,
            'backUrl' => route('walas.dashboard'),
            'backLabel' => 'Kembali ke Dashboard Wali Kelas',
        ]);
    }

    public function downloadPdf(int $summary): View
    {
        $forward = Forward::with('fromUser')->findOrFail($summary);
        Gate::authorize('view', $forward);

        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $className = $this->teacherClass();

        return view('walas.summary-print', [
            'summary' => $this->toSummaryArray($forward),
            'schoolName' => $schoolName,
            'className' => $className,
        ]);
    }

    public function profile(): View
    {
        return $this->page('profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ], [
            'name.required' => 'Nama lengkap dan gelar wajib diisi.',
            'name.max' => 'Nama maksimal 120 karakter.',
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        if (Schema::hasTable('staff_members')) {
            StaffMember::where('email', strtolower($user->email))->update(['name' => $user->name]);
        }

        return back()->with('status', 'Nama lengkap dan data profil dinas berhasil disimpan.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok dengan data kami.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi minimal 10 karakter.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('status', 'Kata sandi akun Anda berhasil diperbarui dengan aman.');
    }

    private function page(string $page, array $data = []): View
    {
        $summaries = $this->userSummaries();
        $className = $this->teacherClass();
        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $reportUrl = url('/s/lapor');

        return view('walas.'.$page, $data + [
            'summaries' => $summaries,
            'unreadCount' => $summaries->where('status', 'unread')->count(),
            'readCount' => $summaries->where('status', 'read')->count(),
            'notifications' => $summaries->where('status', 'unread')->values(),
            'className' => $className,
            'schoolName' => $schoolName,
            'reportUrl' => $reportUrl,
            'qrScanUrl' => $reportUrl,
        ]);
    }
}
