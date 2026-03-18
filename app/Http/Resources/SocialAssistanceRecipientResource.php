<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceRecipientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'social_assistance' => new SocialAssistanceResource($this->socialAssistance),
            'head_of_family' => new HeadOfFamilyResource($this->headOfFamily),
            'bank' => $this->bank,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'account_number' => $this->account_number,
            'proof' => $this->proof,
            'status' => $this->status,
        ];
    }
}
