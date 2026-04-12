<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DevelopmentApplicantStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'development_id' => 'required|exists:developments,id',
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('development_applicants', 'user_id')
                    ->where('development_id', $this->input('development_id'))
                    ->whereNull('deleted_at'),
            ],
            'status' => 'nullable|in:pending,approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'User ini sudah terdaftar pada pembangunan yang dipilih.',
        ];
    }

    public function attributes()
    {
        return [
            'development_id' => 'Pembangunan',
            'user_id' => 'User',
            'status' => 'Status',
        ];
    }
}
