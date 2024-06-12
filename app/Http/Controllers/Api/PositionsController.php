<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PositionsRequests\PositionsRequest;
use App\Services\PositionsService;
use Exception;
use Illuminate\Http\Response;

class PositionsController extends Controller
{
    private $positionsService;

    public function __construct(PositionsService $positionsService)
    {
        $this->positionsService = $positionsService;
    }

    final public function index(): Response
    {
        try {
            return response($this->positionsService->getPositionsList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function show(int $position): Response
    {
        try {
            return response($this->positionsService->getPosition($position));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function store(PositionsRequest $request): Response
    {
        try {
            return response($this->positionsService->createPosition($request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function update(PositionsRequest $request, int $position): Response
    {
        try {
            return response($this->positionsService->updatePosition($position, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function destroy(int $position): Response
    {
        try {
            return response($this->positionsService->deletePosition($position));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
