<?php

namespace App\Http\Requests;

use App\Models\AdmissionsApplication;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $application = $this->route('id') ? AdmissionsApplication::find($this->route('id')) : null;

        return $application ? $this->user()->can('updateStatus', $application) : false;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:submitted,under_review,interview_scheduled,accepted,rejected',
            'reviewer_notes' => 'nullable|string|max:1000',
        ];
    }
}
