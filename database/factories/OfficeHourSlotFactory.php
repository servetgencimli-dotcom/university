<?php

namespace Database\Factories;

use App\Models\OfficeHourSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeHourSlotFactory extends Factory
{
    protected $model = OfficeHourSlot::class;

    public function definition(): array
    {
        return [
            'teacher_id' => User::factory(),
            'day_of_week' => fake()->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'slot_duration_minutes' => 15,
            'max_students_per_slot' => 2,
            'location' => 'Otaq '.fake()->numberBetween(100, 500),
            'zoom_link' => null,
            'is_recurring' => true,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'zoom_link' => 'https://zoom.us/j/'.fake()->numerify('##########'),
        ]);
    }

    public function singleSession(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => false,
        ]);
    }
}
