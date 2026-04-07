<?php

namespace App\Policies;

use App\Models\Coach;
use App\Models\User;

class CoachPolicy
{
    public function view(User $user, Coach $coach): bool
    {
        return $user->school_id === $coach->school_id;
    }

    public function update(User $user, Coach $coach): bool
    {
        return $user->school_id === $coach->school_id;
    }

    public function delete(User $user, Coach $coach): bool
    {
        return $user->school_id === $coach->school_id;
    }
}
