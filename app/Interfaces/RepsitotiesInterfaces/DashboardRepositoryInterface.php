<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Employee;
use App\Models\Group;

interface DashboardRepositoryInterface
{
    public function getDashboardData(): array;
    public function getDashboardGroupReportData(array $data): Group;
    public function getDashboardEducationalEventsReportData(array $data): Employee;
    public function getDashboardChildrenReportData(array $data): Group;

}
