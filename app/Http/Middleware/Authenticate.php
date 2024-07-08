<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AuthControllerException;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  $request
     * @param Closure $next
     * @param ...$guards
     * @return JsonResponse|null
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::guard('api')->guest()) {
            throw AuthControllerException::notAuthUserError();
        }

        return $next($request);
    }
}
