<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:system_admin,admin,teacher,student,master_student,applicant',
            'role_title' => 'nullable|string|max:255',
            'student_id' => 'nullable|string|unique:users,student_id',
            'staff_id' => 'nullable|string|unique:users,staff_id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'degree_level' => 'nullable|in:bachelor,master,phd,staff',
            'status' => 'required|in:active,graduated,suspended,pending',
            'phone' => 'nullable|string|max:50',
            'permissions' => 'nullable|array',
        ];
    }
}
