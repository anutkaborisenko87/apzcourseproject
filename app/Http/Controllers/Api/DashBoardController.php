<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ServicesInterfaces\DashboardServiceInterface;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    private DashboardServiceInterface $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function indexAllData(Request $request)
    {
        return response($this->dashboardService->getDashboardData($request));
    }
}
