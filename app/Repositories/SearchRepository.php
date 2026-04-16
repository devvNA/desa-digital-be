<?php

namespace App\Repositories;

use App\Interfaces\SearchRepositoryInterface;
use App\Models\Development;
use App\Models\Event;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Support\Facades\DB;

class SearchRepository implements SearchRepositoryInterface
{
    public function search(string $query): array
    {
        return [
            'social_assistances' => $this->searchSocialAssistances($query),
            'social_assistance_recipients' => $this->searchSocialAssistanceRecipients($query),
            'developments' => $this->searchDevelopments($query),
            'events' => $this->searchEvents($query),
        ];
    }

    // ──────────────────────────────────────────────
    // List Bansos — programs linked to matching head-of-family
    // ──────────────────────────────────────────────

    private function searchSocialAssistances(string $query): array
    {
        $results = SocialAssistance::withCount('socialAssistanceRecipient')
            ->withSum('socialAssistanceRecipient', 'amount')
            ->whereHas('socialAssistanceRecipient.headOfFamily.user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        $items = $results->map(function ($sa) {
            $distributed = (float) ($sa->social_assistance_recipient_sum_amount ?? 0);

            return [
                'id' => $sa->id,
                'name' => $sa->name,
                'thumbnail' => $sa->thumbnail ? asset('storage/' . $sa->thumbnail) : null,
                'provider' => $sa->provider,
                'amount' => $sa->amount,
                'category' => $sa->category,
                'remaining_amount' => max(0, (float) $sa->amount - $distributed),
                'total_recipients' => $sa->social_assistance_recipient_count,
            ];
        })->values();

        return [
            'total' => $items->count(),
            'data' => $items,
        ];
    }

    // ──────────────────────────────────────────────
    // Pengajuan Bansos — recipient records from matching head-of-family
    // ──────────────────────────────────────────────

    private function searchSocialAssistanceRecipients(string $query): array
    {
        $results = SocialAssistanceRecipient::with([
            'socialAssistance' => function ($q) {
                $q->withCount('socialAssistanceRecipient')
                    ->withSum('socialAssistanceRecipient', 'amount');
            },
        ])
            ->whereHas('headOfFamily.user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        $items = $results->map(function ($r) {
            $sa = $r->socialAssistance;
            $distributed = (float) ($sa->social_assistance_recipient_sum_amount ?? 0);

            return [
                'id' => $r->id,
                'status' => $r->status,
                'amount' => $r->amount,
                'created_at' => $r->created_at,
                'social_assistance' => [
                    'id' => $sa->id,
                    'name' => $sa->name,
                    'thumbnail' => $sa->thumbnail ? asset('storage/' . $sa->thumbnail) : null,
                    'provider' => $sa->provider,
                    'amount' => $sa->amount,
                    'remaining_amount' => max(0, (float) $sa->amount - $distributed),
                    'total_recipients' => $sa->social_assistance_recipient_count,
                ],
            ];
        })->values();

        return [
            'total' => $items->count(),
            'data' => $items,
        ];
    }

    // ──────────────────────────────────────────────
    // Pembangunan — developments linked to matching applicant
    // ──────────────────────────────────────────────

    private function searchDevelopments(string $query): array
    {
        $results = Development::withCount('developmentApplicants')
            ->whereHas('developmentApplicants.user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        $items = $results->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'thumbnail' => $d->thumbnail ? asset('storage/' . $d->thumbnail) : null,
            'person_in_charge' => $d->person_in_charge,
            'amount' => $d->amount,
            'total_applicants' => $d->development_applicants_count,
            'start_date' => $d->start_date,
        ])->values();

        return [
            'total' => $items->count(),
            'data' => $items,
        ];
    }

    // ──────────────────────────────────────────────
    // Event Desa — events linked to matching participant
    // ──────────────────────────────────────────────

    private function searchEvents(string $query): array
    {
        $results = Event::withCount('eventParticipants')
            ->whereHas('eventParticipants.headOfFamily.user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        $items = $results->map(fn($e) => [
            'id' => $e->id,
            'name' => $e->name,
            'thumbnail' => $e->thumbnail ? asset('storage/' . $e->thumbnail) : null,
            'date' => $e->date,
            'time' => $e->time,
            'price' => $e->price,
            'is_active' => $e->is_active,
            'total_participants' => $e->event_participants_count,
        ])->values();

        return [
            'total' => $items->count(),
            'data' => $items,
        ];
    }
}
