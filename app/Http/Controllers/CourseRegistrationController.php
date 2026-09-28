<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\ActivityLog;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRegistrationController extends Controller
{
    /**
     * Show self-service course registration portal for students.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentTerm = AcademicTerm::current();
        $maxCreditLimit = $user->getMaxCreditLimit();

        $enrolledSections = Enrollment::with(['courseSection.course.prerequisite', 'courseSection.teacher'])
            ->where('student_id', $user->id)
            ->where('status', 'enrolled')
            ->get();

        $totalSelectedCredits = $enrolledSections->sum(fn ($e) => $e->courseSection?->course?->credits ?? 0);

        // Available sections for user's department/faculty
        $query = CourseSection::with(['course.prerequisite', 'teacher', 'enrollments'])
            ->where('semester_term', $currentTerm?->code ?? '2026-Bahar');

        if ($user->department_id) {
            $query->whereHas('course.program', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $availableSections = $query->get();

        // Build Real-Time Pre-analysis for each available section
        $sectionsAnalysis = [];
        foreach ($availableSections as $sec) {
            $course = $sec->course;
            $analysis = [
                'prereq_status' => 'passed',
                'prereq_name' => null,
                'schedule_conflict' => null,
            ];

            // 1. Prerequisite check
            if ($course && $course->prerequisite_course_id) {
                $analysis['prereq_name'] = $course->prerequisite?->name ?? 'Prerekvizit';
                $passed = Enrollment::where('student_id', $user->id)
                    ->whereHas('courseSection', fn ($q) => $q->where('course_id', $course->prerequisite_course_id))
                    ->where('status', 'completed')
                    ->where('letter_grade', '!=', 'F')
                    ->exists();
                $analysis['prereq_status'] = $passed ? 'passed' : 'failed';
            }

            // 2. Schedule conflict check against currently enrolled sections
            foreach ($enrolledSections as $enr) {
                $enrolledSec = $enr->courseSection;
                if ($enrolledSec && $enrolledSec->day_of_week === $sec->day_of_week) {
                    $newStart = strtotime($sec->start_time);
                    $newEnd = strtotime($sec->end_time);
                    $existStart = strtotime($enrolledSec->start_time);
                    $existEnd = strtotime($enrolledSec->end_time);

                    if ($newStart < $existEnd && $newEnd > $existStart) {
                        $analysis['schedule_conflict'] = ($enrolledSec->course?->name ?? 'Dərs')." ({$enrolledSec->start_time}-{$enrolledSec->end_time})";
                        break;
                    }
                }
            }

            $sectionsAnalysis[$sec->id] = $analysis;
        }

        return view('portal.course_registration', compact(
            'user',
            'currentTerm',
            'enrolledSections',
            'totalSelectedCredits',
            'availableSections',
            'maxCreditLimit',
            'sectionsAnalysis'
        ));
    }

    /**
     * Enroll in a course section with all strict validations.
     */
    public function enroll(Request $request, $sectionId)
    {
        $user = Auth::user();
        $currentTerm = AcademicTerm::current();

        // 1. Check Registration Window
        if ($currentTerm && ! $currentTerm->registration_open) {
            return back()->with('error', 'Cari semestr üçün fənn qeydiyyatı pəncərəsi bağlıdır.');
        }

        // 2. Financial Debt Hold Check
        if ($user->hasUnpaidDebt()) {
            return back()->with('error', "Ödənilməmiş təhsil haqqı borcunuz ({$user->totalDebtAmount()} ₼) olduğuna görə fənn qeydiyyatı bloklanmışdır. Zəhmət olmasa əvvəlcə fakturanı ödəyin.");
        }

        $section = CourseSection::with(['course.prerequisite', 'enrollments'])->findOrFail($sectionId);
        $course = $section->course;

        // 3. Check Capacity
        if ($section->isFull()) {
            return back()->with('error', "Bu qrupun ({$section->room_number}) tələbə tutumu ({$section->capacity} yer) tam dolmuşdur.");
        }

        // 4. Check already enrolled in this course or section
        $alreadyEnrolled = Enrollment::where('student_id', $user->id)
            ->whereHas('courseSection', fn ($q) => $q->where('course_id', $course->id))
            ->whereIn('status', ['enrolled', 'completed'])
            ->exists();

        if ($alreadyEnrolled) {
            return back()->with('error', "Siz artıq '{$course->name}' fənninə qeydiyyatdan keçmisiniz və ya bu fənni tamamlamısınız.");
        }

        // 5. Prerequisite Check
        if ($course->prerequisite_course_id) {
            $passedPrereq = Enrollment::where('student_id', $user->id)
                ->whereHas('courseSection', fn ($q) => $q->where('course_id', $course->prerequisite_course_id))
                ->where('status', 'completed')
                ->where('letter_grade', '!=', 'F')
                ->exists();

            if (! $passedPrereq) {
                $prereqName = $course->prerequisite?->name ?? 'Prerekvizit fənn';

                return back()->with('error', "Bu fənni seçmək üçün əvvəlcə prerekvizit olan '{$prereqName}' fənnini müvəffəqiyyətlə tamamlamalısınız.");
            }
        }

        // 6. Max Credit Limit Check (Dynamic limit based on GPA: 36 ECTS for Honor, 24 ECTS for Probation, 30 ECTS standard)
        $maxLimit = $user->getMaxCreditLimit();
        $currentCredits = Enrollment::where('student_id', $user->id)
            ->where('status', 'enrolled')
            ->whereHas('courseSection', fn ($q) => $q->where('semester_term', $section->semester_term))
            ->get()
            ->sum(fn ($e) => $e->courseSection?->course?->credits ?? 0);

        if (($currentCredits + $course->credits) > $maxLimit) {
            return back()->with('error', "Kredit limiti aşıldı! Sizə icazə verilən maksimum limit {$maxLimit} ECTS kreditdir (Cari: {$currentCredits} ECTS, Cəhd: +{$course->credits} ECTS).");
        }

        // 7. Schedule Collision Check (Dərs toqquşması)
        $existingSections = CourseSection::whereHas('enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'enrolled');
        })
            ->where('semester_term', $section->semester_term)
            ->where('day_of_week', $section->day_of_week)
            ->get();

        foreach ($existingSections as $existing) {
            $newStart = strtotime($section->start_time);
            $newEnd = strtotime($section->end_time);
            $existStart = strtotime($existing->start_time);
            $existEnd = strtotime($existing->end_time);

            // Overlap condition
            if ($newStart < $existEnd && $newEnd > $existStart) {
                return back()->with('error', "Dərs cədvəlində saat toqquşması! '{$course->name}' fənni {$section->day_of_week} {$section->start_time}-{$section->end_time} saatında '{$existing->course->name}' fənni ilə üst-üstə düşür.");
            }
        }

        // 8. Enroll student
        $enrollment = Enrollment::create([
            'student_id' => $user->id,
            'course_section_id' => $section->id,
            'midterm_score' => 0.00,
            'final_score' => 0.00,
            'total_score' => 0.00,
            'letter_grade' => 'IP',
            'attendance_percentage' => 100.00,
            'status' => 'enrolled',
        ]);

        ActivityLog::log(
            'COURSE_ENROLLED',
            "Tələbə {$user->name} '{$course->name}' fənninə ({$course->credits} ECTS) qeydiyyatdan keçdi.",
            $enrollment
        );

        SystemNotification::send(
            $user,
            'Fənn Qeydiyyatı Təsdiqləndi',
            "Siz {$section->semester_term} semestri üçün '{$course->name}' fənninə uğurla qeydiyyatdan keçdiniz.",
            'academic',
            route('portal.dashboard')
        );

        return back()->with('success', "'{$course->name}' fənninə uğurla qeydiyyatdan keçdiniz! ({$course->credits} ECTS əlavə edildi)");
    }

    /**
     * Drop an enrolled course during registration period.
     */
    public function drop(Request $request, $enrollmentId)
    {
        $user = Auth::user();
        $enrollment = Enrollment::with('courseSection.course')->findOrFail($enrollmentId);

        if ($enrollment->student_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $courseName = $enrollment->courseSection?->course?->name ?? 'Fənn';

        ActivityLog::log(
            'COURSE_DROPPED',
            "Tələbə {$user->name} '{$courseName}' fənnindən imtina etdi (Drop).",
            $enrollment
        );

        $enrollment->delete();

        return back()->with('success', "'{$courseName}' fənn qeydiyyatı ləğv edildi.");
    }
}
