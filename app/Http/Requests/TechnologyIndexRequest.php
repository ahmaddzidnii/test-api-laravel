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

            'page'     => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Optional: default value
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_dir' => $this->sort_dir ?? 'asc',
            'per_page' => $this->per_page ?? 50,
        ]);
    }
}
