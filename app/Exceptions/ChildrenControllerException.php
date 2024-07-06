<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
class ChildrenControllerException extends AbstractClassException
{
    final public static function childrenListError(): self
    {
        return new self("Проблеми отримання даних з бази даних", Response::HTTP_BAD_REQUEST);
    }
    final public static function childrenNotFoundError(int $id): self
    {
        return new self("Дитини з ID $id не знайдено в базі", Response::HTTP_NOT_FOUND);
    }
    final public static function childrenNotCreatedError(): self
    {
        return new self("Проблеми запису даних про дитину до бази даних", Response::HTTP_BAD_REQUEST);
    }
    final public static function childrenNotUpdatedError(): self
    {
        return new self("Проблеми оновлення даних про дитину в базі даних", Response::HTTP_BAD_REQUEST);
    }
    final public static function childrenNotDeletedError(int $id): self
    {
        return new self("Проблеми видалення даних про дитину з ID $id з бази даних", Response::HTTP_BAD_REQUEST);
    }
}
