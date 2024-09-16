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
            'educationalPrograms' => $this->educational_programs_count ?? $this->whenLoaded('educationalPrograms', $this->educationalPrograms),
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
        if (isset($data['educationalPrograms']) && !is_numeric($data['teachers'])) {
            $educationalPrograms = [];
            $this->educationalPrograms->each(function ($item) use (&$educationalPrograms) {
               $educationalPrograms[] = [
                   'program_id' => $item->id,
                   'program_age_restrictions' => $item->age_restrictions,
                   'program_approval_date' => $item->approval_date,
                   'program_author_name' => ($item->author->user->last_name ?? '') . ' ' . ($item->author->user->first_name ?? '') . ' ' . ($item->author->user->patronymic_name ?? ''),
                   'program_teaching_period' => [
                       'from' => $item->pivot->date_start,
                       'to' => $item->pivot->date_finish
                   ]
               ];
            });
            $data['educationalPrograms'] = $educationalPrograms;
        }
        return $data;
    }
}
