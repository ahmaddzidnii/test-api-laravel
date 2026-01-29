<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
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

    /**
     * Optional: default value
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_by'  => $this->sort_by ?? config('pagination.resources.projects.sort_by'),
            'sort_dir' => $this->sort_dir ?? config('pagination.resources.projects.sort_direction'),
            'per_page' => $this->per_page ?? config('pagination.resources.projects.per_page'),
        ]);
    }
}
