<?php

namespace App\Policies;

use App\Models\Tkp\Industry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IndustryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Industry $industry): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете создать настройки отрасль');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Industry $industry): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете обновить настройки отрасль');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Industry $industry): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете удалить настройки отрасль');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Industry $industry): Response
    {
        return Response::deny('Доступ закрыт');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Industry $industry): Response
    {
        return Response::deny('Доступ закрыт');
    }
}
