<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'required|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ad və soyad mütləq daxil edilməlidir.',
            'email.required' => 'E-poçt ünvanı mütləqdir.',
            'email.unique' => 'Bu e-poçt ünvanı artıq qeydiyyatdan keçib.',
            'phone.required' => 'Əlaqə nömrəsi mütləqdir.',
            'password.required' => 'Şifrə daxil edilməlidir.',
            'password.min' => 'Şifrə ən az 8 simvoldan ibarət olmalıdır.',
            'password.confirmed' => 'Şifrənin təkrarı uyğun gəlmir.',
        ];
    }
}
