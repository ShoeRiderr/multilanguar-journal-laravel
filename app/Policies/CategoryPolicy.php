<?php

namespace App\Policies;

use App\Helpers\Policies\AdminHelper;
use App\Models\Category;
use App\Models\User;
use App\UserRole;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
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
    public function view(User $user, Category $category): bool
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

    public function update(User $user, Category $category): bool
    {
        return false;
    }

    public function delete(User $user, Category $category): bool
    {
        return false;
    }

    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}
