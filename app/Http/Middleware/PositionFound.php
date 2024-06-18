<?php

namespace App\Http\Middleware;
;
use App\Models\Position;
use Closure;
use Illuminate\Http\Request;

class PositionFound
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $position = Position::find($request->route('position'));
        if (!$position) {
               return response(['error' => 'Позицію не знайдено'], 404);
            }
        return $next($request);
    }
}
