<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ParrentControllerException extends AbstractClassException
{

    public static function parrentNotFoundError(int $id): self
    {
        return new self("Батька з $id не знайдено", Response::HTTP_NOT_FOUND);
    }

    public static function parrentAlreadyDeactivaredError(int $id): self
    {
        return new self("Батька з $id вже деактивовано", Response::HTTP_UNAUTHORIZED);
    }

    public static function parrentAlreadyActivaredError(int $id): self
    {
        return new self("Батька з $id вже активовано", Response::HTTP_UNAUTHORIZED);
    }

    public static function getActiveParrentsError(int $code): self
    {
        return new self("Плмилка отримання даних про активних батьків", $code);
    }

    public static function getNotActiveParrentsError(int $code): self
    {
        return new self("Плмилка отримання даних про неактивних батьків", $code);
    }

    public static function getActiveParrentsForSelectError(int $code): self
    {
        return new self("Плмилка отримання даних про активних батьків для селекту", $code);
    }

    public static function createParrentError(int $code): self
    {
        return new self("Плмилка запису даних про батька до бази даних", $code);
    }

    public static function updateParrentError(int $code): self
    {
        return new self("Плмилка оновлення даних про батька в базі даних", $code);
    }

    public static function deleteParrentError(int $id): self
    {
        return new self("Проблеми видалення даних з ID $id з бази даних", Response::HTTP_BAD_REQUEST);
    }
}
