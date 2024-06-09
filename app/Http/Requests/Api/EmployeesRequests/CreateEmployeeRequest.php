<?php

namespace App\Http\Requests\Api\EmployeesRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
            'user.last_name' => 'required|string|min:2|max:255',
            'user.first_name' => 'required|string|min:2|max:255',
            'user.patronymic_name' => 'sometimes|string|min:2|max:255',
            'user.email' => 'sometimes|email:rfc,dns|unique:users,email',
            'user.city' => 'sometimes|string|min:2|max:255',
            'user.street' => 'sometimes|string|min:2|max:255',
            'user.house_number' => 'sometimes|string|min:2|max:255',
            'user.apartment_number' => 'sometimes|string|min:2|max:255',
            'user.birth_date' => 'sometimes|date',
            'employee.position_id' => 'required|exists:positions,id',
            'employee.phone' => 'sometimes|string|regex:/^\+?[\d-]+$/|min:7|max:15',
            'employee.contract_number' => 'sometimes|string|min:2|max:15',
            'employee.passport_data' => 'sometimes|string|min:2|max:255',
            'employee.bank_account' => 'sometimes|string|min:2|max:255',
            'employee.bank_title' => 'sometimes|string|min:2|max:255',
            'employee.EDRPOU_bank_code' => 'sometimes|numeric',
            'employee.code_IBAN' => 'sometimes|string|min:2|max:255',
            'employee.medical_card_number' => 'sometimes|string|min:2|max:255',
            'employee.employment_date' => 'sometimes|date|before:now',
        ];
    }
}
