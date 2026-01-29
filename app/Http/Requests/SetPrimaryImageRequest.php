<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPrimaryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imageId' => ['required', 'integer', 'exists:project_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'imageId.required' => 'Image ID is required',
            'imageId.integer' => 'Image ID must be an integer',
            'imageId.exists' => 'Image with this ID does not exist',
        ];
    }

    /**
     * Prepare data for validation by getting imageId from route parameter
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'imageId' => $this->route('imageId'),
        ]);
    }
}
