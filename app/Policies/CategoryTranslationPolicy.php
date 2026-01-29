<?php

namespace App\Policies;

use App\Models\CategoryTranslation;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Helpers\Policies\AdminHelper;
use App\UserRole;

class CategoryTranslationPolicy
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
    public function view(User $user, CategoryTranslation $categoryTranslation): bool
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

    public function update(User $user, CategoryTranslation $categoryTranslation): bool
    {
        return false;
    }

    public function delete(User $user, CategoryTranslation $categoryTranslation): bool
    {
        return false;
    }

    public function restore(User $user, CategoryTranslation $categoryTranslation): bool
    {
        return false;
    }

    public function forceDelete(User $user, CategoryTranslation $categoryTranslation): bool
    {
        return false;
    }
}
