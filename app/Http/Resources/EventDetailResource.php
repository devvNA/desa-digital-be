<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventDetailResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'date' => $this->date,
            'time' => $this->time ? Carbon::parse($this->time)->format('H:i') : null,
            'is_active' => $this->is_active,
            'participants_count' => $this->eventParticipants->count(),
            'recent_participants' => $this->eventParticipants
                ->sortByDesc('created_at')
                ->take(5)
                ->values()
                ->map(fn ($participant) => [
                    'name' => $participant->headOfFamily?->user?->name,
                    'occupation' => $participant->headOfFamily?->occupation,
                    'profile_picture' => $participant->headOfFamily?->profile_picture
                        ? asset('storage/'.$participant->headOfFamily->profile_picture)
                        : null,
                ]),
        ];
    }
}
