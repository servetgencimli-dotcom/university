<?php

namespace Tests\Feature;

use App\Models\AdmissionsApplication;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\StudentAppeal;
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

    public function test_notifications_page_loads_and_read_all_route_works(): void
    {
        $student = User::where('role', 'student')->first();
        $this->actingAs($student);

        $response = $this->get(route('portal.notifications.index'));
        $response->assertStatus(200);

        $readAllResponse = $this->post(route('portal.notifications.readAll'));
        $readAllResponse->assertRedirect();
    }

    public function test_audit_logs_page_loads_for_system_admin_with_target_types(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $this->actingAs($sysAdmin);

        $response = $this->get(route('portal.admin.audit-logs'));
        $response->assertStatus(200);
        $response->assertSee('Təhlükəsizlik və Audit Jurnalı');
    }

    public function test_phase2_course_registration_smart_validations(): void
    {
        $student = User::where('role', 'student')->first();
        $this->actingAs($student);

        // 1. Course Registration Page Loads with Phase 2 Analysis
        $response = $this->get(route('portal.registration.index'));
        $response->assertStatus(200);
        $response->assertSee('Fənn Seçimi');
        $response->assertSee('toqquşması analizi aktivdir');

        // 2. GPA Credit limit logic verification
        $student->gpa = 3.80;
        $student->save();
        $this->assertEquals(36, $student->getMaxCreditLimit());

        $student->gpa = 1.50;
        $student->save();
        $this->assertEquals(24, $student->getMaxCreditLimit());
    }

    public function test_phase3_automated_attendance_fail_and_multi_stage_appeals(): void
    {
        $student = User::where('role', 'student')->first();
        $teacher = User::where('role', 'teacher')->first();
        $enrollment = Enrollment::with('courseSection')->where('student_id', $student->id)->first();
        $this->assertNotNull($enrollment);

        // 1. Simulate 4 sessions where student was absent (0% attendance -> <75% -> F grade & failed_attendance status)
        for ($i = 1; $i <= 4; $i++) {
            AttendanceRecord::create([
                'enrollment_id' => $enrollment->id,
                'session_date' => now()->subDays($i)->format('Y-m-d'),
                'status' => 'absent',
            ]);
        }

        $enrollment->recalculateAttendancePercentage();
        $enrollment->refresh();

        $this->assertEquals('F', $enrollment->letter_grade);

        // 2. Multi-Stage Appeal Submission & Approval with Score Adjustment
        $appeal = StudentAppeal::create([
            'student_id' => $student->id,
            'course_section_id' => $enrollment->course_section_id,
            'enrollment_id' => $enrollment->id,
            'type' => 'grade_objection',
            'subject' => 'Apellyasiya Komissiyası Test Müraciəti',
            'reason' => 'Kollokvium balımın yenidən yoxlanılması barədə.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Teacher forwards to commission
        $this->actingAs($teacher)->post(route('portal.admin.appeals.respond', $appeal->id), [
            'status' => 'commission_review',
            'reviewer_notes' => 'Komissiyanın iclasına yönləndirildi.',
        ])->assertRedirect();

        $appeal->refresh();
        $this->assertEquals('commission_review', $appeal->status);

        // Admin approves with +5 points score adjustment
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->post(route('portal.admin.appeals.respond', $appeal->id), [
            'status' => 'approved',
            'reviewer_notes' => 'Komissiya tərəfindən 5 bal artırıldı.',
            'score_adjustment' => 5,
        ])->assertRedirect();

        $appeal->refresh();
        $enrollment->refresh();

        $this->assertEquals('approved', $appeal->status);
        $this->assertEquals(96.50, (float) $enrollment->total_score);
        $this->assertEquals('A', $enrollment->letter_grade);
    }
}
