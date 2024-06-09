<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class UserActive
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
        $user = User::find($request->route('user'));
        if (!$user) {
            return response(['error' => 'User not found'], 404);
        }
        if (!$user->active) {
            return response(['error' => 'User already not active'], 401);
        }
        return $next($request);
    }
}
