<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class EmployeesControllerException extends AbstractClassException
{
    public static function getEmployeesListError(int $code): self
    {
        return new self("Помилка отримання даних про співробітників", $code);
    }
    public static function getEmployeeByIdError(int $id): self
    {
        return new self("Співробітника з ID $id не знайдено в базі", Response::HTTP_NOT_FOUND);
    }
    public static function createEmployeeError(int $code): self
    {
        return new self("Помилка створення данних про співробітника", $code);
    }
    public static function updateEmployeeError(int $code): self
    {
        return new self("Помилка оновлення даних співробітника", $code);
    }
    public static function deleteEmployeeError(int $code): self
    {
        return new self("Помилка видалення даних співробітника", $code);
    }
    public static function deactivatedEmployeeError(): self
    {
        return new self("Співробітника вже деактивовано", Response::HTTP_UNAUTHORIZED);
    }
    public static function activatedEmployeeError(): self
    {
        return new self("Співробітника вже активовано", Response::HTTP_UNAUTHORIZED);
    }
    public static function hireNotEnrolledEmployeeError(): self
    {
        return new self("Неможливо звідьнити не найнятого працівника", Response::HTTP_UNAUTHORIZED);
    }
    public static function hireEmployeeError(): self
    {
        return new self("Працівник вже звільнений раніше", Response::HTTP_UNAUTHORIZED);
    }
}
