<?php

namespace App\Interfaces\ServicesInterfaces;

use Illuminate\Http\Request;

interface DashboardServiceInterface
{
    public function getDashboardData(Request $request): array;

    public function getDashboardGroupReportWord(array $data): string;
    public function getDashboardGroupReportExcel(array $data): string;
    public function getDashboardEducationalEventsReportWord(array $data): string;
    public function getDashboardEducationalEventsReportExcel(array $data): string;
    public function getDashboardChildrenReportWord(array $data): string;
    public function getDashboardChildrenReportExcel(array $data): string;

}
