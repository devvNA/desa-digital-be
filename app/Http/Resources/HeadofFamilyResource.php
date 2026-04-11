<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeadofFamilyResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'profile_picture' => asset('storage/' . $this->profile_picture),
            'identity_number' => $this->identity_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'age' => \Carbon\Carbon::parse($this->date_of_birth)->age,
            'phone_number' => $this->phone_number,
            'occupation' => $this->occupation,
            'marital_status' => $this->marital_status,
            'family_member' => $this->whenLoaded('familyMembers', function () {
                return $this->familyMembers->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'user' => [
                            'id' => $member->user->id,
                            'name' => $member->user->name,
                            'email' => $member->user->email,
                        ],
                        'profile_picture' => asset('storage/' . $member->profile_picture),
                        'identity_number' => $member->identity_number,
                        'gender' => $member->gender,
                        'date_of_birth' => $member->date_of_birth,
                        'age' => \Carbon\Carbon::parse($member->date_of_birth)->age,
                        'phone_number' => $member->phone_number,
                        'occupation' => $member->occupation,
                        'marital_status' => $member->marital_status,
                        'relation' => $member->relation,
                    ];
                });
            }),
        ];
    }
}
