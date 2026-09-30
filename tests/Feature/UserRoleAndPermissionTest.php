<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_granting_custom_permission_to_student_keeps_student_nav_and_appends_custom_permission(): void
    {
        $student = User::where('role', 'student')->first();

        // 1. Initially student cannot access admin users page
        $response = $this->actingAs($student)->get(route('portal.admin.users.index'));
        $response->assertStatus(403);

        // 2. Admin grants custom permission 'users.manage' to student WITHOUT changing student's base role
        $student->permissions = ['users.manage'];
        $student->save();

        // 3. Student can now access portal.admin.users.index
        $response = $this->actingAs($student)->get(route('portal.admin.users.index'));
        $response->assertStatus(200);

        // 4. Verify student still retains their student role and dashboard
        $this->assertEquals('student', $student->fresh()->role);
        $this->assertTrue($student->isStudent());

        // 5. Verify sidebar shows both Student Nav and Custom Granted Permissions Nav
        $response->assertSee('Tələbə Portalı');
        $response->assertSee('Xüsusi İcazələr', false);
        $response->assertSee('İstifadəçilər & İcazələr', false);
    }

    public function test_granting_custom_permission_to_teacher_keeps_teacher_nav_and_appends_custom_permission(): void
    {
        $teacher = User::where('role', 'teacher')->first();

        // Grant 'cms.manage' permission to teacher
        $teacher->permissions = ['cms.manage'];
        $teacher->save();

        $response = $this->actingAs($teacher)->get(route('portal.admin.cms.index'));
        $response->assertStatus(200);

        $this->assertEquals('teacher', $teacher->fresh()->role);
        $this->assertTrue($teacher->isTeacher());

        $response->assertSee('Cədvəl & Dərs Rejimi', false);
        $response->assertSee('Xüsusi İcazələr', false);
        $response->assertSee('CMS & Semestrlər', false);
    }

    public function test_custom_permission_only_unlocks_its_own_admin_area(): void
    {
        $student = User::where('role', 'student')->first();
        $student->permissions = ['cms.manage'];
        $student->save();

        $this->actingAs($student)->get(route('portal.admin.cms.index'))->assertOk();
        $this->actingAs($student)->get(route('portal.admin.users.index'))->assertForbidden();
        $this->actingAs($student)->get(route('portal.admin.audit-logs'))->assertForbidden();
        $this->actingAs($student)->get(route('portal.teacher.schedule'))->assertForbidden();
    }

    public function test_delegated_user_manager_cannot_grant_system_admin_role(): void
    {
        $manager = User::where('role', 'student')->first();
        $manager->permissions = ['users.manage'];
        $manager->save();

        $this->actingAs($manager)->post(route('portal.admin.users.permissions', $manager->id), [
            'role' => 'system_admin',
            'status' => 'active',
        ])->assertForbidden();

        $this->assertEquals('student', $manager->fresh()->role);
    }
}
