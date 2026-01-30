<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectBasicInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'about' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['PUBLIC', 'PRIVATE'])],
            'duration' => ['sometimes', 'nullable', 'string', 'max:100'],
            'launchYear' => ['sometimes', 'nullable', 'string', 'max:4'],
            'demoUrl' => ['sometimes', 'nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Title must be a string',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'about.string' => 'About must be a string',
            'status.in' => 'Status must be one of: DRAFT, PRODUCTION, ARCHIVED',
            'duration.string' => 'Duration must be a string',
            'duration.max' => 'Duration cannot exceed 100 characters',
            'launchYear.string' => 'Launch year must be a string',
            'launchYear.max' => 'Launch year cannot exceed 4 characters',
            'demoUrl.url' => 'Demo URL must be a valid URL',
            'demoUrl.max' => 'Demo URL cannot exceed 500 characters',
        ];
    }
}
