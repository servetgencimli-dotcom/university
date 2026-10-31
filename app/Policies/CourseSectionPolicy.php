<?php

namespace App\Policies;

use App\Models\CourseSection;
use App\Models\User;

class CourseSectionPolicy
{
    /**
     * Determine whether the user can manage grading/components for the section.
     */
    public function manage(User $user, CourseSection $section): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $section->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can access LMS interactive classroom.
     */
    public function viewLms(User $user, CourseSection $section): bool
    {
        if ($user->isAdmin() || $section->teacher_id === $user->id) {
            return true;
        }

        return $section->enrollments()->where('student_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can open QR attendance kiosk for the section.
     */
    public function kiosk(User $user, CourseSection $section): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $section->teacher_id === $user->id;
    }
}
