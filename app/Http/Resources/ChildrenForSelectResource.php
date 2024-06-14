<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildrenForSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    final public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'label' => ($this->user->last_name ?? '') . ' ' . ($this->user->first_name ?? '')
        ];
    }
}
