<?php

namespace App\Services;

use App\Interfaces\RepsitotiesInterfaces\DashboardRepositoryInterface;
use App\Interfaces\ServicesInterfaces\DashboardServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardService implements DashboardServiceInterface
{
    private DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(Request $request): array
    {
        $respData = $this->dashboardRepository->getDashboardData();
        $groupsStat = [];
        $today = date('Y-m-d');
        if (isset($respData['groups'])) {
            $respData['groups']->each(function ($item, $index) use (&$groupsStat, $today) {
                $groupsStat[$index]['group_id'] = $item->id;
                $groupsStat[$index]['group_title'] = $item->title;
                if (!empty($item->teachers)) {
                    $educational_events = collect();
                    $item->teachers->each(function ($teacher, $indexTeacher) use (&$groupsStat, &$educational_events, $index) {
                        $educational_events = $educational_events->merge($teacher->educational_events);
                        $groupsStat[$index]['date_start'] = isset($groupsStat[$index]['date_start']) ? min($groupsStat[$index]['date_start'], $teacher->pivot->date_start) : $teacher->pivot->date_start;
                        $groupsStat[$index]['date_finish'] = isset($groupsStat[$index]['date_finish']) ? min($groupsStat[$index]['date_finish'], $teacher->pivot->date_finish) : $teacher->pivot->date_finish;
                        $groupsStat[$index]['teachers'][$indexTeacher] = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                    });

                    $groupsStat[$index] = array_merge($groupsStat[$index], $this->getEducEvStat($educational_events, $today));
                }
                $groupsStat[$index]['children_count'] = $item->children_count;
            });
            $respData['groups'] = $groupsStat;

        }
        if (isset($respData['teachers'])) {
            $teachersStat = [];
            $respData['teachers']->each(function ($item, $index) use (&$teachersStat, $today) {
                $teachersStat[$index]['teacher_id'] = $item->id;
                $teachersStat[$index]['teacher_full_name'] = $item->user->last_name . " " . $item->user->first_name . " " . $item->user->patronymic_name;
                $teachersStat[$index]['group'] = $item->groups[0]->title;
                $teachersStat[$index] = array_merge($teachersStat[$index], $this->getEducEvStat($item->educational_events, $today));
            });
            $respData['teachers'] = $teachersStat;
        }
        if (isset($respData['childrens'])) {
            $childrenStat = [];
            $respData['childrens']->each(function ($item, $index) use (&$childrenStat, $today) {
                $childrenStat[$index]['child_id'] = $item->id;
                $childrenStat[$index]['child_full_name'] = $item->user->last_name . " " . $item->user->first_name . " " . $item->user->patronymic_name;
                $childrenStat[$index]['group'] = $item->group->title;
                $childrenStat[$index]['birth_year'] = $item->user->birth_year;
                $childrenStat[$index]['age'] =  Carbon::parse($item->user->birth_date)->age;
                $childrenStat[$index]['visited_educational_events'] = $item->visited_educational_events->count();
                $childrenStat[$index]['avg_estimation_mark'] = $item->visited_educational_events->flatMap(function ($event) {
                    return $event->children_visitors->pluck('pivot.estimation_mark')->filter();
                })->avg();
                $childrenStat[$index]['avg_estimation_mark'] = round($childrenStat[$index]['avg_estimation_mark'], 2);
            });
            $respData['childrens'] = $childrenStat;
        }

        return $respData;
    }

    private function getEducEvStat($educational_events, $today)
    {
        $past_events = $educational_events->filter(function ($event) use ($today) {
            return $event->event_date < $today;
        });
        $past_events_count = $past_events->count();
        $total_events_count = $educational_events->count();
        $past_events_percentage = ($total_events_count > 0)
            ? ($past_events_count / $total_events_count) * 100
            : 0;
        $average_estimation_mark = $past_events->flatMap(function ($event) {
            return $event->children_visitors->pluck('pivot.estimation_mark')->filter();
        })->avg();
        return ['average_estimation_mark' => round($average_estimation_mark, 2),
            'past_events_percentage' => round($past_events_percentage, 2)];
    }
}
