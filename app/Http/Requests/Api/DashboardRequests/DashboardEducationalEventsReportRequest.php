<?php

namespace App\Http\Requests\Api\DashboardRequests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardEducationalEventsReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:employees,id',
            'from' => 'required|date',
            'to' => 'sometimes|date'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
