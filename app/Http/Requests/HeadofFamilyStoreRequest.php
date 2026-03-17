<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HeadofFamilyStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute harus berupa teks',
            'email' => ':attribute harus berupa email yang valid',
            'unique:users' => ':attribute sudah terdaftar',
            'min' => ':attribute minimal :min karakter',
            'max' => ':attribute maksimal :max karakter',
            'image' => ':attribute harus berupa gambar',
            'mimes' => ':attribute harus berupa file gambar dengan format jpeg, png, jpg, gif',
            'date' => ':attribute harus berupa tanggal',
            'in' => ':attribute harus berupa salah satu dari: :values',
        ];
    }
}
