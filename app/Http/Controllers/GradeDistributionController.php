<?php

namespace App\Http\Controllers;

use App\Enums\LetterGrade;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\GradeComponent;
use App\Models\StudentGradeItem;
use App\Models\SystemNotification;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeDistributionController extends Controller
{
    public function __construct(protected GradingService $gradingService) {}

    /**
     * Show grade distribution and student electronic journal for a course section
     */
    public function showCourseGrading($sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::with([
            'course.program',
            'teacher',
            'gradeComponents',
            'enrollments.student',
            'enrollments.gradeItems.gradeComponent',
        ])->findOrFail($sectionId);

        // Security check: only the assigned teacher or admins can manage
        if (! $user->isSystemAdmin() && ! $user->isAdmin() && $section->teacher_id !== $user->id) {
            abort(403, 'Bu fənnin qiymətləndirmə jurnalına baxış icazəniz yoxdur.');
        }

        // If no components exist yet, prepare default recommendation
        $components = $section->gradeComponents;
        $totalMaxScore = $components->sum('max_score');

        return view('portal.teacher_grading', compact('section', 'components', 'totalMaxScore'));
    }

    /**
     * Save / Update grade components (Bal bölgüsü)
     */
    public function saveComponents(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::findOrFail($sectionId);

        if (! $user->isSystemAdmin() && ! $user->isAdmin() && $section->teacher_id !== $user->id) {
            abort(403, 'İcazəniz yoxdur.');
        }

        $validated = $request->validate([
            'components' => 'required|array|min:1',
            'components.*.id' => 'nullable|integer',
            'components.*.name' => 'required|string|max:100',
            'components.*.max_score' => 'required|numeric|min:0.5|max:100',
            'components.*.type' => 'nullable|string|max:50',
        ]);

        $submittedIds = [];
        $orderIndex = 0;

        DB::transaction(function () use ($section, $validated, &$submittedIds, &$orderIndex) {
            foreach ($validated['components'] as $compData) {
                $orderIndex++;
                if (! empty($compData['id'])) {
                    $comp = GradeComponent::where('course_section_id', $section->id)->find($compData['id']);
                    if ($comp) {
                        $comp->update([
                            'name' => $compData['name'],
                            'max_score' => $compData['max_score'],
                            'type' => $compData['type'] ?? 'custom',
                            'order_index' => $orderIndex,
                        ]);
                        $submittedIds[] = $comp->id;

                        continue;
                    }
                }

                $newComp = GradeComponent::create([
                    'course_section_id' => $section->id,
                    'name' => $compData['name'],
                    'max_score' => $compData['max_score'],
                    'type' => $compData['type'] ?? 'custom',
                    'order_index' => $orderIndex,
                ]);
                $submittedIds[] = $newComp->id;
            }

            // Remove components that were deleted by teacher
            GradeComponent::where('course_section_id', $section->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        });

        // Recalculate student scores based on updated components
        $section->load(['enrollments.gradeItems', 'enrollments.student']);
        foreach ($section->enrollments as $enrollment) {
            $enrollment->recalculateTotalFromComponents();
        }

        return back()->with('success', 'Fənn üzrə bal bölgüsü uğurla yadda saxlanıldı!');
    }

    /**
     * Apply default standard Azerbaijani university grade distribution template (10-10-10-20-50)
     */
    public function applyDefaultTemplate(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::findOrFail($sectionId);

        if (! $user->isSystemAdmin() && ! $user->isAdmin() && $section->teacher_id !== $user->id) {
            abort(403, 'İcazəniz yoxdur.');
        }

        $template = [
            ['name' => 'Davamiyyət və Dərsdə Fəallıq', 'max_score' => 10, 'type' => 'attendance', 'order_index' => 1],
            ['name' => 'Seminar və Məşğələ Dərsləri', 'max_score' => 10, 'type' => 'seminar', 'order_index' => 2],
            ['name' => 'Sərbəst İşlər / Layihə / Təqdimat', 'max_score' => 10, 'type' => 'assignment', 'order_index' => 3],
            ['name' => 'Kollokvium / Quizlər / Aralıq İmtahan', 'max_score' => 20, 'type' => 'midterm', 'order_index' => 4],
            ['name' => 'Final İmtahanı', 'max_score' => 50, 'type' => 'final', 'order_index' => 5],
        ];

        DB::transaction(function () use ($section, $template) {
            // Remove existing if empty or reset
            GradeComponent::where('course_section_id', $section->id)->delete();

            foreach ($template as $item) {
                GradeComponent::create([
                    'course_section_id' => $section->id,
                    'name' => $item['name'],
                    'max_score' => $item['max_score'],
                    'type' => $item['type'],
                    'order_index' => $item['order_index'],
                ]);
            }
        });

        return back()->with('success', 'Standart bal bölgüsü şablonu (10-10-10-20-50) fənnə tətbiq edildi!');
    }

    /**
     * Batch save student component scores in electronic journal
     */
    public function saveStudentScores(Request $request, $sectionId)
    {
        $user = Auth::user();
        $section = CourseSection::with(['gradeComponents', 'enrollments.student'])->findOrFail($sectionId);

        if (! $user->isSystemAdmin() && ! $user->isAdmin() && $section->teacher_id !== $user->id) {
            abort(403, 'İcazəniz yoxdur.');
        }

        $grades = $request->input('grades', []); // [enrollment_id => [component_id => score]]

        DB::transaction(function () use ($section, $grades) {
            $components = $section->gradeComponents->keyBy('id');

            foreach ($grades as $enrollmentId => $compScores) {
                $enrollment = Enrollment::where('course_section_id', $section->id)->find($enrollmentId);
                if (! $enrollment) {
                    continue;
                }

                $totalScore = 0.0;
                $midtermSum = 0.0;
                $finalScore = 0.0;

                foreach ($compScores as $compId => $scoreValue) {
                    if (! isset($components[$compId])) {
                        continue;
                    }

                    $maxScore = (float) $components[$compId]->max_score;
                    $score = min($maxScore, max(0.0, (float) $scoreValue));

                    StudentGradeItem::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'grade_component_id' => $compId,
                        ],
                        [
                            'score' => $score,
                        ]
                    );

                    $totalScore += $score;

                    if ($components[$compId]->type === 'final' || str_contains(mb_strtolower($components[$compId]->name), 'final') || str_contains(mb_strtolower($components[$compId]->name), 'imtahan')) {
                        $finalScore += $score;
                    } else {
                        $midtermSum += $score;
                    }
                }

                $degreeLevel = $enrollment->student?->degree_level ?? 'bachelor';
                $letterGrade = LetterGrade::fromScore($totalScore, $degreeLevel);

                $enrollment->midterm_score = $midtermSum;
                $enrollment->final_score = $finalScore;
                $enrollment->total_score = $totalScore;
                $enrollment->letter_grade = $letterGrade->value;
                $enrollment->status = $letterGrade->earnsCredit($degreeLevel) ? 'completed' : 'enrolled';
                $enrollment->saveQuietly();

                if ($enrollment->student) {
                    $this->gradingService->recalculateStudentGpa($enrollment->student);

                    // Notify student of grade update
                    SystemNotification::create([
                        'user_id' => $enrollment->student_id,
                        'title' => 'Qiymət Yeniləndi: '.$section->course->name,
                        'message' => "Müəllim tərəfindən '{$section->course->name}' fənni üzrə ballarınız yeniləndi. Cari yekun bal: {$totalScore} ({$letterGrade->value}).",
                        'type' => 'grade',
                        'link' => route('portal.semester-grades'),
                        'is_read' => false,
                    ]);
                }
            }
        });

        return back()->with('success', 'Bütün tələbələrin balları və hərfi qiymətləri uğurla yeniləndi!');
    }
}
