<?php

namespace App\Http\Controllers;

use App\Models\StaffMember;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\TotpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    /**
     * Show login form.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $rememberedEmail = $request->cookie('hiowy_remember_email');
        $isRemembered = $request->hasCookie('hiowy_remember_email');

        return view('auth.login', [
            'rememberedEmail' => $rememberedEmail,
            'isRemembered' => $isRemembered,
        ]);
    }

    /**
     * Handle initial credential check (email & password) with lockout after 5 failures.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email staf wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $email = strtolower(trim($credentials['email']));
        $throttleKey = 'login-attempt:'.sha1($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = max(1, (int) ceil($seconds / 60));

            if (class_exists(AuditLogger::class)) {
                AuditLogger::logSystem('login_locked', $email);
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan gagal. Akun dikunci sementara selama {$minutes} menit.",
                ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 900);

            if (class_exists(AuditLogger::class)) {
                AuditLogger::logSystem('login_failed', $email);
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Email atau kata sandi tidak cocok. Silakan coba kembali.',
                ]);
        }

        if ($user->normalizedRole() === '') {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Akun Anda belum memiliki peran akses yang sah. Hubungi Koordinator BK.',
                ]);
        }

        RateLimiter::clear($throttleKey);

        // Ensure user has a TOTP secret configured
        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = $this->totp->generateSecret();
            $user->two_factor_recovery_codes = $this->totp->generateRecoveryCodes();
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
     * Tampilkan halaman 2FA (QR Code + verifikasi kode 6-digit).
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
            $user->two_factor_recovery_codes = $this->totp->generateRecoveryCodes();
            $user->save();
        }

        $qrCodeUrl = $this->totp->getQrCodeUrl($user->email, $user->two_factor_secret, 'HiOwy', 60);
        $recoveryCodes = $user->recoveryCodes();

        return view('auth.setup-two-factor', [
            'user' => $user,
            'secret' => $user->two_factor_secret,
            'qrCodeUrl' => $qrCodeUrl,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Cek validitas 6 digit kode TOTP secara real-time (AJAX) sebelum submit form.
     */
    public function checkTwoFactorCode(Request $request): JsonResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $expiresAt = $request->session()->get('auth.2fa.expires_at', 0);

        if (! $userId || now()->timestamp > $expiresAt) {
            return response()->json(['valid' => false, 'message' => 'Sesi 2FA kedaluwarsa.'], 401);
        }

        $user = User::find($userId);
        if (! $user || empty($user->two_factor_secret)) {
            return response()->json(['valid' => false], 404);
        }

        $rawCode = (string) $request->input('code', '');
        $code = preg_replace('/\D/', '', $rawCode);

        if (strlen($code) !== 6) {
            return response()->json(['valid' => false]);
        }

        $valid = $this->totp->verify($user->two_factor_secret, $code, 60);

        return response()->json(['valid' => $valid]);
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
            if (class_exists(AuditLogger::class)) {
                AuditLogger::logSystem('2fa_failed', $user->email);
            }

            return back()->withErrors([
                'code' => 'Kode salah atau sudah kedaluwarsa. Pastikan waktu jam perangkat Anda akurat.',
            ]);
        }

        $user->two_factor_confirmed_at = now();
        $user->save();

        return $this->finalizeLogin($request, $user);
    }

    /**
     * Jalur lama verifikasi diarahkan ke halaman 2FA utama.
     */
    public function showTwoFactorVerify(): RedirectResponse
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
     * Finalize login, mark 2FA confirmed, sync StaffMember status, regenerate session, and redirect.
     */
    private function finalizeLogin(Request $request, User $user): RedirectResponse
    {
        $remember = (bool) $request->session()->get('auth.2fa.remember', false);

        if (empty($user->two_factor_confirmed_at)) {
            $user->two_factor_confirmed_at = now();
            $user->save();
        }

        if (Schema::hasTable('staff_members')) {
            StaffMember::where('email', strtolower($user->email))
                ->where('status', '!=', 'nonaktif')
                ->update(['status' => 'aktif']);
        }

        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.email',
            'auth.2fa.remember',
            'auth.2fa.is_first_time',
            'auth.2fa.activation_url',
            'auth.2fa.expires_at',
        ]);

        // Never persist auto-login remember token so 30-min session idle timeout is enforced.
        Auth::login($user, false);
        $request->session()->regenerate();

        if (class_exists(AuditLogger::class)) {
            AuditLogger::log($user, 'login_success');
        }

        $response = $this->redirectBasedOnRole($user);

        if ($remember) {
            $response->withCookie(cookie()->make('hiowy_remember_email', $user->email, 525600, null, null, false, false));
        } else {
            $response->withoutCookie('hiowy_remember_email');
        }

        return $response;
    }

    /**
     * Redirect user strictly according to validated role.
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        $role = $user->normalizedRole();

        if (! in_array($role, ['bk', 'kesiswaan', 'walas'], true)) {
            Auth::logout();
            abort(403, 'Peran akun tidak valid.');
        }

        $targetRoute = match ($role) {
            'kesiswaan' => route('kesiswaan.dashboard'),
            'walas' => route('walas.dashboard'),
            'bk' => route('bk.dashboard'),
        };

        $intended = session()->get('url.intended');
        if ($intended) {
            if ($role === 'kesiswaan' && ! str_contains($intended, '/kesiswaan')) {
                session()->forget('url.intended');

                return redirect($targetRoute);
            }
            if ($role === 'walas' && ! str_contains($intended, '/wali-kelas')) {
                session()->forget('url.intended');

                return redirect($targetRoute);
            }
            if ($role === 'bk' && (str_contains($intended, '/wali-kelas') || str_contains($intended, '/kesiswaan'))) {
                session()->forget('url.intended');

                return redirect($targetRoute);
            }
        }

        return redirect()->intended($targetRoute);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check() && class_exists(AuditLogger::class)) {
            AuditLogger::log(Auth::user(), 'logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari akun.');
    }
}
