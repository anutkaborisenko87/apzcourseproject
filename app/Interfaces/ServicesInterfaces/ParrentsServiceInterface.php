<?php

namespace App\Interfaces\ServicesInterfaces;

use Illuminate\Http\Request;

interface ParrentsServiceInterface
{
    public function getActiveParrentsList(Request $request): array;
    public function getNotActiveParrentsList(Request $request): array;
    public function getParrentsListForSelect(): array;
    public function getParrentsListForUpdateSelect(int $childId): array;
    public function getParrentInfo(int $id): array;
    public function deactivateParrent(int $id): array;
    public function reactivateParrent(int $id): array;
    public function createNewParrent(array $data): array;
    public function updateParrentInfo(int $id, array $data): array;
    public function deleteParrentInfo(int $id): array;
}
