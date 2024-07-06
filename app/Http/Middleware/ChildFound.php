<?php

namespace App\Http\Middleware;

use App\Exceptions\ChildrenControllerException;
use App\Models\Children;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChildFound
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws ChildrenControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $child = Children::find($request->route('child'));
        if (!$child) {
            throw ChildrenControllerException::childrenNotFoundError($request->route('child'));
        }
        return $next($request);
    }
}
