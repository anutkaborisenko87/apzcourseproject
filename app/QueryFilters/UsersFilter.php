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

    abstract function applyFilter($builder, $request);

    protected function filterName()
    {
        return Str::snake(class_basename($this));
    }


}
