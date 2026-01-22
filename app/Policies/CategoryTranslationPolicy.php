<?php

namespace App\Policies;

use App\Models\CategoryTranslation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CategoryTranslation $categoryTranslation): bool
    {
        return true;
    }
}
