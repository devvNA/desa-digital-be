<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail' => 'Thumbnail',
            'name' => 'Nama',
            'description' => 'Deskripsi',
            'price' => 'Harga',
            'date' => 'Tanggal',
            'time' => 'Waktu',
            'is_active' => 'Status Aktif',
        ];
    }
}
