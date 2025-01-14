<?php

namespace App\Http\Requests\Api\DashboardRequests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardGroupReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:groups,id',
            'from' => 'required|date',
            'to' => 'required|date'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
