<?php

namespace Database\Factories;

use App\Models\AttendanceQrSession;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceQrSessionFactory extends Factory
{
    protected $model = AttendanceQrSession::class;

    public function definition(): array
    {
        return [
            'course_section_id' => CourseSection::factory(),
            'teacher_id' => User::factory(),
            'session_code' => bin2hex(random_bytes(16)),
            'session_date' => now()->toDateString(),
            'expires_at' => now()->addMinutes(20),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(10),
            'is_active' => false,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addMinutes(15),
            'is_active' => true,
        ]);
    }
}
