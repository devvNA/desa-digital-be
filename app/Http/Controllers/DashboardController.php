<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DashboardController extends Controller
{
    private DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function index()
    {
        try {
            $data = $this->dashboardRepository->getDashboardData();

            return ResponseHelper::jsonResponse(true, 'Success', $data, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function headOfFamily()
    {
        try {
            $userId = auth()->id();
            $data = $this->dashboardRepository->getHeadOfFamilyDashboard($userId);

            return ResponseHelper::jsonResponse(true, 'Dashboard head of family fetched successfully.', $data, 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 404);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
