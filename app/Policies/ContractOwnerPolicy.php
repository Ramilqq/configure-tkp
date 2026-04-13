<?php

namespace App\Policies;

use App\Models\Tkp\ContractOwner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContractOwnerPolicy
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
    public function view(User $user, ContractOwner $contractOwner): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете создать настройки владелец договора');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContractOwner $contractOwner): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете обновить настройки владелец договора');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContractOwner $contractOwner): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете удалить настройки владелец договора');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContractOwner $contractOwner): Response
    {
        return Response::deny('Доступ закрыт');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContractOwner $contractOwner): Response
    {
        return Response::deny('Доступ закрыт');
    }
}
