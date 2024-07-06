<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

abstract class AbstractClassException extends Exception
{
    public function render($request): Response
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }
}
