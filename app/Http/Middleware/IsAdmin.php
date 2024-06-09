<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (Response|RedirectResponse)  $next
     * @return JsonResponse|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->guest()) {
            return response()->json(['massage' => "Ви не авторизовані"], 401);
        }
        if (!Auth::guard('api')->user()->hasRole('super_admin')) {
                return response()->json(['massage' => "У вас немає прав доступу"], 401);
            }

        return $next($request);
    }
}
