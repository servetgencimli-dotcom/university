<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveGradeComponentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'components' => 'required|array|min:1',
            'components.*.id' => 'nullable|integer',
            'components.*.name' => 'required|string|max:100',
            'components.*.max_score' => 'required|numeric|min:0.5|max:100',
            'components.*.type' => 'nullable|string|max:50',
        ];
    }
}
