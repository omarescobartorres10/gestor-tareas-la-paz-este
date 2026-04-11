<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the admin dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can manage users.
     */
    public function manageUsers(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete a specific user.
     */
    public function deleteUser(User $user, User $targetUser): bool
    {
        // Admin cannot delete themselves
        if ($user->id === $targetUser->id) {
            return false;
        }

        // Prevent deleting the last admin
        if ($targetUser->isAdmin()) {
            $adminCount = User::where('role', 'admin')
                ->where('is_active', true)
                ->count();

            if ($adminCount <= 1) {
                return false;
            }
        }

        return $user->isAdmin();
    }

    /**
     * Determine if the user can view all tasks.
     */
    public function viewAllTasks(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can reassign tasks.
     */
    public function reassignTask(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can toggle user status.
     */
    public function toggleUserStatus(User $user, User $targetUser): bool
    {
        // Admin cannot deactivate themselves
        if ($user->id === $targetUser->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine if the user can export reports.
     */
    public function exportReports(User $user): bool
    {
        return $user->isAdmin();
    }
}
