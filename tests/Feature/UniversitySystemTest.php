<?php

namespace Tests\Feature;

use App\Models\AdmissionsApplication;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversitySystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_loads_successfully_with_khazar_branding(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('XƏZƏR');
        $response->assertDontSee('NOVA');
    }

    public function test_programs_catalog_and_filtering(): void
    {
        $response = $this->get('/programs');
        $response->assertStatus(200);
        $response->assertSee('İxtisaslar');
    }

    public function test_program_details_page(): void
    {
        $program = Program::first();
        $this->assertNotNull($program);

        $response = $this->get("/programs/{$program->slug}");
        $response->assertStatus(200);
        $response->assertSee($program->name);
    }

    public function test_faculties_page(): void
    {
        $response = $this->get('/faculties');
        $response->assertStatus(200);
        $response->assertSee('Məktəb');
    }

    public function test_virtual_campus_map(): void
    {
        $response = $this->get('/campus-map');
        $response->assertStatus(200);
        $response->assertSee('Kampus');
        $response->assertSee('Neftçilər Korpusu');
    }

    public function test_admissions_submission_and_tracking(): void
    {
        $program = Program::first();

        $response = $this->post('/admissions/apply', [
            'program_id' => $program->id,
            'first_name' => 'Fərid',
            'last_name' => 'Həsənli',
            'email' => 'farid.test@example.com',
            'phone' => '+994 50 999 88 77',
            'degree_level' => 'bachelor',
            'exam_type' => 'DİM',
            'exam_score' => 610,
        ]);

        $response->assertRedirect();

        $app = AdmissionsApplication::where('email', 'farid.test@example.com')->first();
        $this->assertNotNull($app);

        $trackResponse = $this->get("/admissions/track?app_number={$app->application_number}");
        $trackResponse->assertStatus(200);
        $trackResponse->assertSee($app->application_number);
    }

    public function test_multi_role_portal_access(): void
    {
        // 1. System Admin
        $sysAdmin = User::where('role', 'system_admin')->first();
        $this->assertNotNull($sysAdmin);
        $this->actingAs($sysAdmin);
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Super Admin');

        // 2. Teacher
        $teacher = User::where('role', 'teacher')->first();
        $this->actingAs($teacher);
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Müəllim Portalı');

        // 3. Student
        $student = User::where('role', 'student')->first();
        $this->actingAs($student);
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('RƏQƏMSAL TƏLƏBƏ KARTI');

        // 4. Master Student
        $master = User::where('role', 'master_student')->first();
        $this->actingAs($master);
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Magistr Dissertasiyası');
    }

    public function test_unauthorized_student_cannot_update_grades_idor_prevention(): void
    {
        $student = User::where('role', 'student')->first();
        $enrollment = Enrollment::first();

        $this->actingAs($student);
        $response = $this->post("/portal/grades/{$enrollment->id}", [
            'midterm_score' => 50,
            'final_score' => 50,
            'attendance_percentage' => 100,
        ]);

        // Must be forbidden (403)
        $response->assertStatus(403);
    }

    public function test_unauthorized_applicant_cannot_update_application_status(): void
    {
        $applicant = User::where('role', 'applicant')->first();
        $app = AdmissionsApplication::first();

        $this->actingAs($applicant);
        $response = $this->post("/portal/applications/{$app->id}/status", [
            'status' => 'accepted',
            'reviewer_notes' => 'Self accepted',
        ]);

        // Must be forbidden (403)
        $response->assertStatus(403);
    }

    public function test_teacher_can_update_grades_of_their_section_and_recalculates_gpa(): void
    {
        $enrollment = Enrollment::with(['courseSection', 'student'])->first();
        $teacher = User::find($enrollment->courseSection->teacher_id);
        $this->assertNotNull($teacher);

        $this->actingAs($teacher);
        $response = $this->post("/portal/grades/{$enrollment->id}", [
            'midterm_score' => 45,
            'final_score' => 47,
            'attendance_percentage' => 95,
        ]);

        $response->assertRedirect();
        $enrollment->refresh();
        $this->assertEquals(92.00, (float) $enrollment->total_score);
        $this->assertEquals('A', $enrollment->letter_grade);
    }

    public function test_admin_can_update_admission_application_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $app = AdmissionsApplication::first();

        $this->actingAs($admin);
        $response = $this->post("/portal/applications/{$app->id}/status", [
            'status' => 'accepted',
            'reviewer_notes' => 'Təsdiq edildi.',
        ]);

        $response->assertRedirect();
        $app->refresh();
        $this->assertEquals('accepted', $app->status);
    }
}
