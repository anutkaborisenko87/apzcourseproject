<?php

namespace App\Http\Requests\Api\GroupsRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:25',
            'children' => 'sometimes|array|max:20',
            'children.*' => 'sometimes|numeric|exists:childrens,id',
            'teachers' => 'sometimes|array|max:2',
            'teachers.*.employee_id' => 'sometimes|numeric|exists:employees,id',
            'date_start' => [
                'required_if:children,!null|required_if:teachers,!null|required_if:date_finish,!null',
                'date',
            ],
            'date_finish' => 'required_if:date_start,!null|date',
        ];
    }
}
