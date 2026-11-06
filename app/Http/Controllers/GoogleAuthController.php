<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorSetupMail;
use App\Models\User;
use App\Support\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    /**
     * Redirect user ke Google OAuth 2.0 consent screen.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = url(config('services.google.redirect', '/auth/google/callback'));

        if (empty($clientId)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Integrasi Google belum aktif: GOOGLE_CLIENT_ID belum diisi di file .env.',
            ]);
        }

        $state = Str::random(40);
        session()->put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid profile email',
            'access_type'   => 'online',
            'state'         => $state,
            'prompt'        => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    /**
     * Handle Google OAuth 2.0 callback.
     * Menerima authorization code, menukarnya dengan access token,
     * mengambil data profil user, lalu menghubungkan/membuat akun dan masuk ke flow 2FA.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Autentikasi Google dibatalkan: ' . $request->input('error'),
            ]);
        }

        $code = $request->query('code');
        $state = $request->query('state');
        $savedState = session()->pull('google_oauth_state');

        if (! $code || ! $state || $state !== $savedState) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi Google OAuth kedaluwarsa atau tidak valid. Silakan coba kembali.',
            ]);
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = url(config('services.google.redirect', '/auth/google/callback'));

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google Client Secret belum lengkap di file .env.',
            ]);
        }

        // Tukar authorization code dengan token
        try {
            $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]);
        } catch (\Throwable $e) {
            Log::error('Google OAuth token request exception: ' . $e->getMessage());

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal terhubung ke server Google untuk verifikasi token.',
            ]);
        }

        if (! $tokenResponse->successful()) {
            Log::warning('Google OAuth token exchange gagal: ' . $tokenResponse->body());

            return redirect()->route('login')->withErrors([
                'email' => 'Verifikasi Google OAuth gagal. Pastikan Redirect URI di Google Console cocok dengan ' . $redirectUri,
            ]);
        }

        $accessToken = $tokenResponse->json('access_token');

        // Ambil info profil user dari Google UserInfo API
        try {
            $userResponse = Http::withToken($accessToken)->timeout(10)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        } catch (\Throwable $e) {
            Log::error('Google OAuth userinfo exception: ' . $e->getMessage());

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mengambil data profil dari Google.',
            ]);
        }

        if (! $userResponse->successful()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal membaca profil akun Google Anda.',
            ]);
        }

        $googleData = $userResponse->json();
        $googleId   = $googleData['sub'] ?? null;
        $email      = $googleData['email'] ?? null;
        $name       = $googleData['name'] ?? 'Pengguna Google';

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google Anda tidak menyediakan alamat email.',
            ]);
        }

        // Cari user yang sudah terdaftar via google_id atau email
        $user = User::where('google_id', $googleId)->first()
            ?? User::where('email', $email)->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->google_id = $googleId;
                $user->save();
            }
        } else {
            // Registrasi akun baru via Google
            $user = User::create([
                'name'        => $name,
                'email'       => $email,
                'password'    => Hash::make(Str::random(32)),
                'google_id'   => $googleId,
                'school_name' => null,
                'role'        => 'bk',
            ]);
        }

        // Siapkan secret TOTP jika belum ada
        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = $this->totp->generateSecret();
            $user->two_factor_recovery_codes = json_encode($this->totp->generateRecoveryCodes());
            $user->save();
        }

        // Set session 2FA state
        $request->session()->put([
            'auth.2fa.user_id'    => $user->id,
            'auth.2fa.email'      => $user->email,
            'auth.2fa.remember'   => true,
            'auth.2fa.expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('two-factor.setup');
    }
}
