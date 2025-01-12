<?php

namespace App\Repositories;

use App\Exceptions\DashboardControllerException;
use App\Interfaces\RepsitotiesInterfaces\DashboardRepositoryInterface;
use App\Models\Children;
use App\Models\Employee;
use App\Models\Group;
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DashboardRepository implements DashboardRepositoryInterface
{

    /**
     * @throws DashboardControllerException
     */
    public function getDashboardData(): array
    {
        try {
            $today = now()->format('Y-m-d');
            $month = now()->month;
            $year = now()->year;
            if ($month >= 9) {
                $from = $data['from'] ?? Carbon::create($year, 9, 1);
                $to = $data['to'] ?? Carbon::create($year + 1, 9, 1);
            } else {
                $from = $data['from'] ?? Carbon::create($year - 1, 9, 1);
                $to = $data['to'] ?? Carbon::create($year, 9, 1);
            }
            $groups = Group::whereHas('teachers', function ($q) use ($today, $from, $to) {
                $q->where('date_start', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->where('date_finish', '>=', $today)
                            ->orWhereNull('date_finish');
                    });
            })->with(['teachers' => function ($q) use ($from, $to) {
                $q->with(['educational_events' => function ($query) use ($from, $to) {
                    $query->whereBetween('event_date', [$from, $to]);
                }]);
            }])->withCount('children')->get();

            $teachers = Employee::whereHas('groups', function ($q) use ($today) {
                $q->where('date_start', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->where('date_finish', '>=', $today)
                            ->orWhereNull('date_finish');
                    });
            })->with(['educational_events' => function ($query) use ($from, $to, $today) {
                $query->whereBetween('event_date', [$from, $to]);
            }, 'groups' => function ($q) use ($today) {
                $q->wherePivot('date_start', '<=', $today);
                $q->wherePivot('date_finish', '>=', $today);
            }])->get();
            $childrens = Children::where(function ($q) use ($today) {
                $q->whereNotNull('enrollment_date')->where('enrollment_date', '<=', $today);
                $q->whereNotNull('graduation_date')->where('graduation_date', '>=', $today);
            })->with(['visited_educational_events' => function ($query) use ($from, $today) {
                $query->whereBetween('event_date', [$from, $today]);
            }]);
            return compact('groups', 'teachers', 'childrens');

        } catch (Exception $e) {
            throw DashboardControllerException::getDashboardDataError(ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
