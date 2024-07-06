<?php

namespace App\Http\Middleware;

use App\Exceptions\PositionsControllerException;
use App\Models\Position;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PositionFound
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws PositionsControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $position = Position::find($request->route('position'));
        if (!$position) {
           throw PositionsControllerException::getPositionInfoError($request->route('position'));
        }
        return $next($request);
    }
}
