<?php

namespace App\Http\Requests\Api\GroupsRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
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
            'title' => 'sometimes|string|min:3|max:25',
            'children' => 'sometimes|array',
            'children.*' => 'sometimes|numeric|exists:childrens,id',
            'teachers' => 'sometimes|array',
            'teachers.*.employee_id' => 'sometimes|numeric|exists:employees,id',
            'teachers.*.date_start' => 'sometimes|date',
            'teachers.*.date_finish' => 'sometimes|date'
        ];
    }
}
