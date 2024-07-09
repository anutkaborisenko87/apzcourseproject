<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UsersControllerException extends AbstractClassException
{
    public static function getUserByIdError(int $id): self
    {
        return new self("Користувача з ID $id не знайдено", Response::HTTP_NOT_FOUND);
    }
    public static function createUserError(int $code): self
    {
        return new self("Проблеми запису даних про користувача до бази даних", $code);
    }
    public static function updateUserError(int $code): self
    {
        return new self("Проблеми оновлення даних про користувача в базі даних", $code);
    }
    public static function deleteUserError(int $code): self
    {
        return new self("Проблеми видалення даних про користувача в базі даних", $code);
    }
    public static function deactivateUserError(): self
    {
        return new self("Користувача вже деактивовано", Response::HTTP_UNAUTHORIZED);
    }
    public static function activateUserError(): self
    {
        return new self("Користувача вже активовано", Response::HTTP_UNAUTHORIZED);
    }
}
