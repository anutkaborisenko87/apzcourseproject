<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\Parrent;
use App\Models\Position;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ParrentNotActive
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
        $parrent = Parrent::find($request->route('parrent'));
        if (!$parrent) {
            return response(['error' => 'Батька не знайдено'], 404);
        }
        if ($parrent->user->active) {
            return response(['error' => 'Батька вже активовано'], 401);
        }
        return $next($request);
    }
}
