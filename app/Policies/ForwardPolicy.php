<?php

namespace App\Policies;

use App\Models\Forward;
use App\Models\User;

class ForwardPolicy
{
    public function view(User $user, Forward $forward): bool
    {
        return $user->normalizedRole() === 'walas'
            && (int) $user->id === (int) $forward->to_user_id;
    }

    public function markRead(User $user, Forward $forward): bool
    {
        return $this->view($user, $forward);
    }
}
