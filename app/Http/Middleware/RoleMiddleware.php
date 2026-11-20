<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Silakan masuk terlebih dahulu.'], 401)
                : redirect()->guest(route('login'));
        }

        $user = Auth::user();
        $normalizedUserRole = $user->normalizedRole();

        $roleAliases = [
            'walikelas' => 'walas',
            'wali_kelas' => 'walas',
            'walas' => 'walas',
            'bk' => 'bk',
            'kesiswaan' => 'kesiswaan',
        ];

        $normalizedAllowedRoles = array_map(
            fn (string $role) => $roleAliases[strtolower($role)] ?? strtolower($role),
            $roles
        );

        if ($normalizedUserRole === '' || ! in_array($normalizedUserRole, $normalizedAllowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki izin mengakses halaman ini.'], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
