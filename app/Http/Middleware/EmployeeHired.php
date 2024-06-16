<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EmployeeHired
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $employee = Employee::find($request->route('employee'));
        if (!$employee) {
            return response(['error' => 'Співробітника не знайдено'], 404);
        }
        if (is_null($employee->employment_date)) {
            return response(['error' => 'Неможливо звідьнити не найнятого працівника'], 401);
        }
        if (!is_null($employee->date_dismissal)) {
            return response(['error' => 'Працівник вже звільнений раніше'], 401);
        }
        return $next($request);
    }
}
