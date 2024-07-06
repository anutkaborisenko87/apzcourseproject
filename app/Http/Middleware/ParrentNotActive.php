<?php

namespace App\Http\Middleware;

use App\Exceptions\ParrentControllerException;
use App\Models\Parrent;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParrentNotActive
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws ParrentControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $parrent = Parrent::find($request->route('parrent'));
        if (!$parrent) {
            throw ParrentControllerException::parrentNotFoundError($request->route('parrent'));
        }
        if ($parrent->user->active) {
            throw ParrentControllerException::parrentAlreadyActivaredError($request->route('parrent'));
        }
        return $next($request);
    }
}
