<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'about' => 'required|string',
            'headman' => 'required|string|max:255',
            'people' => 'required|integer',
            'address' => 'required|string|max:255',
            'agricultural_area' => 'required|numeric',
            'total_area' => 'required|numeric',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail' => 'Thumbnail',
            'name' => 'Nama',
            'about' => 'Deskripsi',
            'headman' => 'Kepala Desa',
            'people' => 'Jumlah Penduduk',
            'agricultural_area' => 'Luas Lahan Pertanian',
            'total_area' => 'Luas Lahan',
            'images' => 'Gambar',
        ];
    }
}
