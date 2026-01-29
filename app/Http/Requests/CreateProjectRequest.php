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

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'title.max' => 'Title cannot exceed 255 characters',

            'description.required' => 'Description is required',
            'description.string' => 'Description must be a string',

            'slug.required' => 'Slug is required',
            'slug.string' => 'Slug must be a string',
            'slug.max' => 'Slug cannot exceed 255 characters',
            'slug.regex' => 'Slug must be lowercase letters, numbers, and hyphens only',
            'slug.unique' => 'Slug is already in use',

            'linkDemo.string' => 'Link demo must be a string',
            'linkDemo.url' => 'Link demo must be a valid URL',
            'linkDemo.max' => 'Link demo cannot exceed 255 characters',

            'duration.string' => 'Duration must be a string',
            'duration.max' => 'Duration cannot exceed 100 characters',

            'launchYear.string' => 'Launch year must be a string',
            'launchYear.regex' => 'Launch year must be a 4-digit year',
        ];
    }
}
