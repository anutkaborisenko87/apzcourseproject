<?php

namespace App\Services;

use App\Http\Resources\GroupFullInfoResource;
use App\Http\Resources\GroupResource;
use App\Interfaces\ServicesInterfaces\GroupServiceInterface;
use App\Interfaces\RepsitotiesInterfaces\GroupRepositoryInterface;
use Carbon\Carbon;

class GroupService implements GroupServiceInterface
{
    private GroupRepositoryInterface $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    final public function getGroupsListForSelect(): array
    {
        return GroupResource::collection($this->groupRepository->getGroupsForSelect())->resolve();
    }

    final public function getGroupsList(): array
    {
        return GroupFullInfoResource::collection($this->groupRepository->getGroupsList())->resolve();
    }

    final public function showGroupInfo(int $groupId, ?array $data = null): array
    {
        $month = now()->month;
        $year = now()->year;
        $group = $this->groupRepository->getGroupInfo($groupId, $data);
        if ($month >= 9) {
            $from = $data['from'] ?? Carbon::create($year, 9, 1);
            $to = $data['to'] ?? Carbon::create($year + 5, 9, 1);
        } else {
            $from = $data['from'] ?? Carbon::create($year - 1, 9, 1);
            $to = $data['to'] ?? Carbon::create($year + 4, 9, 1);
        }
        $educational_events_info = [];
        if (!empty($group->teachers)) {
            $educational_events = [];
            $group->teachers->each(function ($teacher) use ($from, $to, &$educational_events) {
                $teacher->load([
                    'educational_events' => function ($query) use ($from, $to) {
                        $query->where('event_date', '>=', $from)
                            ->where('event_date', '<=', $to);
                    }
                ]);
                $teacher->educational_events->each(function ($event) use (&$educational_events) {
                    $educational_events[] = $event;
                });
            });
            $now = now();
            $past_events = collect($educational_events)->filter(function ($event) use ($now) {
                return $event->event_date < $now;
            });
            $past_events_count = $past_events->count();
            $total_events_count = count($educational_events);
            $past_events_percentage = ($total_events_count > 0)
                ? ($past_events_count / $total_events_count) * 100
                : 0;
            $average_estimation_mark = $past_events->map(function ($event) {
                return $event->children_visitors->pluck('pivot.estimation_mark')->filter()->avg();
            })->filter()->avg();
            $average_estimation_mark = round($average_estimation_mark, 2);
            $educational_events_info = [
                'past_events_percentage' => $past_events_percentage,
                'average_estimation_mark' => $average_estimation_mark,
            ];
        }
        return array_merge((new GroupFullInfoResource($group))->resolve(), $educational_events_info);
    }

    final public function createNewGroup(array $data): array
    {
        return (new GroupFullInfoResource($this->groupRepository->addGroup($data)))->resolve();
    }

    final public function updateGroup(int $groupId, array $data): array
    {
        $group = $this->groupRepository->getGroupById($groupId);
        return (new GroupFullInfoResource($this->groupRepository->updateGroup($group, $data)))->resolve();
    }

    final public function deleteGroup(int $groupId): array
    {
        $group = $this->groupRepository->getGroupInfo($groupId);
        $groupResp = (new GroupFullInfoResource($group))->resolve();
        $this->groupRepository->deleteGroup($group);
        return $groupResp;
    }
}
