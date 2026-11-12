<?php

namespace App\Http\Controllers;

use App\Models\StaffMember;
use App\Models\User;
use App\Support\WaliKelasPreview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class WaliKelasPreviewController extends Controller
{
    public function __construct(private readonly WaliKelasPreview $preview) {}

    public function dashboard(): View
    {
        return $this->page('dashboard');
    }

    public function summary(int $summary): View
    {
        $item = $this->preview->findSummary($summary);
        return $this->page('summary', ['summary' => $item]);
    }

    /**
     * Mengubah status baca satu ringkasan (Toggle Read / Unread).
     */
    public function toggleRead(Request $request, int $summary): JsonResponse|RedirectResponse
    {
        $updated = $this->preview->toggleRead($summary);
        $summaries = $this->preview->summaries();
        $unreadCount = $summaries->where('status', 'unread')->count();

        $message = $updated['status'] === 'read'
            ? 'Ringkasan berhasil ditandai telah dibaca.'
            : 'Ringkasan dikembalikan ke status belum dibaca.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'     => true,
                'message'     => $message,
                'status'      => $updated['status'],
                'read_at'     => $updated['read_at'],
                'unreadCount' => $unreadCount,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Menandai semua notifikasi / ringkasan sebagai telah dibaca.
     */
    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $this->preview->markAllAsRead();

        $message = 'Seluruh ringkasan telah ditandai dibaca.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'     => true,
                'message'     => $message,
                'unreadCount' => 0,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Mencetak poster resmi QR pengaduan untuk ditempel di ruang kelas.
     */
    public function downloadPoster(): View
    {
        $schoolName = Auth::user()?->school_name ?? 'SMP Telkom Purwokerto';
        $className = $this->preview->getTeacherClass();

        return view('bk.poster-print', [
            'schoolName' => $schoolName . ' — Kelas ' . $className,
            'reportUrl'  => url('/s/lapor'),
            'qrScanUrl'  => url('/s/qr'),
        ]);
    }

    /**
     * Menampilkan dokumen cetak PDF disposisi konseling kelas.
     */
    public function downloadPdf(int $summary): View
    {
        $item = $this->preview->findSummary($summary);
        $schoolName = Auth::user()?->school_name ?? 'SMP Telkom Purwokerto';
        $className = $this->preview->getTeacherClass();

        return view('walas.summary-print', [
            'summary'    => $item,
            'schoolName' => $schoolName,
            'className'  => $className,
        ]);
    }

    /**
     * Halaman profil & keamanan akun Wali Kelas.
     */
    public function profile(): View
    {
        return $this->page('profile');
    }

    /**
     * Memperbarui profil dinas (Nama Tampilan).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ], [
            'name.required' => 'Nama lengkap dan gelar wajib diisi.',
            'name.max'      => 'Nama maksimal 120 karakter.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->name = $request->input('name');
            $user->save();

            try {
                if (Schema::hasTable('staff_members')) {
                    StaffMember::where('email', $user->email)->update(['name' => $user->name]);
                }
            } catch (\Throwable $e) {}
        }

        return back()->with('status', 'Nama lengkap dan data profil dinas berhasil disimpan.');
    }

    /**
     * Memperbarui kata sandi akun Wali Kelas.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok dengan data kami.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return back()->with('status', 'Kata sandi akun Anda berhasil diperbarui dengan aman.');
    }

    private function page(string $page, array $data = []): View
    {
        $summaries = $this->preview->summaries();
        $className = $this->preview->getTeacherClass();
        $schoolName = Auth::user()?->school_name ?? 'SMP Telkom Purwokerto';

        return view('walas.'.$page, $data + [
            'summaries'     => $summaries,
            'unreadCount'   => $summaries->where('status', 'unread')->count(),
            'readCount'     => $summaries->where('status', 'read')->count(),
            'notifications' => $summaries->where('status', 'unread')->values(),
            'className'     => $className,
            'schoolName'    => $schoolName,
            'reportUrl'     => url('/s/lapor'),
            'qrScanUrl'     => url('/s/qr'),
        ]);
    }
}
