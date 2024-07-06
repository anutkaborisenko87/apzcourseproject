<?php

namespace App\Http\Middleware;

use App\Exceptions\EmployeesControllerException;
use App\Models\Employee;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeHired
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws EmployeesControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $employee = Employee::find($request->route('employee'));
        if (!$employee) {
            throw EmployeesControllerException::getEmployeeByIdError($request->route('employee'));
        }
        if (is_null($employee->employment_date)) {
            throw EmployeesControllerException::hireNotEnrolledEmployeeError();
        }
        if (!is_null($employee->date_dismissal)) {
            throw EmployeesControllerException::hireEmployeeError();
        }
        return $next($request);
    }
}
