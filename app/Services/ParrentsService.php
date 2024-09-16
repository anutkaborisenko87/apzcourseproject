<?php

namespace App\Services;

use App\Http\Resources\ParrentForSelectResource;
use App\Http\Resources\ParrentResource;
use App\Interfaces\RepsitotiesInterfaces\ParrentsRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Interfaces\ServicesInterfaces\ParrentsServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ParrentsService implements ParrentsServiceInterface
{
    private ParrentsRepositoryInterface $parrentsRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(ParrentsRepositoryInterface $parrentsRepository, UserRepositoryInterface $userRepository)
    {
        $this->parrentsRepository = $parrentsRepository;
        $this->userRepository = $userRepository;
    }

    final public function getActiveParrentsList(Request $request): array
    {
        $parrents = $this->parrentsRepository->getActiveParrents($request);
        return $this->formatRespData($parrents, $request);
    }

    private function formatRespData(LengthAwarePaginator $parrentsPaginated, Request $request): array
    {
        $parrentsResp = $parrentsPaginated->toArray();
        $parrentsResp['data'] = ParrentResource::collection($parrentsPaginated->getCollection())->resolve();
        $requestData = $request->except(['page', 'per_page']);
        return array_merge($parrentsResp, $requestData);
    }

    final public function getParrentsListForSelect(): array
    {
        $parrents = $this->parrentsRepository->getActiveParrentsForSelect();
        return ParrentForSelectResource::collection($parrents)->resolve();
    }

    final public function getParrentsListForUpdateSelect(int $childId): array
    {
        $parrents = $this->parrentsRepository->getActiveParrentsForUpdateSelect($childId);
        return ParrentForSelectResource::collection($parrents)->resolve();
    }

    final public function getNotActiveParrentsList(Request $request): array
    {
        $parrents = $this->parrentsRepository->getNotActiveParrents($request);
        return  $this->formatRespData($parrents, $request);
    }

    final public function getParrentInfo(int $id): array
    {
        return (new ParrentResource($this->parrentsRepository->getParrentById($id)))->resolve();
    }

    final public function deactivateParrent(int $id): array
    {
        $parrent = $this->parrentsRepository->getParrentById($id);
        $this->userRepository->deactivateUser($parrent->user);
        return (new ParrentResource($parrent))->resolve();
    }

    final public function reactivateParrent(int $id): array
    {
        $parrent = $this->parrentsRepository->getParrentById($id);
        $this->userRepository->reactivateUser($parrent->user);
        return (new ParrentResource($parrent))->resolve();
    }

    final public function createNewParrent(array $data): array
    {
        $userData = $data['user'];
        $parrentData = isset($data['parrent']) ? $data['parrent'] : [];
        $newUser = $this->userRepository->createUser($userData);
        $parrentData['user_id'] = $newUser->id;
        $newParrent = $this->parrentsRepository->createParrent($parrentData);
        if (!$newParrent) {
            $this->userRepository->deleteUser($newUser);
        }
        return (new ParrentResource($newParrent))->resolve();
    }

    final public function updateParrentInfo(int $id, array $data): array
    {
        $parrent = $this->parrentsRepository->getParrentById($id);
        if (isset($data['user'])) {
            $userData = $data['user'];
            $this->userRepository->updateUser($parrent->user, $userData);
        }
        $udatedParrent = $this->parrentsRepository->updateParrent($parrent, $data['parrent']);
        return (new ParrentResource($udatedParrent))->resolve();
    }

    final public function deleteParrentInfo(int $id): array
    {
        $parrent = $this->parrentsRepository->getParrentById($id);
        $parrentResp = (new ParrentResource($parrent))->resolve();
        $this->parrentsRepository->deleteParrent($parrent);
        return ['success' => true, 'parrent' => $parrentResp];
    }
}
