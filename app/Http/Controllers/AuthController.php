<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorSetupMail;
use App\Models\User;
use App\Support\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    /**
     * Show login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle initial credential check (email & password).
     * No rate limiting is applied per project instructions.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email admin wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Email atau kata sandi tidak cocok. Silakan coba kembali.',
                ]);
        }

        // Ensure user has a TOTP secret configured
        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = $this->totp->generateSecret();
            $user->two_factor_recovery_codes = json_encode($this->totp->generateRecoveryCodes());
            $user->save();
        }

        $isFirstTime = empty($user->two_factor_confirmed_at);

        // Store temporary authentication state awaiting 2FA challenge / setup
        $request->session()->put([
            'auth.2fa.user_id' => $user->id,
            'auth.2fa.email' => $user->email,
            'auth.2fa.remember' => $request->boolean('remember'),
            'auth.2fa.is_first_time' => $isFirstTime,
            'auth.2fa.expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('two-factor.setup');
    }

    /**
     * Tampilkan halaman 2FA (halaman tunggal dengan QR Code dan verifikasi kode 6-digit).
     */
    public function showTwoFactorSetup(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $expiresAt = $request->session()->get('auth.2fa.expires_at', 0);

        if (! $userId || now()->timestamp > $expiresAt) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi 2FA telah kedaluwarsa. Silakan masuk kembali.',
            ]);
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = $this->totp->generateSecret();
            $user->two_factor_recovery_codes = json_encode($this->totp->generateRecoveryCodes());
            $user->save();
        }

        $qrCodeUrl = $this->totp->getQrCodeUrl($user->email, $user->two_factor_secret, 'HiOwy', 60);
        $recoveryCodes = $user->recoveryCodes();

        return view('auth.setup-two-factor', [
            'user'          => $user,
            'secret'        => $user->two_factor_secret,
            'qrCodeUrl'     => $qrCodeUrl,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Konfirmasi verifikasi 2FA setelah pengguna memasukkan 6 digit kode.
     */
    public function confirmTwoFactorSetup(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $expiresAt = $request->session()->get('auth.2fa.expires_at', 0);

        if (! $userId || now()->timestamp > $expiresAt) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi 2FA telah kedaluwarsa. Silakan masuk kembali.',
            ]);
        }

        $user = User::findOrFail($userId);

        if ($request->filled('recovery_code')) {
            $recoveryCode = trim((string) $request->input('recovery_code'));
            if ($user->consumeRecoveryCode($recoveryCode)) {
                $user->two_factor_confirmed_at = now();
                $user->save();

                return $this->finalizeLogin($request, $user);
            }
        }

        $rawCode = (string) $request->input('code', '');
        $code = preg_replace('/\D/', '', $rawCode);

        if (strlen($code) !== 6) {
            return back()->withErrors([
                'code' => 'Masukkan 6 digit kode dari aplikasi autentikator Anda.',
            ]);
        }

        $valid = $this->totp->verify($user->two_factor_secret, $code, 60);

        if (! $valid) {
            return back()->withErrors([
                'code' => 'Kode salah atau sudah kedaluwarsa. Pastikan waktu jam perangkat Anda akurat.',
            ]);
        }

        // Tandai 2FA sudah aktif/terverifikasi
        $user->two_factor_confirmed_at = now();
        $user->save();

        return $this->finalizeLogin($request, $user);
    }

    /**
     * Jalur lama verifikasi diarahkan ke halaman 2FA utama.
     */
    public function showTwoFactorVerify(Request $request): RedirectResponse
    {
        return redirect()->route('two-factor.setup');
    }

    /**
     * Display activation page opened from Gmail link.
     */
    public function showTwoFactorActivate(Request $request, User $user): View
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan aktivasi tidak valid atau telah kedaluwarsa.');
        }

        $otpauthUri = $this->totp->getOtpAuthUri($user->email, $user->two_factor_secret, 'HiOwy', 60);
        $qrCodeUrl = $this->totp->getQrCodeUrl($user->email, $user->two_factor_secret, 'HiOwy', 60);

        return view('auth.two-factor-activate', [
            'user' => $user,
            'otpauthUri' => $otpauthUri,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Jalur lama submit diarahkan ke konfirmasi 2FA.
     */
    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        return $this->confirmTwoFactorSetup($request);
    }

    /**
     * Finalize login, mark 2FA confirmed on first time, regenerate session, and redirect.
     */
    private function finalizeLogin(Request $request, User $user): RedirectResponse
    {
        $remember = (bool) $request->session()->get('auth.2fa.remember', false);

        // Mark 2FA as confirmed on first successful verification
        if (empty($user->two_factor_confirmed_at)) {
            $user->two_factor_confirmed_at = now();
            $user->save();
        }

        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.email',
            'auth.2fa.remember',
            'auth.2fa.is_first_time',
            'auth.2fa.is_google',
            'auth.2fa.activation_url',
            'auth.2fa.expires_at',
        ]);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user according to role.
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'kesiswaan' => redirect()->intended(route('kesiswaan.dashboard')),
            'wali_kelas', 'walas' => redirect()->intended(route('walas.dashboard')),
            default => redirect()->intended(route('bk.dashboard')),
        };
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari akun.');
    }
}
