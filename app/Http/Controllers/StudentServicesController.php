<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\Enrollment;
use App\Models\StudentAppeal;
use App\Models\SystemNotification;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentServicesController extends Controller
{
    /**
     * Semestr qiymətləri (Detailed Semester Grades with Teacher Component Breakdown)
     */
    public function grades(Request $request)
    {
        $user = Auth::user();
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        $termCode = $request->get('term', $currentTerm?->code ?? '2026-Bahar');

        $enrollments = Enrollment::with([
            'courseSection.course.program',
            'courseSection.teacher',
            'courseSection.gradeComponents',
            'gradeItems.gradeComponent',
        ])
            ->where('student_id', $user->id)
            ->whereHas('courseSection', function ($q) use ($termCode) {
                if ($termCode) {
                    $q->where('semester_term', $termCode);
                }
            })
            ->get();

        $terms = AcademicTerm::orderByDesc('id')->get();

        return view('portal.student_grades', compact('user', 'enrollments', 'termCode', 'terms', 'currentTerm'));
    }

    /**
     * Dərs cədvəli (Interactive Weekly Timetable)
     */
    public function schedule(Request $request)
    {
        $user = Auth::user();
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        $termCode = $request->get('term', $currentTerm?->code ?? '2026-Bahar');

        $enrollments = Enrollment::with([
            'courseSection.course',
            'courseSection.teacher',
        ])
            ->where('student_id', $user->id)
            ->whereHas('courseSection', function ($q) use ($termCode) {
                if ($termCode) {
                    $q->where('semester_term', $termCode);
                }
            })
            ->get();

        $daysOfWeek = [
            'Bazar ertəsi',
            'Çərşənbə axşamı',
            'Çərşənbə',
            'Cümə axşamı',
            'Cümə',
            'Şənbə',
        ];

        // Group by day of week
        $scheduleByDay = [];
        foreach ($daysOfWeek as $day) {
            $scheduleByDay[$day] = $enrollments->filter(function ($enr) use ($day) {
                return $enr->courseSection && $enr->courseSection->day_of_week === $day;
            })->sortBy(fn ($enr) => $enr->courseSection->start_time);
        }

        return view('portal.student_schedule', compact('user', 'enrollments', 'scheduleByDay', 'daysOfWeek', 'termCode'));
    }

    /**
     * Kursun Davamı Məlumatı (Course Continuation & Attendance Statistics)
     */
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $enrollments = Enrollment::with([
            'courseSection.course',
            'courseSection.teacher',
            'attendanceRecords' => fn ($q) => $q->orderBy('session_date', 'desc'),
        ])
            ->where('student_id', $user->id)
            ->get();

        return view('portal.student_attendance', compact('user', 'enrollments'));
    }

    /**
     * Student Appeals & Petitions (Tamamlama, İmtahan Etirazı, Cərimə, Dekanlıq)
     */
    public function appeals(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('type', 'grade_objection'); // grade_objection, makeup_exam, penalty_exam, dean_petition

        $myAppeals = StudentAppeal::with(['courseSection.course', 'reviewer'])
            ->where('student_id', $user->id)
            ->latest('submitted_at')
            ->get();

        $myEnrollments = Enrollment::with('courseSection.course')
            ->where('student_id', $user->id)
            ->get();

        return view('portal.student_appeals', compact('user', 'myAppeals', 'myEnrollments', 'activeTab'));
    }

    /**
     * Submit a new appeal or application
     */
    public function storeAppeal(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:makeup_exam,grade_objection,penalty_exam,dean_petition',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'subject' => 'required|string|max:200',
            'reason' => 'required|string|min:10|max:3000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('appeals', 'public');
        }

        $enrollmentId = null;
        if (! empty($validated['course_section_id'])) {
            $enrollment = Enrollment::where('student_id', $user->id)
                ->where('course_section_id', $validated['course_section_id'])
                ->first();
            $enrollmentId = $enrollment?->id;
        }

        $appeal = StudentAppeal::create([
            'student_id' => $user->id,
            'course_section_id' => $validated['course_section_id'] ?? null,
            'enrollment_id' => $enrollmentId,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Send confirmation notification
        SystemNotification::create([
            'user_id' => $user->id,
            'title' => 'Müraciətiniz Qeydə Alındı',
            'message' => "'{$appeal->type_name}' üzrə ərizəniz (#{$appeal->id}) qəbul edildi və baxılmaq üçün dekanlığa/komissiyaya göndərildi.",
            'type' => 'academic',
            'link' => route('portal.appeals.index', ['type' => $appeal->type]),
            'is_read' => false,
        ]);

        return redirect()->route('portal.appeals.index', ['type' => $validated['type']])
            ->with('success', 'Müraciətiniz uğurla göndərildi! Cavab bildiriş bölmənizə və kabinetinizə göndəriləcək.');
    }

    /**
     * Admin/Dean/Teacher Appeal Management List
     */
    public function manageAppeals(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSystemAdmin() && ! $user->isTeacher()) {
            abort(403);
        }

        $query = StudentAppeal::with(['student', 'courseSection.course', 'reviewer'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // If teacher, only show appeals for their sections
        if ($user->isTeacher() && ! $user->isAdmin() && ! $user->isSystemAdmin()) {
            $query->whereHas('courseSection', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        $appeals = $query->paginate(20);

        return view('portal.admin_appeals', compact('appeals'));
    }

    /**
     * Respond to an appeal (Multi-Stage Commission Review, Approve / Reject, Score Adjustment)
     */
    public function respondAppeal(Request $request, $appealId)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSystemAdmin() && ! $user->isTeacher()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:in_review,department_review,commission_review,approved,rejected',
            'reviewer_notes' => 'required|string|max:2000',
            'score_adjustment' => 'nullable|numeric|min:-50|max:50',
        ]);

        $appeal = StudentAppeal::with(['enrollment.student', 'courseSection'])->findOrFail($appealId);
        $appeal->update([
            'status' => $validated['status'],
            'reviewer_notes' => $validated['reviewer_notes'],
            'reviewed_by' => $user->id,
            'responded_at' => now(),
        ]);

        // If approved and score adjustment is specified, update enrollment grade & GPA
        if ($validated['status'] === 'approved' && ! empty($validated['score_adjustment']) && $appeal->enrollment) {
            $enrollment = $appeal->enrollment;
            $adj = (float) $validated['score_adjustment'];
            $newTotal = min(100, max(0, (float) $enrollment->total_score + $adj));

            $degreeLevel = $enrollment->student?->degree_level ?? 'bachelor';
            $letterGrade = Enrollment::calculateGrade($newTotal, $degreeLevel);

            $enrollment->total_score = $newTotal;
            $enrollment->letter_grade = $letterGrade;
            $enrollment->save();

            if ($enrollment->student) {
                app(GradingService::class)->recalculateStudentGpa($enrollment->student);
            }
        }

        // Notify student
        SystemNotification::create([
            'user_id' => $appeal->student_id,
            'title' => "Müraciətə Cavab Verildi: {$appeal->type_name}",
            'message' => "Müraciətiniz (#{$appeal->id}) nəticəsi: {$appeal->status_label}. Qeyd: {$appeal->reviewer_notes}",
            'type' => 'academic',
            'link' => route('portal.appeals.index', ['type' => $appeal->type]),
            'is_read' => false,
        ]);

        return back()->with('success', "Müraciət statusu uğurla '{$appeal->status_label}' olaraq qeyd edildi!");
    }
}
