<?php

namespace App\Repositories;

use App\Interfaces\DashboardRepositoryInterface;
use App\Models\Development;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use App\Models\SocialAssistanceRecipient;

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
}
