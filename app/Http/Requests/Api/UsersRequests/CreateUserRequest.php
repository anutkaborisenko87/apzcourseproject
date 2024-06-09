<?php

namespace App\Http\Requests\Api\UsersRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'last_name' => 'required|string|min:2|max:255',
            'first_name' => 'required|string|min:2|max:255',
            'patronymic_name' => 'sometimes|string|min:2|max:255',
            'role' => 'sometimes|numeric|exists:roles,id',
            'email' => 'sometimes|email:rfc,dns|unique:users,email',
            'city' => 'sometimes|string|min:2|max:255',
            'street' => 'sometimes|string|min:2|max:255',
            'house_number' => 'sometimes|string|min:2|max:255',
            'apartment_number' => 'sometimes|string|min:2|max:255',
            'birth_date' => 'sometimes|date',
        ];
    }
}
