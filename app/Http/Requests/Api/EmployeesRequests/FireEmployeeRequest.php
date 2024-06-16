<?php

namespace App\Http\Requests\Api\EmployeesRequests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class FireEmployeeRequest extends FormRequest
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
            'date_dismissal' => 'sometimes|date|before:now',
        ];
    }
}
