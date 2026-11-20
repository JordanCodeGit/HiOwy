<?php

namespace App\Support;

use App\Models\AccessLog;
use App\Models\StudentReport;
use App\Models\User;
use Illuminate\Support\Str;

class AuditLogger
{
    public static function log(?User $user, string $action, ?StudentReport $report = null, ?string $details = null): void
    {
        $req = app()->runningInConsole() && ! app()->runningUnitTests() ? null : request();

        AccessLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistem',
            'user_role' => $user ? ($user->normalizedRole() ?: ($user->role ?? 'staff')) : 'system',
            'action' => $action,
            'student_report_id' => $report?->id,
            'details' => $details,
            'ip_address' => $req?->ip(),
            'user_agent' => $req ? Str::limit((string) $req->userAgent(), 250, '') : null,
            'created_at' => now(),
        ]);
    }

    public static function logSystem(string $action, string $subject, ?StudentReport $report = null, ?string $details = null): void
    {
        $req = app()->runningInConsole() && ! app()->runningUnitTests() ? null : request();

        AccessLog::create([
            'user_id' => null,
            'user_name' => Str::limit($subject, 120, ''),
            'user_role' => 'system',
            'action' => $action,
            'student_report_id' => $report?->id,
            'details' => $details,
            'ip_address' => $req?->ip(),
            'user_agent' => $req ? Str::limit((string) $req->userAgent(), 250, '') : null,
            'created_at' => now(),
        ]);
    }
}
