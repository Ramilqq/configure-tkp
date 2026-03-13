<?php

namespace App\Policies;

use App\Models\Tkp\Tkp;
use App\Models\User;

class TkpPolicy
{
    /**
     * Проверить может ли пользователь просматривать ТКП
     */
    public function view(User $user, Tkp $tkp): bool
    {
        return $user->id === $tkp->user_id;
    }

    /**
     * Проверить может ли пользователь редактировать ТКП
     */
    public function update(User $user, Tkp $tkp): bool
    {
        return $user->id === $tkp->user_id;
    }

    /**
     * Проверить может ли пользователь удалять ТКП
     */
    public function delete(User $user, Tkp $tkp): bool
    {
        return $user->id === $tkp->user_id;
    }

    /**
     * Проверить может ли пользователь создавать ТКП
     */
    public function create(User $user): bool
    {
        return true;
    }
}
