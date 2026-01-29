<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'testimonial' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatarUrl' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',

            'role.required' => 'Role is required',
            'role.string' => 'Role must be a string',
            'role.max' => 'Role cannot exceed 255 characters',

            'testimonial.required' => 'Testimonial is required',
            'testimonial.string' => 'Testimonial must be a string',

            'rating.required' => 'Rating is required',
            'rating.integer' => 'Rating must be an integer',
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating cannot exceed 5',

            'avatarUrl.url' => 'Avatar URL must be a valid URL',
            'avatarUrl.max' => 'Avatar URL cannot exceed 500 characters',
        ];
    }
}
