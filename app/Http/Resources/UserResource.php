<?php

namespace App\Http\Resources;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    final public function toArray($request): array
    {
        $userId = '';
        if (isset($this['id'])) {
            $userId = $this['id'];
        }
        if (isset($this->id)) {
            $userId = $this->id;
        }
        $birthDate = null;
        if (isset($this['birth_date'])) {
            $birthDate = $this['birth_date'];
        }
        if (isset($this->birth_date)) {
            $birthDate = $this['birth_date'];
        }
        $data = [
            'user_id' => $userId,
            'last_name' => $this->last_name ?? $this['last_name'],
            'first_name' => $this->first_name ?? $this['first_name'],
            'patronymic_name' => $this->patronymic_name ?? $this['patronymic_name'],
            'email' => $this->email ?? $this['email'],
            'city' => $this->city ?? $this['city'],
            'street' => $this->street ?? $this['street'],
            'house_number' => $this->house_number ?? $this['house_number'],
            'apartment_number' => $this->apartment_number ?? $this['apartment_number'],
            'birthdate' => $birthDate ? (new DateTime($birthDate))->format('Y-m-d') : null,
        ];
        if ($this->userCategory()) {
            $data['user_category'] = $this->userCategory();
        }
        if ($this->roles()->first()) {
            $data['role'] = $this->roles()->first()->name;
        }
        return $data;
    }
}
