<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'contract_number' => $this->contract_number,
            'passport_data' => $this->passport_data,
            'position' => (new PositionResource($this->position))->resolve(),
            'medical_card_number' => $this->medical_card_number,
            'employment_date' => !is_null($this->employment_date) ? (new DateTime($this->employment_date))->format("Y-m-d") : null,
            'date_dismissal' => !is_null($this->date_dismissal) ? (new DateTime($this->date_dismissal))->format("Y-m-d") : null,
        ];
        return array_merge($userData, $data);
    }
}
