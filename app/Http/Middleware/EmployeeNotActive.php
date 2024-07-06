<?php

namespace App\Http\Middleware;

use App\Exceptions\EmployeesControllerException;
use App\Models\Employee;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EmployeeNotActive
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
            throw EmployeesControllerException::getEmployeeByIdError($request->route('employee'));
        }
        if ($employee->user->active) {
            throw EmployeesControllerException::activatedEmployeeError();
        }
        return $next($request);
    }
}
