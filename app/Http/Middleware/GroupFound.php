<?php

namespace App\Http\Middleware;

use App\Exceptions\GroupsControllerException;
use App\Models\Group;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupFound
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws GroupsControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $child = Group::find($request->route('group'));
        if (!$child) {
            throw GroupsControllerException::getGroupByIdError($request->route('group'));
            }
        return $next($request);
    }
}
