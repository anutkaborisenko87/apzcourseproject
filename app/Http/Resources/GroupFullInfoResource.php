<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupFullInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    final public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'children' => $this->children_count ?? $this->whenLoaded('children', $this->children),
            'teachers' => $this->teachers_count ?? $this->whenLoaded('teachers', $this->teachers),
        ];
       if (isset($data['children']) && !is_numeric($data['children'])) {
            $children = [];
            $this->children->each(function ($item) use (&$children) {
               $children[] = [
                   'child_id' => $item->id,
                   'child_name' => ($item->user->last_name ?? '') . ' ' . ($item->user->first_name ?? '') . ' ' . ($item->user->patronymic_name ?? ''),
                   'enrolment_period' => [
                       'from' => $item->enrollment_date,
                       'to' => $item->graduation_date
                   ]
               ];
            });
            $data['children'] = $children;
        }
       if (isset($data['teachers']) && !is_numeric($data['teachers'])) {
            $teachers = [];
            $this->teachers->each(function ($item) use (&$teachers) {
               $teachers[] = [
                   'teacher_id' => $item->id,
                   'teacher_name' => ($item->user->last_name ?? '') . ' ' . ($item->user->first_name ?? '') . ' ' . ($item->user->patronymic_name ?? ''),
                   'teaching_period' => [
                       'from' => $item->pivot->date_start,
                       'to' => $item->pivot->date_finish
                   ]
               ];
            });
            $data['teachers'] = $teachers;
        }
        $firstTeacher = $this->teachers->first();
        if ($firstTeacher && $firstTeacher->pivot) {
            $data['date_start'] = $firstTeacher->pivot->date_start;
            $data['date_finish'] = $firstTeacher->pivot->date_finish;
        }
        if ($this->teachers->count() > 0) {
            $now = now();
            $educational_events = collect();
            foreach ($this->teachers as $teacher) {
                $educational_events = $educational_events->merge($teacher->educational_events);
            }
            $past_events = $educational_events->filter(function ($event) use ($now) {
                return $event->event_date < $now;
            });
            $past_events_count = $past_events->count();
            $total_events_count = $educational_events->count();
            $past_events_percentage = ($total_events_count > 0)
                ? ($past_events_count / $total_events_count) * 100
                : 0;
            $average_estimation_mark = $past_events->flatMap(function ($event) {
                return $event->children_visitors->pluck('pivot.estimation_mark')->filter();
            })->avg();
            $data['average_estimation_mark'] = round($average_estimation_mark, 2);
            $data['past_events_percentage'] = $past_events_percentage;
        }

        return $data;
    }
}
