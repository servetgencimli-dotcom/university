<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseAnnouncement;
use App\Models\CourseMaterial;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsController extends Controller
{
    /**
     * Display the LMS interactive classroom for a course section.
     */
    public function showCourse(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::with([
            'course.program',
            'teacher',
            'assignments.submissions.student',
            'materials',
            'announcements',
            'enrollments.student',
        ])->findOrFail($sectionId);

        // Security: user must be the teacher, admin, or enrolled student
        $isTeacherOrAdmin = $user->isAdmin() || $section->teacher_id === $user->id;
        $isEnrolled = Enrollment::where('course_section_id', $section->id)
            ->where('student_id', $user->id)
            ->exists();

        if (! $isTeacherOrAdmin && ! $isEnrolled) {
            abort(403, 'Bu fənnin LMS otağına yalnız fənnin tələbələri və müəllimi daxil ola bilər.');
        }

        $mySubmissions = [];
        if (! $isTeacherOrAdmin) {
            $mySubmissions = AssignmentSubmission::where('student_id', $user->id)
                ->whereIn('assignment_id', $section->assignments->pluck('id'))
                ->get()
                ->keyBy('assignment_id');
        }

        return view('portal.lms_course', compact('section', 'isTeacherOrAdmin', 'mySubmissions'));
    }

    /**
     * Teacher creates a new assignment.
     */
    public function createAssignment(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::findOrFail($sectionId);

        if ($section->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|numeric|min:1|max:100',
        ]);

        $assignment = Assignment::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'],
            'max_score' => $validated['max_score'],
        ]);

        // Notify enrolled students
        foreach ($section->enrollments as $enr) {
            if ($enr->student) {
                SystemNotification::send(
                    $enr->student,
                    "Yeni Tapşırıq: {$assignment->title}",
                    "'{$section->course->name}' fənni üzrə yeni tapşırıq əlavə edildi. Son tarix: " . $assignment->due_date->format('d.m.Y H:i'),
                    'assignment',
                    route('portal.lms.course', $section->id)
                );
            }
        }

        return back()->with('success', "Tapşırıq ('{$assignment->title}') uğurla yaradıldı və tələbələrə elan olundu.");
    }

    /**
     * Student submits work for an assignment.
     */
    public function submitAssignment(Request $request, $assignmentId)
    {
        $user = Auth::user();
        $assignment = Assignment::with('courseSection')->findOrFail($assignmentId);

        $validated = $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,docx,zip,py,cpp,java,txt',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'submission_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/submissions'), $fileName);
            $filePath = 'uploads/submissions/' . $fileName;
        }

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $user->id,
            ],
            [
                'submission_text' => $validated['submission_text'] ?? null,
                'file_path' => $filePath ?? AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_id', $user->id)->value('file_path'),
                'submitted_at' => now(),
            ]
        );

        ActivityLog::log(
            'ASSIGNMENT_SUBMITTED',
            "Tələbə {$user->name} '{$assignment->title}' tapşırığını təqdim etdi.",
            $submission
        );

        return back()->with('success', 'Tapşırığınız uğurla təqdim edildi!');
    }

    /**
     * Teacher grades student's submission.
     */
    public function gradeSubmission(Request $request, $submissionId)
    {
        $user = Auth::user();
        $submission = AssignmentSubmission::with(['assignment.courseSection', 'student'])->findOrFail($submissionId);

        if ($submission->assignment->courseSection->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $maxScore = $submission->assignment->max_score;
        $validated = $request->validate([
            'score' => "required|numeric|min:0|max:{$maxScore}",
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
        ]);

        if ($submission->student) {
            SystemNotification::send(
                $submission->student,
                "Tapşırıq Qiymətləndirildi: {$submission->assignment->title}",
                "Qiymətiniz: {$submission->score} / {$maxScore}. Müəllim rəyi: {$submission->feedback}",
                'grade',
                route('portal.lms.course', $submission->assignment->course_section_id)
            );
        }

        return back()->with('success', "Qiymət ({$submission->score} bal) və rəy qeydə alındı.");
    }

    /**
     * Teacher uploads course material.
     */
    public function addMaterial(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::findOrFail($sectionId);

        if ($section->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,link,video,syllabus',
            'url' => 'nullable|url',
            'file' => 'nullable|file|max:20480',
        ]);

        $pathOrUrl = $validated['url'] ?? '#';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'mat_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/materials'), $fileName);
            $pathOrUrl = 'uploads/materials/' . $fileName;
        }

        CourseMaterial::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_path_or_url' => $pathOrUrl,
            'is_published' => true,
        ]);

        return back()->with('success', 'Fənn materialı əlavə edildi.');
    }

    /**
     * Teacher creates course announcement.
     */
    public function addAnnouncement(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::findOrFail($sectionId);

        if ($section->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = CourseAnnouncement::create([
            'course_section_id' => $section->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'published_at' => now(),
        ]);

        return back()->with('success', 'Elan paylaşıldı.');
    }
}
