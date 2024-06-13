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
        try {
            $parrents = $this->parrentsRepository->getActiveParrents();
            $resp = $parrents->toArray();
            $resp['data'] = ParrentResource::collection($parrents->getCollection())->resolve();
            return $resp;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getParrentsListForSelect(): array
    {
        try {
            $parrents = $this->parrentsRepository->getActiveParrentsForSelect();
            return ParrentForSelectResource::collection($parrents)->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getNotActiveParrentsList(): array
    {
        try {
            $parrents = $this->parrentsRepository->getNotActiveParrents();
            $resp = $parrents->toArray();
            $resp['data'] = ParrentResource::collection($parrents->getCollection())->resolve();
            return $resp;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getParrentInfo(int $id): array
    {
        try {
            return (new ParrentResource($this->parrentsRepository->getParrentById($id)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deactivateParrent(int $id): array
    {
        try {
            $parrent = $this->parrentsRepository->getParrentById($id);
            $this->userRepository->deactivateUser($parrent->user);
            return (new ParrentResource($parrent))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function reactivateParrent(int $id): array
    {
        try {
            $parrent = $this->parrentsRepository->getParrentById($id);
            $this->userRepository->reactivateUser($parrent->user);
            return (new ParrentResource($parrent))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createNewParrent(array $data): array
    {
        try {
            $userData = $data['user'];
            $parrentData = $data['parrent'];
            $newUser = $this->userRepository->createUser($userData);
            if (!$newUser) throw new Exception('Помилка створення користувача');
            $parrentData['user_id'] = $newUser->id;
            $newParrent = $this->parrentsRepository->createParrent($parrentData);
            if (!$newParrent) {
                $this->userRepository->deleteUser($newUser);
                throw new Exception('Помилка створення батька');
            }
            return (new ParrentResource($newParrent))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updateParrentInfo(int $id, array $data): array
    {
        try {
            $parrent = $this->parrentsRepository->getParrentById($id);
            if (isset($data['user'])) {
                $userData = $data['user'];
                $this->userRepository->updateUser($parrent->user, $userData);
            }
            $udatedParrent = $this->parrentsRepository->updateParrent($parrent, $data['parrent']);
            return (new ParrentResource($udatedParrent))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deleteParrentInfo(int $id): array
    {
        try {
            $parrent = $this->parrentsRepository->getParrentById($id);
            $parrentResp = (new ParrentResource($parrent))->resolve();
            $this->parrentsRepository->deleteParrent($parrent);
            return ['success' => true, 'parrent' => $parrentResp];
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
