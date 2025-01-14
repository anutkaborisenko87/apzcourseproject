<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DashboardRequests\DashboardChildrenReportRequest;
use App\Http\Requests\Api\DashboardRequests\DashboardEducationalEventsReportRequest;
use App\Http\Requests\Api\DashboardRequests\DashboardGroupReportRequest;
use App\Interfaces\ServicesInterfaces\DashboardServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashBoardController extends Controller
{
    private DashboardServiceInterface $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function indexAllData(Request $request): Response
    {
        return response($this->dashboardService->getDashboardData($request));
    }

    public function getGroupReportWord(DashboardGroupReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardGroupReportWord($request->validated());
        $relativePath = "reports/wordreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }

    public function getGroupReportExcel(DashboardGroupReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardGroupReportExcel($request->validated());
        $relativePath = "reports/xlsreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }

    public function getEducationalEventsReportWord(DashboardEducationalEventsReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardEducationalEventsReportWord($request->validated());
        $relativePath = "reports/wordreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }

    public function getEducationalEventsReportExcel(DashboardEducationalEventsReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardEducationalEventsReportExcel($request->validated());
        $relativePath = "reports/xlsreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }

    public function getChildrenReportWord(DashboardChildrenReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardChildrenReportWord($request->validated());
        $relativePath = "reports/wordreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }

    public function getChildrenReportExcel(DashboardChildrenReportRequest $request): StreamedResponse
    {
        $filePath = $this->dashboardService->getDashboardChildrenReportExcel($request->validated());
        $relativePath = "reports/xlsreports/" . basename($filePath);
        return Storage::disk('public')->download($relativePath);
    }
}
