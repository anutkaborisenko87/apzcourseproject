<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class GroupsControllerException extends AbstractClassException
{
    public static function getGroupsListError(int $code): self
    {
        return new self("Помилка отримання даних", $code);
    }
    public static function createGroupError(int $code): self
    {
        return new self("Помилка створення групи", $code);
    }
    public static function updateGroupError(int $code): self
    {
        return new self("Помилка оновлення групи", $code);
    }
    public static function getGroupByIdError(int $id): self
    {
        return new self("Групу з ID $id не знайдено", Response::HTTP_NOT_FOUND);
    }
    public static function deleteGroupError(int $id): self
    {
        return new self("Помилка видалення групи з ID $id", Response::HTTP_BAD_REQUEST);
    }
}
