<?php

namespace App\Http\Requests;

use App\Models\Enrollment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $enrollment = $this->route('id') ? Enrollment::find($this->route('id')) : null;

        return $enrollment ? $this->user()->can('update', $enrollment) : false;
    }

    public function rules(): array
    {
        return [
            'midterm_score' => 'required|numeric|min:0|max:50',
            'final_score' => 'required|numeric|min:0|max:50',
            'attendance_percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'midterm_score.required' => 'Aralıq imtahan balı mütləq daxil edilməlidir.',
            'midterm_score.max' => 'Aralıq imtahan balı maksimum 50 ola bilər.',
            'final_score.required' => 'Yekun imtahan balı mütləq daxil edilməlidir.',
            'final_score.max' => 'Yekun imtahan balı maksimum 50 ola bilər.',
            'attendance_percentage.required' => 'Davamiyyət faizi daxil edilməlidir.',
            'attendance_percentage.max' => 'Davamiyyət maksimum 100% ola bilər.',
        ];
    }
}
