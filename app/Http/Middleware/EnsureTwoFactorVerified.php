<?php

namespace App\Http\Middleware;

use App\Models\StaffMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Ensure the authenticated user is active and has completed 2FA setup & verification.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Autentikasi diperlukan.'], 401)
                : redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if (Schema::hasTable('staff_members')
            && StaffMember::where('email', strtolower((string) $user->email))->value('status') === 'nonaktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Akun Anda telah dinonaktifkan oleh Admin Sekolah.'], 403)
                : redirect()->route('login')->withErrors(['email' => 'Akun Anda sedang dinonaktifkan oleh Admin Sekolah.']);
        }

        if (empty($user->two_factor_secret) || empty($user->two_factor_confirmed_at)) {
            $userId = $user->id;
            $userEmail = $user->email;

            Auth::logout();
            $request->session()->put([
                'auth.2fa.user_id' => $userId,
                'auth.2fa.email' => $userEmail,
                'auth.2fa.remember' => false,
                'auth.2fa.expires_at' => now()->addMinutes(15)->timestamp,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Verifikasi 2FA wajib diselesaikan.'], 403);
            }

            return redirect()->route('two-factor.setup')
                ->withErrors(['code' => 'Anda wajib mengaktifkan dan memverifikasi 2FA terlebih dahulu.']);
        }

        return $next($request);
    }
}
