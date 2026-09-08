<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\CourseSection;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Record per-session attendance for a course section.
     */
    public function storeSession(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::with('enrollments.student')->findOrFail($sectionId);

        if ($section->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Yalnız bu fənni tədris edən müəllim davamiyyət yaza bilər.');
        }

        $validated = $request->validate([
            'session_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|array',
        ]);

        $sessionDate = $validated['session_date'];

        foreach ($validated['attendance'] as $enrollmentId => $status) {
            $enrollment = Enrollment::where('id', $enrollmentId)
                ->where('course_section_id', $section->id)
                ->first();

            if (! $enrollment) {
                continue;
            }

            AttendanceRecord::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'session_date' => $sessionDate,
                ],
                [
                    'status' => $status,
                    'notes' => $validated['notes'][$enrollmentId] ?? null,
                ]
            );

            $enrollment->recalculateAttendancePercentage();
        }

        ActivityLog::log(
            'ATTENDANCE_RECORDED',
            "Müəllim {$user->name} {$sessionDate} tarixi üçün '{$section->course->name}' fənninə davamiyyət qeyd etdi.",
            $section
        );

        return back()->with('success', "{$sessionDate} tarixi üçün davamiyyət uğurla qeydə alındı və tələbələrin davamiyyət faizləri yeniləndi.");
    }
}
