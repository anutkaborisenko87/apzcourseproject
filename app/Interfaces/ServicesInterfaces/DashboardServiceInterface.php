<?php

namespace App\Interfaces\ServicesInterfaces;

use Illuminate\Http\Request;

interface DashboardServiceInterface
{
    public function getDashboardData(Request $request): array;

}
