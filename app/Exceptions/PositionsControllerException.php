<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class PositionsControllerException extends AbstractClassException
{
    public static function getPositionsError(int $code): self
    {
       return new self("Проблеми отримання даних з бази даних", $code);
    }
    public static function createPositionsError(int $code): self
    {
       return new self("Помилка створення позиції", $code);
    }
    public static function getPositionInfoError(int $id): self
    {
       return new self("Позиція з ID $id не знайдена", Response::HTTP_NOT_FOUND);
    }
    public static function deletePositionInfoError(int $id): self
    {
       return new self("Помилка видалення позиції з ID $id", Response::HTTP_BAD_REQUEST);
    }
    public static function updatePositionInfoError(int $code): self
    {
       return new self("Помилка оновлення позиції", $code);
    }
}
