<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }
}
