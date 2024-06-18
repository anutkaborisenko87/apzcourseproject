<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParrentsRequests\CreateParrentRequest;
use App\Http\Requests\Api\ParrentsRequests\UpdateParrentRequest;
use App\Services\ParrentsService;
use Exception;
use Illuminate\Http\Response;

class ParrentsController extends Controller
{
    private $parrentsService;

    public function __construct(ParrentsService $parrentsService)
    {
        $this->parrentsService = $parrentsService;
    }

    final public function indexActive(): Response
    {
        try {
            return response($this->parrentsService->getActiveParrentsList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function indexForSelect(int $childId = null): Response
    {
        try {
            if (!is_null($childId)) return response($this->parrentsService->getParrentsListForUpdateSelect($childId));
            return response($this->parrentsService->getParrentsListForSelect());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function indexNotActive(): Response
    {
        try {
            return response($this->parrentsService->getNotActiveParrentsList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function show(int $parrent): Response
    {
        try {
            return response($this->parrentsService->getParrentInfo($parrent));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function reactivate(int $parrent): Response
    {
        try {
            return response($this->parrentsService->reactivateParrent($parrent));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function deactivate(int $parrent): Response
    {
        try {
            return response($this->parrentsService->deactivateParrent($parrent));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function store(CreateParrentRequest $request): Response
    {
        try {
            return response($this->parrentsService->createNewParrent($request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function update(UpdateParrentRequest $request, int $parrent): Response
    {
        try {
            return response($this->parrentsService->updateParrentInfo($parrent, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }

    final public function destroy(int $parrent): Response
    {
        try {
            return response($this->parrentsService->deleteParrentInfo($parrent));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }



}
