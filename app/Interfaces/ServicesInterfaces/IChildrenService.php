<?php

namespace App\Interfaces\ServicesInterfaces;

interface IChildrenService
{
    public function childrenForSelectList(): array;
    public function childrenForUpdateSelectList(int $parrenId): array;
}
