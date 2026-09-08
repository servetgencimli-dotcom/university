<?php

namespace App\Policies;

use App\Models\AdmissionsApplication;
use App\Models\User;

class AdmissionsApplicationPolicy
{
    /**
     * Determine whether the user can update the admission application status.
     */
    public function updateStatus(User $user, AdmissionsApplication $application): bool
    {
        return $user->isAdmin() || $user->hasPermission('admissions.manage');
    }

    /**
     * Determine whether the user can view the application.
     */
    public function view(User $user, AdmissionsApplication $application): bool
    {
        if ($user->isAdmin() || $user->hasPermission('admissions.manage')) {
            return true;
        }

        // Applicant can only view their own application
        return $application->user_id === $user->id;
    }
}
