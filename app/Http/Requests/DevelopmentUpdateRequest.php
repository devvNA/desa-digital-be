<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DevelopmentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'person_in_charge' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount' => 'required|numeric',
            'status' => 'required|in:ongoing,completed',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail' => 'Thumbnail',
            'name' => 'Nama',
            'description' => 'Deskripsi',
            'person_in_charge' => 'Penanggung Jawab',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'amount' => 'Jumlah Dana',
            'status' => 'Status',
        ];
    }
}
