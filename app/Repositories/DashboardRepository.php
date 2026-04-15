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
    public function getDashboardData(): array
    {
        $now = Carbon::now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $startOfPreviousMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfPreviousMonth = $now->copy()->subMonth()->endOfMonth();

        return [
            'statistics' => $this->buildStatistics($startOfCurrentMonth, $startOfPreviousMonth, $endOfPreviousMonth),
            'social_assistance' => $this->buildSocialAssistanceSection($startOfCurrentMonth, $startOfPreviousMonth, $endOfPreviousMonth),
            'upcoming_events' => $this->buildUpcomingEvents($now),
            'development_applicants' => $this->buildDevelopmentApplicantsSection($startOfCurrentMonth, $startOfPreviousMonth, $endOfPreviousMonth),
            'population_demographics' => $this->buildPopulationDemographics(),
        ];
    }

    // ──────────────────────────────────────────────
    // Statistics Cards
    // ──────────────────────────────────────────────

    private function buildStatistics(Carbon $startCurrent, Carbon $startPrev, Carbon $endPrev): array
    {
        return [
            'residents' => $this->buildStatCard(
                $this->totalResidents(),
                $this->totalResidentsInRange($startCurrent, now()),
                $this->totalResidentsInRange($startPrev, $endPrev),
            ),
            'head_of_families' => $this->buildStatCard(
                HeadOfFamily::count(),
                HeadOfFamily::where('created_at', '>=', $startCurrent)->count(),
                HeadOfFamily::whereBetween('created_at', [$startPrev, $endPrev])->count(),
            ),
            'developments' => $this->buildStatCard(
                Development::count(),
                Development::where('created_at', '>=', $startCurrent)->count(),
                Development::whereBetween('created_at', [$startPrev, $endPrev])->count(),
            ),
            'events' => $this->buildStatCard(
                Event::count(),
                Event::where('created_at', '>=', $startCurrent)->count(),
                Event::whereBetween('created_at', [$startPrev, $endPrev])->count(),
            ),
        ];
    }

    private function buildStatCard(int $total, int $currentMonth, int $previousMonth): array
    {
        return [
            'total' => $total,
            'growth_percentage' => $this->calculateGrowth($currentMonth, $previousMonth),
        ];
    }

    private function calculateGrowth(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function totalResidents(): int
    {
        return HeadOfFamily::count() + FamilyMember::count();
    }

    private function totalResidentsInRange(Carbon $start, Carbon $end): int
    {
        return HeadOfFamily::whereBetween('created_at', [$start, $end])->count()
            + FamilyMember::whereBetween('created_at', [$start, $end])->count();
    }

    // ──────────────────────────────────────────────
    // Social Assistance Section
    // ──────────────────────────────────────────────

    private function buildSocialAssistanceSection(Carbon $startCurrent, Carbon $startPrev, Carbon $endPrev): array
    {
        $total = SocialAssistanceRecipient::count();
        $currentMonth = SocialAssistanceRecipient::where('created_at', '>=', $startCurrent)->count();
        $previousMonth = SocialAssistanceRecipient::whereBetween('created_at', [$startPrev, $endPrev])->count();

        $latestRecipients = SocialAssistanceRecipient::with([
            'socialAssistance:id,name,amount',
            'headOfFamily:id,user_id',
            'headOfFamily.user:id,name',
        ])
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'amount' => $r->amount,
                'status' => $r->status,
                'created_at' => $r->created_at,
                'recipient_name' => $r->headOfFamily?->user?->name,
                'social_assistance' => [
                    'id' => $r->socialAssistance->id,
                    'name' => $r->socialAssistance->name,
                ],
            ])
            ->values();

        return [
            'total' => $total,
            'growth_percentage' => $this->calculateGrowth($currentMonth, $previousMonth),
            'latest' => $latestRecipients,
        ];
    }

    // ──────────────────────────────────────────────
    // Upcoming Events
    // ──────────────────────────────────────────────

    private function buildUpcomingEvents(Carbon $now): array
    {
        $events = Event::withCount('eventParticipants')
            ->where('date', '>=', $now->toDateString())
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'date' => $e->date,
                'time' => $e->time,
                'price' => $e->price,
                'thumbnail' => $e->thumbnail ? asset('storage/' . $e->thumbnail) : null,
                'participants_count' => $e->event_participants_count,
            ])
            ->values();

        return [
            'total_upcoming' => $events->count(),
            'events' => $events,
        ];
    }

    // ──────────────────────────────────────────────
    // Development Applicants Section
    // ──────────────────────────────────────────────

    private function buildDevelopmentApplicantsSection(Carbon $startCurrent, Carbon $startPrev, Carbon $endPrev): array
    {
        $total = DevelopmentApplicant::count();
        $currentMonth = DevelopmentApplicant::where('created_at', '>=', $startCurrent)->count();
        $previousMonth = DevelopmentApplicant::whereBetween('created_at', [$startPrev, $endPrev])->count();

        $latestApplicants = DevelopmentApplicant::with([
            'development:id,name,thumbnail',
            'user:id,name,profile_picture',
        ])
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'status' => $a->status,
                'created_at' => $a->created_at,
                'user' => [
                    'id' => $a->user->id,
                    'name' => $a->user->name,
                    'profile_picture' => $a->user->profile_picture
                        ? asset('storage/' . $a->user->profile_picture)
                        : null,
                ],
                'development' => [
                    'id' => $a->development->id,
                    'name' => $a->development->name,
                    'thumbnail' => $a->development->thumbnail
                        ? asset('storage/' . $a->development->thumbnail)
                        : null,
                ],
            ])
            ->values();

        return [
            'total' => $total,
            'growth_percentage' => $this->calculateGrowth($currentMonth, $previousMonth),
            'latest' => $latestApplicants,
        ];
    }

    // ──────────────────────────────────────────────
    // Population Demographics
    // ──────────────────────────────────────────────

    private function buildPopulationDemographics(): array
    {
        $now = Carbon::now();

        // Gather all residents (head of families + family members) with gender & date_of_birth
        $heads = HeadOfFamily::select('gender', 'date_of_birth')->get();
        $members = FamilyMember::select('gender', 'date_of_birth')->get();
        $allResidents = $heads->concat($members);

        $totalResidents = $allResidents->count();

        // Gender breakdown
        $maleCount = $allResidents->where('gender', 'male')->count();
        $femaleCount = $allResidents->where('gender', 'female')->count();

        // Age groups
        $balita = $allResidents->filter(fn($r) => Carbon::parse($r->date_of_birth)->age <= 5)->count();
        $anakAnak = $allResidents->filter(fn($r) => Carbon::parse($r->date_of_birth)->age >= 6 && Carbon::parse($r->date_of_birth)->age <= 12)->count();
        $remaja = $allResidents->filter(fn($r) => Carbon::parse($r->date_of_birth)->age >= 13 && Carbon::parse($r->date_of_birth)->age <= 17)->count();
        $dewasa = $allResidents->filter(fn($r) => Carbon::parse($r->date_of_birth)->age >= 18 && Carbon::parse($r->date_of_birth)->age <= 59)->count();
        $lansia = $allResidents->filter(fn($r) => Carbon::parse($r->date_of_birth)->age >= 60)->count();

        // Find dominant age range per gender
        $maleDominantAge = $this->findDominantAgeRange($allResidents->where('gender', 'male'));
        $femaleDominantAge = $this->findDominantAgeRange($allResidents->where('gender', 'female'));

        return [
            'total' => $totalResidents,
            'gender' => [
                'male' => [
                    'count' => $maleCount,
                    'dominant_age_range' => $maleDominantAge,
                ],
                'female' => [
                    'count' => $femaleCount,
                    'dominant_age_range' => $femaleDominantAge,
                ],
            ],
            'age_groups' => [
                'balita' => ['label' => '0-5 tahun', 'count' => $balita],
                'anak_anak' => ['label' => '6-12 tahun', 'count' => $anakAnak],
                'remaja' => ['label' => '13-17 tahun', 'count' => $remaja],
                'dewasa' => ['label' => '18-59 tahun', 'count' => $dewasa],
                'lansia' => ['label' => '60+ tahun', 'count' => $lansia],
            ],
        ];
    }

    private function findDominantAgeRange(\Illuminate\Support\Collection $residents): string
    {
        if ($residents->isEmpty()) {
            return '-';
        }

        $ageRanges = [
            '0-5' => 0,
            '6-12' => 0,
            '13-17' => 0,
            '18-25' => 0,
            '26-31' => 0,
            '32-36' => 0,
            '37-45' => 0,
            '46-59' => 0,
            '60+' => 0,
        ];

        foreach ($residents as $r) {
            $age = Carbon::parse($r->date_of_birth)->age;
            match (true) {
                $age <= 5 => $ageRanges['0-5']++,
                $age <= 12 => $ageRanges['6-12']++,
                $age <= 17 => $ageRanges['13-17']++,
                $age <= 25 => $ageRanges['18-25']++,
                $age <= 31 => $ageRanges['26-31']++,
                $age <= 36 => $ageRanges['32-36']++,
                $age <= 45 => $ageRanges['37-45']++,
                $age <= 59 => $ageRanges['46-59']++,
                default => $ageRanges['60+']++,
            };
        }

        $dominant = array_keys($ageRanges, max($ageRanges))[0];

        return $dominant;
    }

    // ──────────────────────────────────────────────
    // Head of Family Dashboard (unchanged)
    // ──────────────────────────────────────────────

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
            'family_members' => $familyMembers->map(fn($m) => [
                'id' => $m->id,
                'identity_number' => $m->identity_number,
                'occupation' => $m->occupation,
                'age' => Carbon::parse($m->date_of_birth)->age,
                'profile_picture' => $m->profile_picture ? asset('storage/' . $m->profile_picture) : null,
                'user' => [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                ],
            ])->values(),
            'social_assistance_recipients' => $socialAssistanceRecipients->map(fn($r) => [
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
            'event_participants' => $eventParticipants->map(fn($p) => [
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
                    'thumbnail' => asset('storage/' . $p->event->thumbnail),
                    'participants_count' => $p->event->event_participants_count,
                ],
            ])->values(),
            'development_applicants' => $developmentApplicants->map(fn($a) => [
                'id' => $a->id,
                'status' => $a->status,
                'created_at' => $a->created_at,
                'development' => [
                    'id' => $a->development->id,
                    'name' => $a->development->name,
                    'thumbnail' => asset('storage/' . $a->development->thumbnail),
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
