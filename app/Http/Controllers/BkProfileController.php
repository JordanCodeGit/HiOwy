<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class BkProfileController extends Controller
{
    /**
     * Tampilkan halaman profil & pengaturan akun BK.
     */
    public function show(): View
    {
        $user = Auth::user() ?? User::where('role', 'bk')->first() ?? User::first();

        // Ambil inisial nama untuk avatar
        $words = preg_split('/\s+/', trim($user->name ?? 'Sarah Jenkins'));
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        } else {
            $initials = strtoupper(substr($words[0] ?? 'SJ', 0, 2));
        }

        $roleLabel = match ($user->role ?? 'bk') {
            'kesiswaan' => 'Kesiswaan',
            'wali_kelas', 'walas', 'walikelas' => 'Wali Kelas',
            default => 'Guru BK / Konselor Inti',
        };

        return view('bk.profile', [
            'user'      => $user,
            'initials'  => $initials,
            'roleLabel' => $roleLabel,
        ]);
    }

    /**
     * Perbarui data profil (Nama Tampilan, Sekolah & Foto Profil).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'name.required' => 'Nama tampilan lengkap wajib diisi.',
            'name.max'      => 'Nama tampilan maksimal 255 karakter.',
            'avatar.image'  => 'Berkas harus berupa gambar.',
            'avatar.mimes'  => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'avatar.max'    => 'Ukuran foto maksimal 4MB.',
        ]);

        $user = Auth::user() ?? User::where('role', 'bk')->first() ?? User::first();

        $user->name = $request->input('name');
        if ($request->filled('school_name')) {
            $user->school_name = $request->input('school_name');
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/avatars');
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        }

        $user->save();

        return back()->with('success_profile', 'Data akun resmi berhasil diperbarui.');
    }

    /**
     * Perbarui tahun ajaran & semester aktif.
     */
    public function updatePeriod(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:50'],
            'semester'     => ['required', 'string', 'in:ganjil,genap'],
        ]);

        session([
            'academic_year'     => $request->input('tahun_ajaran'),
            'academic_semester' => $request->input('semester'),
        ]);

        return back()->with('success_period', 'Tahun ajaran & periode berjalan berhasil disimpan.');
    }

    /**
     * Perbarui kata sandi pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.mixed'            => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
            'password.numbers'          => 'Kata sandi harus mengandung minimal 1 angka.',
            'password.symbols'          => 'Kata sandi harus mengandung minimal 1 simbol khusus.',
        ]);

        $user = Auth::user() ?? User::where('role', 'bk')->first() ?? User::first();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak cocok dengan catatan kami.',
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Logout perangkat lain jika sesi autentikasi sedang berjalan
        if (Auth::check()) {
            Auth::logoutOtherDevices($request->input('password'));
        }

        return back()->with('success_password', 'Kata sandi berhasil diperbarui. Sesi di perangkat lain telah diputus demi keamanan.');
    }

    /**
     * Lengkapi data profil konselor pertama kali (nama, asal sekolah & foto profil).
     */
    public function completeProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'school_name' => ['required', 'string', 'max:255'],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'name.required'        => 'Nama lengkap & gelar wajib diisi.',
            'name.max'             => 'Nama maksimal 255 karakter.',
            'school_name.required' => 'Asal sekolah wajib diisi.',
            'school_name.max'      => 'Asal sekolah maksimal 255 karakter.',
            'avatar.image'         => 'Berkas harus berupa gambar.',
            'avatar.mimes'         => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'avatar.max'           => 'Ukuran foto maksimal 4MB.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->name = $request->input('name');
            $user->school_name = $request->input('school_name');

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/avatars');
                if (! file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $filename);
                $user->avatar = 'uploads/avatars/' . $filename;
            }

            $user->save();
        }

        return redirect()->route('bk.dashboard')->with('status', 'Profil konselor berhasil disimpan! Selamat datang di HiOwy.');
    }

    /**
     * Keluar dari seluruh sesi perangkat.
     */
    public function logoutAllDevices(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari seluruh sesi peramban dan perangkat.');
    }
}
