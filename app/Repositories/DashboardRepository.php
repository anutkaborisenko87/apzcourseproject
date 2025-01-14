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
            $educationPeriod = $from->format('Y') . '/' . $to->format('Y');
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
            return compact('groups', 'teachers', 'childrens', 'educationPeriod');

        } catch (Exception $e) {
            throw DashboardControllerException::getDashboardDataError();
        }
    }

    public function getDashboardGroupReportData(array $data): Group
    {
        try {
            $month = now()->month;
            $from = $data['from'] ?? ($month >= 9 ? Carbon::create(now()->year, 9, 1) : Carbon::create(now()->year - 1, 9, 1));
            $to = $data['to'] ?? ($month >= 9 ? Carbon::create(now()->year + 1, 9, 1) : Carbon::create(now()->year, 9, 1));
            return Group::where('id', $data['group_id'])->with(['children' => function ($query) use ($from, $to) {
                $query->whereDate('enrollment_date', '<=', $from)->where(function ($q) use ($from) {
                    $q->orWhereNull('graduation_date')
                        ->orWhereDate('graduation_date', '>=', $from);
                })->with('parrent_relations');
            },
                'teachers' => function ($query) use ($from, $to) {
                $query->wherePivot('date_start', '<=', $from)
                        ->wherePivot('date_finish', '>=', $to);
                }
            ])->first();
        } catch (Exception $exception) {
            throw DashboardControllerException::getDashboardDataError();
        }
    }

    public function getDashboardEducationalEventsReportData(array $data): Employee
    {
        try {
            $month = now()->month;
            $from = $data['from'] ?? ($month >= 9 ? Carbon::create(now()->year, 9, 1) : Carbon::create(now()->year - 1, 9, 1));
            $to = $data['to'] ?? date('Y-m-d');
            return Employee::where('id', $data['teacher_id'])->with(['educational_events' => function ($query) use ($from, $to) {
                $query->whereBetween('event_date', [$from, $to])->with('children_visitors')->orderBy('event_date', 'asc');
            }, 'user'])->first();
        } catch (Exception $exception) {
            throw DashboardControllerException::getDashboardDataError();
        }
    }

    public function getDashboardChildrenReportData(array $data): Group
    {
        try {
            $month = now()->month;
            $from = $data['from'] ?? ($month >= 9 ? Carbon::create(now()->year, 9, 1) : Carbon::create(now()->year - 1, 9, 1));
            $to = $data['to'] ?? date('Y-m-d');
            return Group::where('id', $data['group_id'])->with(['children' => function ($query) use ($from, $to) {
                $query->whereDate('enrollment_date', '<=', $from)->where(function ($q) use ($from) {
                    $q->orWhereNull('graduation_date')
                        ->orWhereDate('graduation_date', '>=', $from);
                })->with(['visited_educational_events' => function ($query) use ($from, $to) {
                    $query->whereBetween('event_date', [$from, $to]);
                }])->withCount(['visited_educational_events' => function ($query) use ($from, $to) {
                    $query->whereBetween('event_date', [$from, $to]);
                }]);
            },
                'teachers' => function ($query) use ($from, $to) {
                    $query->wherePivot('date_start', '<=', $from)
                        ->wherePivot('date_finish', '>=', $to);
                }
            ])->first();
        } catch (Exception $exception) {
            throw DashboardControllerException::getDashboardDataError();
        }
    }
}
