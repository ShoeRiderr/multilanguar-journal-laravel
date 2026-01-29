<?php

namespace App\Helpers\Policies;

class AdminHelper
{
    public static function isAdminRoute(): bool
    {
        $route = request()->route();
        $routeName = $route ? $route->getName() : null;
        return $routeName && str_starts_with($routeName, 'admin.');
    }
}