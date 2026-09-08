<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can update the enrollment grades and attendance.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // Super admin and Academic admin can always update
        if ($user->isAdmin()) {
            return true;
        }

        // A teacher can ONLY update if they are the designated teacher of the section
        if ($user->isTeacher()) {
            return $enrollment->courseSection && $enrollment->courseSection->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view the enrollment details.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $enrollment->courseSection && $enrollment->courseSection->teacher_id === $user->id;
        }

        // Student can only view their own enrollment
        return $enrollment->student_id === $user->id;
    }
}
