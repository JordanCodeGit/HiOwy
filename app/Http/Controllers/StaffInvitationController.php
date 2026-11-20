<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\StaffMember;
use App\Models\User;
use App\Support\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffInvitationController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    /**
     * Tampilkan halaman penerimaan undangan staf dari tautan bertanda tangan.
     */
    public function show(Request $request, StaffMember $staff): View|RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($staff->status === 'nonaktif') {
            abort(403, 'Undangan akses staf ini telah dinonaktifkan oleh admin sekolah.');
        }

        $email = strtolower(trim($staff->email));
        $existingUser = User::where('email', $email)->first();

        if ($existingUser && ! empty($existingUser->two_factor_confirmed_at)) {
            return redirect()->route('login')
                ->with('status', 'Akun staf Anda sudah aktif. Silakan masuk dengan email dan kata sandi Anda.');
        }

        $schoolName = SchoolSetting::get('school_name', 'SMP Telkom Purwokerto');
        $acceptUrl = URL::temporarySignedRoute(
            'staff.invitation.accept',
            now()->addHour(),
            ['staff' => $staff->id]
        );

        return view('auth.accept-invitation', [
            'staff' => $staff,
            'schoolName' => $schoolName,
            'acceptUrl' => $acceptUrl,
        ]);
    }

    /**
     * Proses pembuatan kata sandi staf undangan lalu lanjut ke aktivasi 2FA.
     */
    public function accept(Request $request, StaffMember $staff): RedirectResponse
    {
        if ($staff->status === 'nonaktif') {
            abort(403, 'Undangan akses staf ini telah dinonaktifkan oleh admin sekolah.');
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)->mixedCase()->numbers()->symbols(),
            ],
            'terms' => ['required', 'accepted'],
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 10 karakter.',
            'password.mixed' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Kata sandi harus mengandung minimal 1 angka.',
            'password.symbols' => 'Kata sandi harus mengandung minimal 1 simbol khusus.',
            'terms.required' => 'Anda harus menyetujui Ketentuan Layanan.',
            'terms.accepted' => 'Anda harus menyetujui Ketentuan Layanan.',
        ]);

        $role = match (strtolower((string) $staff->role)) {
            'walikelas', 'wali_kelas', 'walas' => 'wali_kelas',
            'kesiswaan' => 'kesiswaan',
            'bk' => 'bk',
            default => null,
        };

        if ($role === null) {
            abort(403, 'Peran undangan tidak valid.');
        }

        $email = strtolower(trim($staff->email));
        $existingUser = User::where('email', $email)->first();

        if ($existingUser && ! empty($existingUser->two_factor_confirmed_at)) {
            return redirect()->route('login')
                ->with('status', 'Akun staf Anda sudah aktif. Silakan masuk.');
        }

        $name = trim((string) ($validated['name'] ?? '')) ?: $staff->name;
        $secret = $this->totp->generateSecret();
        $recoveryCodes = $this->totp->generateRecoveryCodes();

        if ($existingUser) {
            $existingUser->name = $name;
            $existingUser->password = Hash::make($validated['password']);
            $existingUser->role = $role;
            $existingUser->two_factor_secret = $secret;
            $existingUser->two_factor_recovery_codes = $recoveryCodes;
            $existingUser->two_factor_confirmed_at = null;
            $existingUser->save();
            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'two_factor_secret' => $secret,
                'two_factor_recovery_codes' => $recoveryCodes,
                'two_factor_confirmed_at' => null,
            ]);
        }

        $staff->status = 'aktif';
        $staff->name = $name;
        $staff->save();

        $request->session()->put([
            'auth.2fa.user_id' => $user->id,
            'auth.2fa.email' => $user->email,
            'auth.2fa.remember' => false,
            'auth.2fa.is_first_time' => true,
            'auth.2fa.expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('two-factor.setup');
    }
}
