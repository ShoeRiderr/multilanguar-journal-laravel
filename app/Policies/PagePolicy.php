<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Helpers\Policies\AdminHelper;
use App\UserRole;

class PagePolicy
{
    public function before(User $user): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (AdminHelper::isAdminRoute()) {
            return $user->role === UserRole::ADMIN;
        }
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        if (AdminHelper::isAdminRoute()) {
            return $user->role === UserRole::ADMIN;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Page $page): bool
    {
        return false;
    }

    public function delete(User $user, Page $page): bool
    {
        return false;
    }

    public function restore(User $user, Page $page): bool
    {
        return false;
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return false;
    }
}
