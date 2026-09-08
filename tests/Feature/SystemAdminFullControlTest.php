<?php

namespace Tests\Feature;

use App\Models\AdmissionsApplication;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemAdminFullControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_system_admin_can_change_any_user_password(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $student = User::where('role', 'student')->first();

        $this->actingAs($sysAdmin);

        $response = $this->post("/portal/admin/users/{$student->id}/password", [
            'password' => 'newSecretPassword123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertTrue(Hash::check('newSecretPassword123!', $student->password));
    }

    public function test_non_admin_cannot_change_passwords(): void
    {
        $student = User::where('role', 'student')->first();
        $teacher = User::where('role', 'teacher')->first();

        $this->actingAs($student);

        $response = $this->post("/portal/admin/users/{$teacher->id}/password", [
            'password' => 'hackedPassword',
        ]);

        $response->assertStatus(403);
    }

    public function test_system_admin_can_update_roles_and_granular_permissions(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $student = User::where('role', 'student')->first();

        $this->actingAs($sysAdmin);

        $response = $this->post("/portal/admin/users/{$student->id}/permissions", [
            'role' => 'master_student',
            'role_title' => 'Tədqiqatçı Magistrant',
            'status' => 'active',
            'permissions' => ['theses.submit', 'labs.access'],
        ]);

        $response->assertRedirect();
        $student->refresh();
        $this->assertEquals('master_student', $student->role);
        $this->assertEquals('Tədqiqatçı Magistrant', $student->role_title);
        $this->assertContains('theses.submit', $student->permissions);
    }

    public function test_system_admin_can_impersonate_user_and_return(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $student = User::where('role', 'student')->first();

        $this->actingAs($sysAdmin);

        // 1. Impersonate student
        $response = $this->post("/portal/admin/impersonate/{$student->id}");
        $response->assertRedirect('/portal');
        $response->assertSessionHas('impersonator_id', $sysAdmin->id);

        $this->assertEquals($student->id, auth()->id());

        // 2. Stop impersonating
        $stopResponse = $this->post('/portal/admin/stop-impersonating');
        $stopResponse->assertRedirect('/portal');

        $this->assertEquals($sysAdmin->id, auth()->id());
    }

    public function test_system_admin_can_view_dashboard_as_other_roles(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $this->actingAs($sysAdmin);

        // Preview teacher
        $respTeacher = $this->get('/portal?view_as=teacher');
        $respTeacher->assertStatus(200);
        $respTeacher->assertSee('Müəllim');

        // Preview student
        $respStudent = $this->get('/portal?view_as=student');
        $respStudent->assertStatus(200);
        $respStudent->assertSee('Bakalavr');
    }

    public function test_system_admin_can_accept_dim_candidate_and_create_student_account(): void
    {
        $sysAdmin = User::where('role', 'system_admin')->first();
        $program = Program::first();

        $candidate = AdmissionsApplication::create([
            'application_number' => 'KU-DIM-2026-9012',
            'dim_work_number' => '2604921',
            'program_id' => $program->id,
            'first_name' => 'Elmir',
            'last_name' => 'Əliyev',
            'email' => 'elmir.dim@gmail.com',
            'phone' => '+994 50 111 22 33',
            'passport_fin' => '7XYZ999',
            'exam_score' => 615,
            'exam_type' => 'DİM',
            'secim_sira' => 1,
            'odenis_formasi' => 'Dövlət Sifarişli',
            'degree_level' => 'bachelor',
            'status' => 'under_review',
        ]);

        $this->actingAs($sysAdmin);

        $response = $this->post("/portal/admin/dim-candidates/{$candidate->id}/accept");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $candidate->refresh();
        $this->assertEquals('accepted', $candidate->status);
        $this->assertNotNull($candidate->user_id);

        $createdStudent = User::find($candidate->user_id);
        $this->assertNotNull($createdStudent);
        $this->assertEquals('Elmir Əliyev', $createdStudent->name);
        $this->assertEquals('student', $createdStudent->role);
        $this->assertStringContainsString('khazar.org', $createdStudent->email);
        $this->assertStringStartsWith('KU-', $createdStudent->student_id);
    }
}
