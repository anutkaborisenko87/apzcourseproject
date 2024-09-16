<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PositionsRequests\PositionsRequest;
use App\Interfaces\ServicesInterfaces\PositionsServiceInterface;
use Illuminate\Http\Response;

class PositionsController extends Controller
{
    private PositionsServiceInterface $positionsService;

    public function __construct(PositionsServiceInterface $positionsService)
    {
        $this->positionsService = $positionsService;
    }

    final public function index(): Response
    {
        return response($this->positionsService->getPositionsList());
    }

    final public function show(int $position): Response
    {
        return response($this->positionsService->getPosition($position));
    }

    final public function store(PositionsRequest $request): Response
    {
        return response($this->positionsService->createPosition($request->validated()));
    }

    final public function update(PositionsRequest $request, int $position): Response
    {
        return response($this->positionsService->updatePosition($position, $request->validated()));
    }

    final public function destroy(int $position): Response
    {
        return response($this->positionsService->deletePosition($position));
    }
}
