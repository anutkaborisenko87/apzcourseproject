<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildrenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    final public function toArray($request): array
    {
        $userData = (new UserResource($this->user))->resolve();
        $data = [
            'id' => $this->id,
            'mental_helth' => $this->mental_helth,
            'birth_certificate' => $this->birth_certificate,
            'medical_card_number' => $this->medical_card_number,
            'social_status' => $this->social_status,
            'enrollment_year' => $this->enrollment_year,
            'enrollment_date' => $this->enrollment_date,
            'graduation_year' => $this->graduation_year,
            'graduation_date' => $this->graduation_date,
        ];
        $data['group'] = !is_null($this->group_id) ? (new GroupResource($this->group))->resolve() : null;
        $data['parrents'] = [];
        if (!empty($this->parrent_relations->toArray())) {
            $this->parrent_relations->each(function ($item) use (&$data) {
                $data['parrents'][] = [
                    'parrent_id' => $item->id,
                    'parrent_name' => ($item->user->last_name ?? '' ). ' ' . ($item->user->first_name ?? '' ). ' ' . ($item->user->patronymic_name ?? '' ),
                    'relations' => $item->pivot->relations
                ];
            });
        }
        if (isset ($this->founded)) {
            $data['founded'] = $this->founded;
        }

        return array_merge($userData, $data);
    }
}
