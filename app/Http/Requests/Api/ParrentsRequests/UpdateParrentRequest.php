<?php

namespace App\Http\Requests\Api\ParrentsRequests;

use App\Models\Parrent;
use Illuminate\Foundation\Http\FormRequest;

class UpdateParrentRequest extends FormRequest
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
        $parent = Parrent::find((int) $this->route('parrent'));
        return [
            'user.last_name' => 'sometimes|string|min:2|max:255',
            'user.first_name' => 'sometimes|string|min:2|max:255',
            'user.patronymic_name' => 'sometimes|string|min:2|max:255',
            'user.email' => 'sometimes|email:rfc,dns|unique:users,email,' . $parent->user_id,
            'user.city' => 'sometimes|string|min:2|max:255',
            'user.street' => 'sometimes|string|min:2|max:255',
            'user.house_number' => 'sometimes|string|min:2|max:255',
            'user.apartment_number' => 'sometimes|string|min:2|max:255',
            'user.birth_date' => 'sometimes|date',
            'parrent.phone' => 'sometimes|string|regex:/^\+?[\d-]+$/|min:7|max:15',
            'parrent.work_place' => 'sometimes|string|min:2|max:15',
            'parrent.passport_data' => 'sometimes|string|min:2|max:255',
            'parrent.marital_status' => 'sometimes|string|min:2|max:255',
            'parrent.children.*.child_id' => 'sometimes|numeric|exists:childrens,id',
            'parrent.children.*.relations' => 'sometimes|string|min:2|max:255',
        ];
    }
}
