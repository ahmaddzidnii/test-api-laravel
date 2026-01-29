<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'userId' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'role' => [
                'required',
                'string',
                Rule::in(['ADMIN', 'SUPER_ADMIN']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'userId.required' => 'User ID is required',
            'userId.integer' => 'User ID must be an integer',
            'userId.exists' => 'User with this ID does not exist',

            'role.required' => 'Role is required',
            'role.string' => 'Role must be a string',
            'role.in' => 'Role must be either ADMIN or SUPER_ADMIN',
        ];
    }
}
