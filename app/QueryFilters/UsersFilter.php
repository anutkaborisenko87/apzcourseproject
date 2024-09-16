<?php

namespace App\QueryFilters;

use Closure;
use Illuminate\Support\Str;

abstract class UsersFilter
{
    public function handle($users, Closure $next)
    {
        $request = request();
        if (!$request->has($this->filterName())) {
            return $next($users);
        }

        return $next($this->applyFilter($users, $request));

    }

    abstract public function applyFilter($builder, $request);

    protected function filterName(): string
    {
        return Str::snake(class_basename($this));
    }


}
