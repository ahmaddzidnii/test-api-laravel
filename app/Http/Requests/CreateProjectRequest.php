<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:projects,slug',
            ],
            'linkDemo' => [
                'nullable',
                'string',
                'url',
                'max:255',
            ],
            'duration' => [
                'nullable',
                'string',
                'max:100',
            ],
            'launchYear' => [
                'nullable',
                'string',
                'regex:/^\d{4}$/',
            ],
        ];
    }
}
