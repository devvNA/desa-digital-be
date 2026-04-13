<?php

namespace App\Repositories;

use App\Interfaces\DashboardRepositoryInterface;
use App\Models\Development;
use App\Models\DevelopmentApplicant;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use App\Models\SocialAssistanceRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getDashboardData()
    {
        try {
            return [
                'residents' => HeadOfFamily::count() + FamilyMember::count(),
                'head_of_families' => HeadOfFamily::count(),
                'social_assistances' => SocialAssistanceRecipient::count(),
                'events' => Event::count(),
                'developments' => Development::count(),
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getHeadOfFamilyDashboard(string $userId): array
    {
        $headOfFamily = HeadOfFamily::where('user_id', $userId)->first();

        if (! $headOfFamily) {
            throw new ModelNotFoundException('Head of family profile not found.');
        }

        $headOfFamilyId = $headOfFamily->id;

        // --- Counts (single query each, no eager load needed) ---
        $familyMembersCount = FamilyMember::where('head_of_family_id', $headOfFamilyId)->count();
        $socialAssistanceRecipientsCount = SocialAssistanceRecipient::where('head_of_family_id', $headOfFamilyId)->count();
        $eventParticipantsCount = EventParticipant::where('head_of_family_id', $headOfFamilyId)->count();
        $developmentApplicantsCount = DevelopmentApplicant::where('user_id', $userId)->count();

        // --- Preview lists (latest 5, eager loaded) ---
        $familyMembers = FamilyMember::with('user:id,name')
            ->where('head_of_family_id', $headOfFamilyId)
            ->latest()
            ->take(5)
            ->get();

        $socialAssistanceRecipients = SocialAssistanceRecipient::with('socialAssistance:id,name,amount')
            ->where('head_of_family_id', $headOfFamilyId)
            ->latest()
            ->take(5)
            ->get();

        $eventParticipants = EventParticipant::with(['event' => function ($query) {
            $query->withCount('eventParticipants');
        }])
            ->where('head_of_family_id', $headOfFamilyId)
            ->latest()
            ->take(5)
            ->get();

        $developmentApplicants = DevelopmentApplicant::with('development:id,name,thumbnail,person_in_charge,start_date,end_date')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // --- Shape response ---
        return [
            'summary' => [
                'family_members_count' => $familyMembersCount,
                'social_assistance_recipients_count' => $socialAssistanceRecipientsCount,
                'event_participants_count' => $eventParticipantsCount,
                'development_applicants_count' => $developmentApplicantsCount,
            ],
            'family_members' => $familyMembers->map(fn ($m) => [
                'id' => $m->id,
                'identity_number' => $m->identity_number,
                'occupation' => $m->occupation,
                'age' => Carbon::parse($m->date_of_birth)->age,
                'profile_picture' => $m->profile_picture ? asset('storage/'.$m->profile_picture) : null,
                'user' => [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                ],
            ])->values(),
            'social_assistance_recipients' => $socialAssistanceRecipients->map(fn ($r) => [
                'id' => $r->id,
                'created_at' => $r->created_at,
                'status' => $r->status,
                'amount' => $r->amount,
                'social_assistance' => [
                    'id' => $r->socialAssistance->id,
                    'name' => $r->socialAssistance->name,
                    'amount' => $r->socialAssistance->amount,
                ],
            ])->values(),
            'event_participants' => $eventParticipants->map(fn ($p) => [
                'id' => $p->id,
                'quantity' => $p->quantity,
                'total_price' => $p->total_price,
                'created_at' => $p->created_at,
                'event' => [
                    'id' => $p->event->id,
                    'name' => $p->event->name,
                    'date' => $p->event->date,
                    'time' => $p->event->time,
                    'price' => $p->event->price,
                    'thumbnail' => asset('storage/'.$p->event->thumbnail),
                    'participants_count' => $p->event->event_participants_count,
                ],
            ])->values(),
            'development_applicants' => $developmentApplicants->map(fn ($a) => [
                'id' => $a->id,
                'status' => $a->status,
                'created_at' => $a->created_at,
                'development' => [
                    'id' => $a->development->id,
                    'name' => $a->development->name,
                    'thumbnail' => asset('storage/'.$a->development->thumbnail),
                    'person_in_charge' => $a->development->person_in_charge,
                    'start_date' => $a->development->start_date,
                    'days_needed' => $a->development->start_date && $a->development->end_date
                        ? $a->development->start_date->diffInDays($a->development->end_date)
                        : 0,
                ],
            ])->values(),
        ];
    }
}
