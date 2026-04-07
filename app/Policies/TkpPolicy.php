<?php

namespace App\Policies;

use App\Models\Tkp\Tkp;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TkpPolicy
{
    /**
     * Проверить может ли пользователь просматривать ТКП
     */
    public function view(User $user, Tkp $tkp): Response
    {
        return true ? Response::allow()
            : Response::deny('Вы не можете открыть чужое ТКП.');
    }

    /**
     * Проверить может ли пользователь редактировать ТКП
     */
    public function update(User $user, Tkp $tkp): Response
    {
        return $user->id === $tkp->user_id ? Response::allow()
            : Response::deny('Вы не можете редактировать чужое ТКП.');
    }

    /**
     * Проверить может ли пользователь удалять ТКП
     */
    public function delete(User $user, Tkp $tkp): Response
    {
        return $user->id === $tkp->user_id ? Response::allow()
            : Response::deny('Вы не можете удалить чужое ТКП.');
    }

    /**
     * Проверить может ли пользователь создавать ТКП
     */
    public function create(User $user): Response
    {
        return true ? Response::allow()
            : Response::deny('Вы не можете создать ТКП.');
    }
}
