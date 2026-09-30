<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemAdminFullAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_system_admin_sees_all_student_teacher_and_admin_sidebar_links(): void
    {
        $admin = User::where('role', 'system_admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('portal.dashboard'));
        $response->assertStatus(200);

        // Verify Student Nav sections exist in sidebar for System Admin
        $response->assertSee('Tələbə Portalı');
        $response->assertSee('Dərs cədvəli');
        $response->assertSee('Transkript');
        $response->assertSee('Fənn Qeydiyyatı');

        // Verify Teacher Nav sections exist in sidebar for System Admin
        $response->assertSee('Cədvəl & Dərs Rejimi', false);
        $response->assertSee('Elektron Qiymət Jurnalı');
        $response->assertSee('QR Davamiyyət Kiosku');

        // Verify System Admin Nav sections exist
        $response->assertSee('Super Admin Nəzarəti');
        $response->assertSee('İstifadəçilər & İcazələr', false);
        $response->assertSee('DİM Qəbul Reyestri');
        $response->assertSee('Audit & Təhlükəsizlik Jurnalı', false);
    }
}
