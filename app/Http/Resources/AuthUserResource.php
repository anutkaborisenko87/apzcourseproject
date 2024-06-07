<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    final public function toArray($request):array
    {
        return [
            'id' => $this->id,
            'name' => $this->last_name . ' ' . $this->first_name,
            'email' => $this->email,
            'role' => $this->roles()->first() ? $this->roles()->first()->name : $this->roles()->first()
        ];
    }
}
