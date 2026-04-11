<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceDetailResource extends JsonResource
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
            'thumbnail' => asset('storage/'.$this->thumbnail),
            'name' => $this->name,
            'category' => $this->category,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'description' => $this->description,
            'is_available' => $this->is_available,
            'recipients_count' => $this->socialAssistanceRecipient->count(),
            'recent_recipients' => $this->socialAssistanceRecipient
                ->sortByDesc('created_at')
                ->take(3)
                ->values()
                ->map(fn ($recipient) => [
                    'id' => $recipient->id,
                    'amount' => $recipient->amount,
                    'status' => $recipient->status,
                    'created_at' => $recipient->created_at,
                    'recipient_name' => $recipient->headOfFamily?->user?->name,
                    'recipient_profile_picture' => $recipient->headOfFamily?->profile_picture
                        ? asset('storage/'.$recipient->headOfFamily->profile_picture)
                        : null,
                ]),
        ];
    }
}
