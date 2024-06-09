<?php

namespace App\Http\Requests\Api\UsersRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');
        return [
            'last_name' => 'sometimes|string|min:2|max:255',
            'first_name' => 'sometimes|string|min:2|max:255',
            'patronymic_name' => 'sometimes|string|nullable',
            'email' => 'sometimes|nullable|unique:users,email,' . $userId,
            'role' => 'sometimes|nullable|exists:roles,id',
            'city' => 'sometimes|nullable',
            'street' => 'sometimes|nullable',
            'house_number' => 'sometimes|nullable',
            'apartment_number' => 'sometimes|nullable',
            'birth_date' => 'sometimes|date|before:now',
        ];
    }
}
