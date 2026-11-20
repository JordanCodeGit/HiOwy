<?php

namespace App\Policies;

use App\Models\StudentReport;
use App\Models\User;

class StudentReportPolicy
{
    public function viewAnyBk(User $user): bool
    {
        return $user->normalizedRole() === 'bk';
    }

    public function viewBk(User $user, StudentReport $report): bool
    {
        return $user->normalizedRole() === 'bk';
    }

    public function reply(User $user, StudentReport $report): bool
    {
        return $user->normalizedRole() === 'bk';
    }

    public function correctCategory(User $user, StudentReport $report): bool
    {
        return $user->normalizedRole() === 'bk';
    }

    public function forward(User $user, StudentReport $report): bool
    {
        return $user->normalizedRole() === 'bk';
    }

    public function viewAsKesiswaan(User $user, StudentReport $report): bool
    {
        if ($user->normalizedRole() !== 'kesiswaan') {
            return false;
        }

        if ($report->is_training ?? false) {
            return false;
        }

        return $report->isVisibleToKesiswaan();
    }

    public function manageAsKesiswaan(User $user, StudentReport $report): bool
    {
        return $this->viewAsKesiswaan($user, $report);
    }
}
