<?php

namespace App\Http\Requests\Api\PositionsRequests;

use Illuminate\Foundation\Http\FormRequest;

class PositionsRequest extends FormRequest
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
            'position_title' => 'required|string|min:3|max:25'
        ];
    }
}
