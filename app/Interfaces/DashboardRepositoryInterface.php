<?php

namespace App\Interfaces;

interface DashboardRepositoryInterface
{
    public function getDashboardData();

    public function getHeadOfFamilyDashboard(string $userId): array;
}
