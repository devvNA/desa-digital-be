<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profilePicture = $this->profile_picture
            ?? $this->headOfFamily?->profile_picture
            ?? $this->familyMembers?->profile_picture;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_picture' => $profilePicture ? asset('storage/'.$profilePicture) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
