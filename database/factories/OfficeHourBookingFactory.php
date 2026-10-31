<?php

namespace Database\Factories;

use App\Models\OfficeHourBooking;
use App\Models\OfficeHourSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeHourBookingFactory extends Factory
{
    protected $model = OfficeHourBooking::class;

    public function definition(): array
    {
        return [
            'slot_id' => OfficeHourSlot::factory(),
            'student_id' => User::factory(),
            'booking_date' => now()->addDays(2)->toDateString(),
            'start_time' => '14:00',
            'end_time' => '14:15',
            'topic' => 'Kurs işi haqqında məsləhətləşmə',
            'status' => 'pending',
            'cancellation_reason' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function cancelled(?string $reason = 'Müəllim məşğuldur'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }
}
