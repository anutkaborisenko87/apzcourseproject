<?php

namespace App\Interfaces\ServicesInterfaces;

interface IParrentsService
{
    public function getActiveParrentsList(): array;
    public function getNotActiveParrentsList(): array;
    public function getParrentsListForSelect(): array;
    public function getParrentInfo(int $id): array;
    public function deactivateParrent(int $id): array;
    public function reactivateParrent(int $id): array;
    public function createNewParrent(array $data): array;
    public function updateParrentInfo(int $id, array $data): array;
    public function deleteParrentInfo(int $id): array;
}
