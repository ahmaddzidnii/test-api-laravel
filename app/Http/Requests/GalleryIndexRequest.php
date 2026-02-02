<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryIndexRequest extends FormRequest
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
            'search'   => ['nullable', 'string', 'max:100'],

            'sort_by'  => [
                'nullable',
                'string',
                'in:title,launch_year,created_at'
            ],

            'sort_dir' => [
                'nullable',
                'string',
                'in:asc,desc'
            ],

            'page'     => ['nullable', 'integer', 'min:' . config('pagination.min_per_page')],
            'per_page' => ['nullable', 'integer', 'min:' . config('pagination.min_per_page'), 'max:' . config('pagination.max_per_page')],
        ];
    }
}
