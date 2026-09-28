<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\GradeComponent;
use App\Models\Role;
use App\Models\StudentAppeal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAndTeacherGradingTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;

    protected User $student;

    protected CourseSection $section;

    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $roleTeacher = Role::create(['name' => 'teacher', 'label_az' => 'Müəllim', 'level' => 50]);
        $roleStudent = Role::create(['name' => 'student', 'label_az' => 'Tələbə', 'level' => 20]);

        $faculty = Faculty::create(['name' => 'School of Engineering', 'slug' => 'eng', 'code' => 'ENG']);
        $dept = $faculty->departments()->create(['name' => 'Computer Science', 'slug' => 'cs', 'code' => 'CS']);
        $program = $dept->programs()->create(['name' => 'CS BSc', 'slug' => 'cs-bsc', 'code' => 'CS-BSC', 'credits_total' => 240]);
        $course = Course::create(['program_id' => $program->id, 'code' => 'CS101', 'name' => 'Data Structures', 'credits' => 6]);

        $this->teacher = new User([
            'name' => 'Prof. Teacher',
            'email' => 'teacher@khazar.edu.az',
            'password' => bcrypt('secret123'),
        ]);
        $this->teacher->role = 'teacher';
        $this->teacher->save();

        $this->student = new User([
            'name' => 'Murad Student',
            'email' => 'murad@khazar.edu.az',
            'password' => bcrypt('secret123'),
            'student_id' => 'KU-2026-0001',
            'degree_level' => 'bachelor',
        ]);
        $this->student->role = 'student';
        $this->student->save();

        $this->section = CourseSection::create([
            'course_id' => $course->id,
            'teacher_id' => $this->teacher->id,
            'semester_term' => '2026-Bahar',
            'room_number' => 'Neftçilər Lab 302',
            'day_of_week' => 'Bazar ertəsi',
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_section_id' => $this->section->id,
            'midterm_score' => 0,
            'final_score' => 0,
            'total_score' => 0,
            'letter_grade' => 'F',
        ]);
    }

    public function test_teacher_can_configure_grade_distribution_components(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('portal.grading.components.save', $this->section->id), [
            'components' => [
                ['name' => 'Davamiyyət', 'max_score' => 10, 'type' => 'attendance'],
                ['name' => 'Seminar', 'max_score' => 10, 'type' => 'seminar'],
                ['name' => 'Sərbəst İş', 'max_score' => 10, 'type' => 'assignment'],
                ['name' => 'Kollokvium', 'max_score' => 20, 'type' => 'midterm'],
                ['name' => 'Final İmtahanı', 'max_score' => 50, 'type' => 'final'],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('grade_components', 5);
        $this->assertEquals(5, $this->section->gradeComponents()->count());
    }

    public function test_teacher_can_assign_component_scores_and_recalculates_student_gpa(): void
    {
        $comp1 = GradeComponent::create(['course_section_id' => $this->section->id, 'name' => 'Davamiyyət', 'max_score' => 10, 'order_index' => 1]);
        $comp2 = GradeComponent::create(['course_section_id' => $this->section->id, 'name' => 'Seminar', 'max_score' => 10, 'order_index' => 2]);
        $comp3 = GradeComponent::create(['course_section_id' => $this->section->id, 'name' => 'Sərbəst İş', 'max_score' => 10, 'order_index' => 3]);
        $comp4 = GradeComponent::create(['course_section_id' => $this->section->id, 'name' => 'Kollokvium', 'max_score' => 20, 'order_index' => 4]);
        $comp5 = GradeComponent::create(['course_section_id' => $this->section->id, 'name' => 'Final', 'max_score' => 50, 'order_index' => 5]);

        $response = $this->actingAs($this->teacher)->post(route('portal.grading.scores.save', $this->section->id), [
            'grades' => [
                $this->enrollment->id => [
                    $comp1->id => 10,
                    $comp2->id => 10,
                    $comp3->id => 10,
                    $comp4->id => 18,
                    $comp5->id => 45,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->enrollment->refresh();
        $this->student->refresh();

        $this->assertEquals(93.00, (float) $this->enrollment->total_score);
        $this->assertEquals('A', $this->enrollment->letter_grade);
        $this->assertEquals(4.00, (float) $this->student->gpa);
        $this->assertEquals(6, $this->student->earned_credits);
    }

    public function test_student_can_view_schedule_grades_and_attendance(): void
    {
        $this->actingAs($this->student)->get(route('portal.schedule'))->assertOk()->assertSee('Həftəlik Akademik Dərs Cədvəli');
        $this->actingAs($this->student)->get(route('portal.semester-grades'))->assertOk()->assertSee('Semestr Qiymət Cədvəli');
        $this->actingAs($this->student)->get(route('portal.attendance-report'))->assertOk()->assertSee('Kursun Davamı və Qayıb Statistikası');
    }

    public function test_student_can_submit_grade_appeal_and_teacher_can_respond(): void
    {
        $response = $this->actingAs($this->student)->post(route('portal.appeals.store'), [
            'type' => 'grade_objection',
            'course_section_id' => $this->section->id,
            'subject' => 'Final İmtahanı Balına Etiraz',
            'reason' => '3-cü sual üzrə yazdığım həll tam riyazi isbatla verilmişdir.',
        ]);

        $response->assertRedirect();
        $appeal = StudentAppeal::where('student_id', $this->student->id)->first();
        $this->assertNotNull($appeal);
        $this->assertEquals('pending', $appeal->status);

        // Teacher responds
        $response = $this->actingAs($this->teacher)->post(route('portal.admin.appeals.respond', $appeal->id), [
            'status' => 'approved',
            'reviewer_notes' => 'Müraciət araşdırıldı və bal 2 bal artırıldı.',
        ]);

        $response->assertRedirect();
        $appeal->refresh();
        $this->assertEquals('approved', $appeal->status);
        $this->assertEquals('Müraciət araşdırıldı və bal 2 bal artırıldı.', $appeal->reviewer_notes);
    }
}
