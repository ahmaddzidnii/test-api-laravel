<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncProjectDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'aboutProject' => ['sometimes', 'string'],

            'features' => ['sometimes', 'array'],
            'features.*.feature' => ['required', 'string', 'max:500'],

            'challenges' => ['sometimes', 'array'],
            'challenges.*.challenge' => ['required', 'string', 'max:1000'],

            'results' => ['sometimes', 'array'],
            'results.*.result' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'aboutProject.string' => 'About project must be a string',

            'features.array' => 'Features must be an array',
            'features.*.feature.required' => 'Feature description is required',
            'features.*.feature.string' => 'Feature must be a string',
            'features.*.feature.max' => 'Feature cannot exceed 500 characters',

            'challenges.array' => 'Challenges must be an array',
            'challenges.*.challenge.required' => 'Challenge description is required',
            'challenges.*.challenge.string' => 'Challenge must be a string',
            'challenges.*.challenge.max' => 'Challenge cannot exceed 1000 characters',

            'results.array' => 'Results must be an array',
            'results.*.result.required' => 'Result description is required',
            'results.*.result.string' => 'Result must be a string',
            'results.*.result.max' => 'Result cannot exceed 1000 characters',
        ];
    }
}
