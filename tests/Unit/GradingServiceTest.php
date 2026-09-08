<?php

namespace Tests\Unit;

use App\Enums\LetterGrade;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_letter_grade_conversion_on_4_scale(): void
    {
        // Bachelor 5-tier standard
        $this->assertEquals(LetterGrade::A, LetterGrade::fromScore(95, 'bachelor'));
        $this->assertEquals(4.0, LetterGrade::A->gradePoint());

        $this->assertEquals(LetterGrade::B, LetterGrade::fromScore(85, 'bachelor'));
        $this->assertEquals(3.0, LetterGrade::B->gradePoint());

        $this->assertEquals(LetterGrade::C, LetterGrade::fromScore(75, 'bachelor'));
        $this->assertEquals(2.0, LetterGrade::C->gradePoint());

        $this->assertEquals(LetterGrade::D, LetterGrade::fromScore(65, 'bachelor'));
        $this->assertEquals(1.0, LetterGrade::D->gradePoint());

        $this->assertEquals(LetterGrade::F, LetterGrade::fromScore(55, 'bachelor'));
        $this->assertEquals(0.0, LetterGrade::F->gradePoint());
    }

    public function test_master_degree_thresholds(): void
    {
        // Master passing threshold is 65 (F < 65)
        $this->assertEquals(LetterGrade::D, LetterGrade::fromScore(66, 'master'));
        $this->assertEquals(LetterGrade::F, LetterGrade::fromScore(62, 'master'));
    }

    public function test_automatic_gpa_and_credits_recalculation(): void
    {
        $school = Faculty::create([
            'name' => 'School of Science',
            'slug' => 'science',
            'code' => 'SCI',
        ]);

        $department = $school->departments()->create([
            'name' => 'Computer Science',
            'slug' => 'cs',
            'code' => 'CS',
        ]);

        $program = $department->programs()->create([
            'name' => 'Computer Science BSc',
            'slug' => 'cs-bsc',
            'code' => 'CS-BSC',
            'credits_total' => 240,
        ]);

        $course1 = Course::create([
            'program_id' => $program->id,
            'code' => 'CS101',
            'name' => 'Algorithms',
            'credits' => 6,
        ]);

        $course2 = Course::create([
            'program_id' => $program->id,
            'code' => 'CS102',
            'name' => 'OOP',
            'credits' => 4,
        ]);

        $teacher = new User([
            'name' => 'Prof. Teacher',
            'email' => 'teacher@khazar.org',
            'password' => 'password',
        ]);
        $teacher->role = 'teacher';
        $teacher->save();

        $student = new User([
            'name' => 'Murad Test',
            'email' => 'murad.test@khazar.org',
            'password' => 'password',
            'degree_level' => 'bachelor',
        ]);
        $student->role = 'student';
        $student->save();

        $sec1 = CourseSection::create([
            'course_id' => $course1->id,
            'teacher_id' => $teacher->id,
        ]);

        $sec2 = CourseSection::create([
            'course_id' => $course2->id,
            'teacher_id' => $teacher->id,
        ]);

        // Enrollment 1: 95 total -> Grade A (4.0 points) * 6 credits = 24 quality points
        $enr1 = new Enrollment([
            'student_id' => $student->id,
            'course_section_id' => $sec1->id,
            'midterm_score' => 47,
            'final_score' => 48,
            'attendance_percentage' => 100,
        ]);
        $enr1->total_score = 95;
        $enr1->letter_grade = 'A';
        $enr1->save();

        // Enrollment 2: 85 total -> Grade B (3.0 points) * 4 credits = 12 quality points
        // Total points = 36 / 10 credits = 3.60 GPA
        $enr2 = new Enrollment([
            'student_id' => $student->id,
            'course_section_id' => $sec2->id,
            'midterm_score' => 42,
            'final_score' => 43,
            'attendance_percentage' => 95,
        ]);
        $enr2->total_score = 85;
        $enr2->letter_grade = 'B';
        $enr2->save();

        $gradingService = app(GradingService::class);
        $gradingService->recalculateStudentGpa($student);

        $student->refresh();
        $this->assertEquals(3.60, (float) $student->gpa);
        $this->assertEquals(10, $student->earned_credits);
    }
}
