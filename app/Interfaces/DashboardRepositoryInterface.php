<?php

namespace App\Interfaces;

interface DashboardRepositoryInterface
{
    public function getDashboardData(): array;

    public function getHeadOfFamilyDashboard(string $userId): array;
}
