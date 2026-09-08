<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\GradingService;

class EnrollmentObserver
{
    public function __construct(protected GradingService $gradingService) {}

    /**
     * Handle the Enrollment "saved" event.
     */
    public function saved(Enrollment $enrollment): void
    {
        if ($enrollment->student) {
            $this->gradingService->recalculateStudentGpa($enrollment->student);
        }
    }

    /**
     * Handle the Enrollment "deleted" event.
     */
    public function deleted(Enrollment $enrollment): void
    {
        if ($enrollment->student) {
            $this->gradingService->recalculateStudentGpa($enrollment->student);
        }
    }
}
