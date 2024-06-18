<?php

namespace App\Http\Requests\Api\ChildrenRequests;

use App\Models\Children;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChildRequest extends FormRequest
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
        $child = Children::find((int) $this->route('child'));
        return [
            'user.last_name' => 'sometimes|string|min:2|max:255',
            'user.first_name' => 'sometimes|string|min:2|max:255',
            'user.patronymic_name' => 'sometimes|string|min:2|max:255',
            'user.email' => 'sometimes|email:rfc,dns|unique:users,email' . $child->user_id,
            'user.city' => 'sometimes|string|min:2|max:255',
            'user.street' => 'sometimes|string|min:2|max:255',
            'user.house_number' => 'sometimes|string|min:2|max:255',
            'user.apartment_number' => 'sometimes|string|min:2|max:255',
            'user.birth_date' => 'sometimes|date|before:now',
            'child.group_id' => 'sometimes|numeric|exists:groups,id',
            'child.mental_helth' => 'sometimes|string|min:2|max:255',
            'child.birth_certificate' => 'sometimes|string|min:2|max:255',
            'child.medical_card_number' => 'sometimes|string|min:2|max:255',
            'child.social_status' => 'sometimes|string|min:2|max:255',
            'child.enrollment_date' => 'sometimes|date',
            'child.graduation_date' => 'sometimes|date',
            'child.parrents.*.parrent_id' => 'sometimes|numeric|exists:parrents,id',
            'child.parrents.*.relations' => 'sometimes|string|min:2|max:255',
        ];
    }
}
