<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user): ?bool
    {
        // Superadmin bypasses all checks
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Continue with normal policy checks
        return null;
    }

    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_activity_logs');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Activity $activityLog): bool
    {
        return $user->hasPermissionTo('view_activity_logs');
    }
}
