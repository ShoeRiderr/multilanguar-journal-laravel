<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Helpers\Policies\AdminHelper;
use App\UserRole;

class PostPolicy
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
    public function view(User $user, Post $post): bool
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

    public function update(User $user, Post $post): bool
    {
        return false;
    }

    public function delete(User $user, Post $post): bool
    {
        return false;
    }

    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
