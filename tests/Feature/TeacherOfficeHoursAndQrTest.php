<?php

namespace Tests\Feature;

use App\Models\AttendanceQrSession;
use App\Models\CourseSection;
use App\Models\OfficeHourBooking;
use App\Models\OfficeHourSlot;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherOfficeHoursAndQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_teacher_can_create_office_hour_slot_and_student_can_book(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $student = User::where('role', 'student')->first();

        // 1. Teacher creates office hour slot
        $response = $this->actingAs($teacher)->post(route('portal.teacher.office-hours.slot.store'), [
            'day_of_week' => 'Bazar ertəsi',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'slot_duration_minutes' => 20,
            'max_students_per_slot' => 3,
            'location' => 'Otaq 304',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('office_hour_slots', [
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Bazar ertəsi',
            'location' => 'Otaq 304',
        ]);

        $slot = OfficeHourSlot::first();

        // 2. Student views office hours and books
        $response = $this->actingAs($student)->get(route('portal.student.office-hours'));
        $response->assertStatus(200);

        $bookResponse = $this->actingAs($student)->post(route('portal.student.office-hours.book'), [
            'slot_id' => $slot->id,
            'booking_date' => now()->addDay()->toDateString(),
            'topic' => 'Kollokvium mövzuları haqqında',
        ]);

        $bookResponse->assertRedirect();
        $this->assertDatabaseHas('office_hour_bookings', [
            'slot_id' => $slot->id,
            'student_id' => $student->id,
            'topic' => 'Kollokvium mövzuları haqqında',
            'status' => 'pending',
        ]);

        // 3. Teacher confirms booking
        $booking = OfficeHourBooking::first();
        $confirmResponse = $this->actingAs($teacher)->patch(route('portal.teacher.office-hours.booking.status', $booking->id), [
            'status' => 'confirmed',
        ]);

        $confirmResponse->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    public function test_qr_kiosk_generation_and_student_scan_checkin(): void
    {
        $section = CourseSection::with('enrollments')->first();
        $teacher = User::find($section->teacher_id);
        $enrollment = $section->enrollments->first();
        $student = User::find($enrollment->student_id);

        // 1. Teacher opens QR Kiosk view
        $response = $this->actingAs($teacher)->get(route('portal.qr.kiosk', $section->id));
        $response->assertStatus(200);

        $session = AttendanceQrSession::where('course_section_id', $section->id)->first();
        $this->assertNotNull($session);

        // 2. Live status JSON endpoint
        $statusResponse = $this->actingAs($teacher)->get(route('portal.qr.status', $session->id));
        $statusResponse->assertStatus(200);

        // 3. Student scans QR code and views checkin screen
        $scanResponse = $this->actingAs($student)->get(route('portal.qr.scan', $session->session_code));
        $scanResponse->assertStatus(200);

        // 4. Student processes checkin
        $processResponse = $this->actingAs($student)->post(route('portal.qr.process', $session->session_code));
        $processResponse->assertRedirect(route('portal.qr.scan', $session->session_code));

        $this->assertDatabaseHas('attendance_records', [
            'enrollment_id' => $enrollment->id,
            'session_date' => $session->session_date,
            'status' => 'present',
        ]);
    }

    public function test_student_cannot_read_qr_session_live_status(): void
    {
        $section = CourseSection::with('enrollments.student')->first();
        $student = $section->enrollments->first()->student;

        $session = AttendanceQrSession::create([
            'course_section_id' => $section->id,
            'teacher_id' => $section->teacher_id,
            'session_code' => bin2hex(random_bytes(16)),
            'session_date' => now()->toDateString(),
            'expires_at' => now()->addMinutes(15),
            'is_active' => true,
        ]);

        $this->actingAs($student)->get(route('portal.qr.status', $session->id))->assertForbidden();
        $this->actingAs(User::find($section->teacher_id))->get(route('portal.qr.status', $session->id))->assertOk();
    }
}
