<?php

namespace App\Policies;

use App\Models\Tkp\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierPolicy
{
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Supplier $supplier): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете создать поставщика');
    }

    public function update(User $user, Supplier $supplier): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете обновить поставщика');
    }

    public function delete(User $user, Supplier $supplier): Response
    {
        return $user->role == User::ADMIN ? Response::allow()
            : Response::deny('Вы не можете удалить поставщика');
    }

    public function restore(User $user, Supplier $supplier): Response
    {
        return Response::deny('Доступ закрыт');
    }

    public function forceDelete(User $user, Supplier $supplier): Response
    {
        return Response::deny('Доступ закрыт');
    }
}
