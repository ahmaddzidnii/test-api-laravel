<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncProjectTechnologiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'technologies' => ['present', 'array'],
            'technologies.*.id' => ['required', 'integer', 'exists:technologies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'technologies.required' => 'Technologies array is required',
            'technologies.array' => 'Technologies must be an array',
            'technologies.*.id.required' => 'Technology ID is required',
            'technologies.*.id.integer' => 'Technology ID must be an integer',
            'technologies.*.id.exists' => 'Technology with this ID does not exist',
        ];
    }
}
