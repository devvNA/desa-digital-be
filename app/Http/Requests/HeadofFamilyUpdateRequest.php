<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\HeadOfFamily;
use Illuminate\Validation\Rule;

class HeadofFamilyUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        // Get the HeadOfFamily ID from the route parameter
        $headOfFamilyId = $this->route('head_of_family');
        $headOfFamily = HeadOfFamily::find($headOfFamilyId);
        $userId = $headOfFamily ? $headOfFamily->user_id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identity_number' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'date_of_birth' => 'required|date',
            'phone_number' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'marital_status' => 'required|string|in:single,married',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Kata Sandi',
            'profile_picture' => 'Foto Profil',
            'identity_number' => 'Nomor Identitas',
            'gender' => 'Jenis Kelamin',
            'date_of_birth' => 'Tanggal Lahir',
            'phone_number' => 'Nomor Telepon',
            'occupation' => 'Pekerjaan',
            'marital_status' => 'Status Pernikahan',
        ];
    }
}
