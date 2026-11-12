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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userRole = $user->role ?? 'bk';

        $roleAliases = [
            'walikelas' => 'walas',
            'wali_kelas' => 'walas',
            'walas' => 'walas',
            'bk' => 'bk',
            'kesiswaan' => 'kesiswaan',
        ];

        $normalizedUserRole = $roleAliases[$userRole] ?? $userRole;

        $normalizedAllowedRoles = array_map(function ($r) use ($roleAliases) {
            return $roleAliases[$r] ?? $r;
        }, $roles);

        if (! in_array($normalizedUserRole, $normalizedAllowedRoles, true)) {
            return match ($normalizedUserRole) {
                'walas' => redirect()->route('walas.dashboard'),
                'kesiswaan' => redirect()->route('kesiswaan.dashboard'),
                default => redirect()->route('bk.dashboard'),
            };
        }

        return $next($request);
    }
}
