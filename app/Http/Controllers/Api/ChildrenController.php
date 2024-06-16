<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChildrenService;
use Exception;
use Illuminate\Http\Response;

class ChildrenController extends Controller
{
    private $childrenService;

    public function __construct(ChildrenService $childrenService)
    {
        $this->childrenService = $childrenService;
    }

    final public function indexForSelect(): Response
    {
        try {
            return response($this->childrenService->childrenForSelectList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexForUpdateSelect(int $parrentId): Response
    {
        try {
            return response($this->childrenService->childrenForUpdateSelectList($parrentId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
