<?php

namespace App\Services;

use App\Enums\LetterGrade;
use App\Models\Enrollment;
use App\Models\User;

class GradingService
{
    /**
     * Calculate letter grade for an enrollment based on total score and student degree
     */
    public function determineGrade(float $totalScore, string $degreeLevel = 'bachelor'): LetterGrade
    {
        return LetterGrade::fromScore($totalScore, $degreeLevel);
    }

    /**
     * Recompute and update GPA and earned credits for a specific student
     */
    public function recalculateStudentGpa(User $student): void
    {
        $enrollments = Enrollment::with('courseSection.course')
            ->where('student_id', $student->id)
            ->get();

        $totalWeightedPoints = 0.0;
        $totalGradedCredits = 0;
        $earnedCredits = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->courseSection?->course;
            if (! $course) {
                continue;
            }

            $credits = $course->credits;
            $letterGrade = LetterGrade::tryFrom($enrollment->letter_grade) ?? LetterGrade::fromScore($enrollment->total_score, $student->degree_level ?? 'bachelor');

            // Grade points on 4.0 scale
            $points = $letterGrade->gradePoint();

            // Include in GPA calculation if graded (A-F)
            if ($letterGrade !== LetterGrade::IC && $letterGrade !== LetterGrade::IP) {
                $totalWeightedPoints += ($points * $credits);
                $totalGradedCredits += $credits;

                if ($letterGrade->earnsCredit($student->degree_level ?? 'bachelor')) {
                    $earnedCredits += $credits;
                }
            }
        }

        $gpa = $totalGradedCredits > 0 ? round($totalWeightedPoints / $totalGradedCredits, 2) : 0.00;

        $student->forceFill([
            'gpa' => $gpa,
            'earned_credits' => $earnedCredits,
        ])->saveQuietly();
    }
}
