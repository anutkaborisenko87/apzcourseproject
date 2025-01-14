<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DashboardControllerException extends AbstractClassException
{
    public static function getDashboardDataError(): self
    {
        return new self("Помилка отримання даних", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }
    public static function getDashboardReportDataError(): self
    {
        return new self("Помилка формування звіту", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

}
