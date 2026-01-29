<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Helpers\Policies\AdminHelper;
use App\UserRole;

class LanguagePolicy
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
    public function view(User $user, Language $language): bool
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

    public function update(User $user, Language $language): bool
    {
        return false;
    }

    public function delete(User $user, Language $language): bool
    {
        return false;
    }

    public function restore(User $user, Language $language): bool
    {
        return false;
    }

    public function forceDelete(User $user, Language $language): bool
    {
        return false;
    }
}
