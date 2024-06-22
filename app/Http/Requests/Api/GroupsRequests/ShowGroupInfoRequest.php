<?php

namespace App\Http\Requests\Api\GroupsRequests;

use Illuminate\Foundation\Http\FormRequest;

class ShowGroupInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    final public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    final public function rules(): array
    {
        return [
            'from' => 'sometimes|date',
            'to' => 'sometimes|date',
        ];
    }
}
