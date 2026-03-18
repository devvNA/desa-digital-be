<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceRecipientStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'social_assistance_id' => 'required|exists:social_assistances,id',
            'head_of_family_id' => 'required|exists:head_of_families,id',
            'bank' => 'required|string|in:BRI,BNI,MANDIRI,BCA,PERMATA',
            'amount' => 'required|numeric',
            'reason' => 'required|string',
            'account_number' => 'required',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:pending,approved,rejected',
        ];
    }

    public function attributes(): array
    {
        return [
            'social_assistance_id' => 'Bantuan Sosial',
            'head_of_family_id' => 'Kepala Keluarga',
            'bank' => 'Bank',
            'amount' => 'Jumlah Bantuan',
            'reason' => 'Alasan',
            'account_number' => 'Nomor Rekening',
            'proof' => 'Bukti',
            'status' => 'Status',
        ];
    }
}
