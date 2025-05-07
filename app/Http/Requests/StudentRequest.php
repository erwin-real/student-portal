<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            // 'first_name' => ['required', 'string', 'max:50'],
            // 'middle_name' => ['required', 'string', 'max:50'],
            // 'last_name' => ['required', 'string', 'max:50'],
            // 'gender' => ['required', 'string'],
            // 'address' => ['required', 'string', 'min:10'],
            // 'date_of_birth' => ['required', 'date'],
            // 'email' => ['nullable', 'email'],
            // 'mobile_no' => ['nullable', 'numeric'],
            // 'level' => ['nullable', 'string'],
            // 'section' => ['nullable', 'string']

            'first_name' => ['nullable'],
            'middle_name' => ['nullable'],
            'last_name' => ['nullable'],
            'gender' => ['nullable'],
            'address' => ['nullable'],
            'date_of_birth' => ['nullable'],
            'email' => ['nullable'],
            'mobile_no' => ['nullable'],
            'level_id' => ['nullable'],
            'section_id' => ['nullable']
        ];
    }
}
