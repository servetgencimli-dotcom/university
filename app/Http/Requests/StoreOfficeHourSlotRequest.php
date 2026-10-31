<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeHourSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isTeacher() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration_minutes' => 'required|integer|min:5|max:120',
            'max_students_per_slot' => 'required|integer|min:1|max:10',
            'location' => 'nullable|string|max:255',
            'zoom_link' => 'nullable|url|max:255',
            'is_recurring' => 'nullable|boolean',
        ];
    }
}
