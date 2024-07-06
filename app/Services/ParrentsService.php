<?php

namespace App\Services;

use App\Http\Resources\ParrentForSelectResource;
use App\Http\Resources\ParrentResource;
use App\Interfaces\ServicesInterfaces\IParrentsService;
use App\Repositories\ParrentsRepository;
use App\Repositories\UserRepository;
use Exception;

class ParrentsService implements IParrentsService
{
    private $parrentsRepository;
    private $userRepository;

    public function __construct(ParrentsRepository $parrentsRepository, UserRepository $userRepository)
    {
        $this->parrentsRepository = $parrentsRepository;
        $this->userRepository = $userRepository;
    }

    final public function getActiveParrentsList(): array
    {
        $parrents = $this->parrentsRepository->getActiveParrents();
        $resp = $parrents->toArray();
        $resp['data'] = ParrentResource::collection($parrents->getCollection())->resolve();
        return $resp;
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

    final public function getNotActiveParrentsList(): array
    {
        $parrents = $this->parrentsRepository->getNotActiveParrents();
        $resp = $parrents->toArray();
        $resp['data'] = ParrentResource::collection($parrents->getCollection())->resolve();
        return $resp;
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
