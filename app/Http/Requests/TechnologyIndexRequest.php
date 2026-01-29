<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TechnologyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'   => ['nullable', 'string', 'max:100'],

            'sort_by'  => [
                'nullable',
                'string',
                'in:name,created_at'
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

    public function messages(): array
    {
        return [
            'search.string' => 'Search must be a string',
            'search.max' => 'Search cannot exceed :max characters',

            'sort_by.string' => 'Sort by must be a string',
            'sort_by.in' => 'Sort by must be one of: name, created_at',

            'sort_dir.string' => 'Sort direction must be a string',
            'sort_dir.in' => 'Sort direction must be either asc or desc',

            'page.integer' => 'Page must be an integer',
            'page.min' => 'Page must be at least :min',

            'per_page.integer' => 'Per page must be an integer',
            'per_page.min' => 'Per page must be at least :min',
            'per_page.max' => 'Per page cannot exceed :max',
        ];
    }

    /**
     * Optional: default value
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_by'  => $this->sort_by ?? config('pagination.resources.technologies.sort_by'),
            'sort_dir' => $this->sort_dir ?? config('pagination.resources.technologies.sort_direction'),
            'per_page' => $this->per_page ?? config('pagination.resources.technologies.per_page'),
        ]);
    }
}
