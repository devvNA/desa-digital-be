<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevelopmentResource extends JsonResource
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
            'thumbnail' => asset('storage/' . $this->thumbnail),
            'name' => $this->name,
            'description' => $this->description,
            'person_in_charge' => $this->person_in_charge,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days_needed' => $this->start_date->diffInDays($this->end_date) ,
            'amount' => $this->amount,
            'development_applicants' => DevelopmentApplicantResource::collection($this->whenLoaded('developmentApplicants')),
            'status' => $this->status,
            'applicants_count' => $this->development_applicants_count
                ?? ($this->relationLoaded('developmentApplicants') ? $this->developmentApplicants->count() : 0),
        ];
    }
}
