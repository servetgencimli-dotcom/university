<?php

namespace App\Http\Controllers;

use App\Enums\LetterGrade;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Models\AcademicTerm;
use App\Models\AdmissionsApplication;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ResearchPaper;
use App\Models\Role;
use App\Models\StudentThesis;
use App\Models\TuitionInvoice;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function __construct(protected GradingService $gradingService) {}

    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSystemAdmin() && $request->filled('view_as')) {
            return match ($request->view_as) {
                'teacher' => $this->teacherDashboard(),
                'master_student' => $this->masterStudentDashboard(),
                'student' => $this->studentDashboard(),
                'admin' => $this->adminDashboard(),
                'applicant' => $this->applicantDashboard(),
                default => $this->systemAdminDashboard(),
            };
        }

        return match ($user->role) {
            'system_admin' => $this->systemAdminDashboard(),
            'admin' => $this->adminDashboard(),
            'teacher' => $this->teacherDashboard(),
            'master_student' => $this->masterStudentDashboard(),
            'student' => $this->studentDashboard(),
            'applicant' => $this->applicantDashboard(),
            default => $this->studentDashboard(),
        };
    }

    private function systemAdminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'students_count' => User::whereIn('role', ['student', 'master_student'])->count(),
            'teachers_count' => User::where('role', 'teacher')->count(),
            'programs_count' => Program::count(),
            'applications_count' => AdmissionsApplication::count(),
            'pending_applications' => AdmissionsApplication::whereIn('status', ['submitted', 'under_review'])->count(),
            'total_revenue' => TuitionInvoice::where('status', 'paid')->sum('amount'),
            'research_papers_count' => ResearchPaper::count(),
        ];

        $recentApplications = AdmissionsApplication::with('program.department.faculty')
            ->latest()
            ->take(8)
            ->get();

        $roles = Role::with('permissions')->get();

        return view('portal.system_admin', compact('stats', 'recentApplications', 'roles'));
    }

    private function adminDashboard()
    {
        $user = Auth::user();
        $faculty = $user->faculty;

        $stats = [
            'faculty_students' => User::where('faculty_id', $faculty?->id)->whereIn('role', ['student', 'master_student'])->count(),
            'faculty_teachers' => User::where('faculty_id', $faculty?->id)->where('role', 'teacher')->count(),
            'pending_applications' => AdmissionsApplication::where('status', 'submitted')->count(),
        ];

        $applicationsQuery = AdmissionsApplication::with('program.department.faculty')->latest();
        if ($faculty) {
            $applicationsQuery->whereHas('program.department', function ($q) use ($faculty) {
                $q->where('faculty_id', $faculty->id);
            });
        }
        $applications = $applicationsQuery->take(10)->get();

        $programs = Program::whereHas('department', function ($q) use ($faculty) {
            if ($faculty) {
                $q->where('faculty_id', $faculty->id);
            }
        })->get();

        return view('portal.admin', compact('stats', 'applications', 'programs', 'faculty'));
    }

    private function teacherDashboard()
    {
        $user = Auth::user();
        $taughtSections = CourseSection::with(['course.program', 'enrollments.student'])
            ->where('teacher_id', $user->id)
            ->get();

        $advisedTheses = StudentThesis::with('student')
            ->where('advisor_id', $user->id)
            ->get();

        $myResearch = ResearchPaper::where('author_id', $user->id)->get();

        return view('portal.teacher', compact('user', 'taughtSections', 'advisedTheses', 'myResearch'));
    }

    /**
     * Teacher weekly schedule grid — all sections sorted by day & time.
     */
    public function teacherSchedule()
    {
        $user = Auth::user();

        if (! $user->isTeacher() && ! $user->isAdmin() && ! $user->isSystemAdmin()) {
            abort(403);
        }

        $currentTerm = AcademicTerm::where('is_current', true)->first();
        $termCode = $currentTerm?->code ?? '2026-Bahar';

        $taughtSections = CourseSection::with(['course.program', 'enrollments.student'])
            ->where('teacher_id', $user->id)
            ->get()
            ->sortBy('start_time');

        return view('portal.teacher_schedule', compact('user', 'taughtSections', 'termCode'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with(['courseSection.course.program', 'courseSection.teacher'])
            ->where('student_id', $user->id)
            ->get();

        $invoices = TuitionInvoice::where('student_id', $user->id)->latest()->get();

        return view('portal.student', compact('user', 'enrollments', 'invoices'));
    }

    private function masterStudentDashboard()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with(['courseSection.course.program', 'courseSection.teacher'])
            ->where('student_id', $user->id)
            ->get();

        $thesis = StudentThesis::with('advisor')
            ->where('student_id', $user->id)
            ->first();

        $invoices = TuitionInvoice::where('student_id', $user->id)->latest()->get();

        return view('portal.master_student', compact('user', 'enrollments', 'thesis', 'invoices'));
    }

    private function applicantDashboard()
    {
        $user = Auth::user();
        $applications = AdmissionsApplication::with(['program.department.faculty', 'documents'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('portal.applicant', compact('user', 'applications'));
    }

    public function updateGrade(UpdateGradeRequest $request, $enrollmentId)
    {
        $validated = $request->validated();
        $enrollment = Enrollment::with('student')->findOrFail($enrollmentId);

        $total = $validated['midterm_score'] + $validated['final_score'];
        $degreeLevel = $enrollment->student?->degree_level ?? 'bachelor';
        $letterGrade = LetterGrade::fromScore($total, $degreeLevel);

        $enrollment->midterm_score = $validated['midterm_score'];
        $enrollment->final_score = $validated['final_score'];
        $enrollment->total_score = $total;
        $enrollment->letter_grade = $letterGrade->value;
        $enrollment->attendance_percentage = $validated['attendance_percentage'];
        $enrollment->status = $letterGrade->earnsCredit($degreeLevel) ? 'completed' : 'enrolled';
        $enrollment->save(); // Triggers EnrollmentObserver to auto-recalculate student GPA and credits!

        return back()->with('success', "Qiymət ({$letterGrade->value} - {$total} bal) və davamiyyət uğurla yeniləndi! Tələbənin GPA göstəricisi yenidən hesablandı.");
    }

    public function updateApplicationStatus(UpdateApplicationStatusRequest $request, $appId)
    {
        $validated = $request->validated();
        $app = AdmissionsApplication::findOrFail($appId);

        $app->update([
            'status' => $validated['status'],
            'reviewer_notes' => $validated['reviewer_notes'] ?? $app->reviewer_notes,
        ]);

        return back()->with('success', "Müraciət (#{$app->application_number}) statusu uğurla yeniləndi!");
    }
}
