<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParrentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    final public function toArray($request): array
    {
        $userData = (new UserResource($this->user))->resolve();

        $data = [
            'id' => $this->id,
            'phone' => $this->phone,
            'work_place' => $this->work_place,
            'passport_data' => $this->passport_data,
            'marital_status' => $this->marital_status,
        ];
        if (!empty($this->children_relations->toArray())) {
            $this->children_relations->each(function ($child) use (&$data) {
                $data['children'][] = [
                    'child_id' => $child->id,
                    'child_name' => ($child->user->last_name ?? '' ). ' ' . ($child->user->first_name ?? '' ). ' ' . ($child->user->patronymic_name ?? '' ),
                    'relations' => $child->pivot->relations
                ];
            });

        }
        return array_merge($userData, $data);
    }
}
