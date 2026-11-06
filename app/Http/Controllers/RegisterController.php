<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorSetupMail;
use App\Models\User;
use App\Support\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    /**
     * Tampilkan form pendaftaran.
     */
    public function showRegister(): View|RedirectResponse
    {
        return view('auth.signup');
    }

    /**
     * Proses pendaftaran akun baru via form web.
     */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'    => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'terms'       => ['required', 'accepted'],
        ], [
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'password.required'    => 'Kata sandi wajib diisi.',
            'password.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'         => 'Kata sandi minimal 8 karakter.',
            'terms.required'       => 'Anda harus menyetujui Ketentuan Layanan.',
            'terms.accepted'       => 'Anda harus menyetujui Ketentuan Layanan.',
        ]);

        $secret = $this->totp->generateSecret();
        $recoveryCodes = $this->totp->generateRecoveryCodes();

        $defaultName = explode('@', $data['email'])[0];

        $user = User::create([
            'name'                      => $defaultName,
            'email'                     => $data['email'],
            'school_name'               => null,
            'password'                  => Hash::make($data['password']),
            'role'                      => 'bk',
            'two_factor_secret'         => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);

        $activationUrl = URL::temporarySignedRoute(
            'two-factor.activate',
            now()->addMinutes(15),
            ['user' => $user->id]
        );

        $otpauthUri = $this->totp->getOtpAuthUri($user->email, $secret, 'HiOwy', 60);

        try {
            Mail::to($user->email)->send(new TwoFactorSetupMail($user, $activationUrl, $otpauthUri));
        } catch (\Throwable $e) {
            Log::warning('Email aktivasi 2FA (register) gagal terkirim: ' . $e->getMessage());
        }

        $request->session()->put([
            'auth.2fa.user_id'    => $user->id,
            'auth.2fa.email'      => $user->email,
            'auth.2fa.remember'   => false,
            'auth.2fa.expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('two-factor.setup');
    }
}
