<?php

namespace App\Http\Middleware;

use App\Exceptions\UsersControllerException;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserActive
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws UsersControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::find($request->route('user'));
        if (!$user) {
            throw UsersControllerException::getUserByIdError($request->route('user'));
        }
        if (!$user->active) {
            throw UsersControllerException::deactivateUserError();
        }
        return $next($request);
    }
}
