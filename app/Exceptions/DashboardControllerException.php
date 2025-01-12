<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class DashboardControllerException extends AbstractClassException
{
    public static function getDashboardDataError(int $code): self
    {
        return new self("Помилка отримання даних", $code);
    }

}
