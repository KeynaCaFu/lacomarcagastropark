<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Get the current authenticated user
     */
    public static function user(): ?User
    {
        return Auth::user();
    }

    /**
     * Check if the current user is admin global
     */
    public static function isAdminGlobal(): bool
    {
        $user = Auth::user();
        return $user && $user->isAdminGlobal();
    }

    /**
     * Check if the current user is admin local (manager)
     */
    public static function isAdminLocal(): bool
    {
        $user = Auth::user();
        return $user && $user->isAdminLocal();
    }

    /**
     * Get the locals assigned to the current user
     */
    public static function getUserLocals()
    {
        $user = Auth::user();
        return $user ? $user->locals : collect();
    }

    /**
     * Get the role name of the current user
     */
    public static function getRoleName(): ?string
    {
        $user = Auth::user();
        return $user && $user->role ? $user->role->role_type : null;
    }

    /**
     * Check if user can access a specific local
     */
    public static function canAccessLocal($localId): bool
    {
        if (static::isAdminGlobal()) {
            return true;
        }

        if (static::isAdminLocal()) {
            $user = Auth::user();
            return $user->locals->pluck('local_id')->contains($localId);
        }

        return false;
    }

    /**
     * Get the first local assigned to the current user
     */
    public static function getDefaultLocal()
    {
        $locals = static::getUserLocals();
        return $locals->first();
    }
}
