<?php

namespace App\Services;

use App\Http\Resources\ParrentForSelectResource;
use App\Http\Resources\ParrentResource;
use App\Interfaces\RepsitotiesInterfaces\ParrentsRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Interfaces\ServicesInterfaces\ParrentsServiceInterface;
use App\Models\Parrent;
use App\Models\User;
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
        return $this->formatRespData($parrents, $request, true);
    }

    private function formatRespData(LengthAwarePaginator $parrentsPaginated, Request $request, bool $active): array
    {
        $parrentsResp = $parrentsPaginated->toArray();
        $parrentsResp['data'] = ParrentResource::collection($parrentsPaginated->getCollection())->resolve();
        $requestData = $request->except(['page', 'per_page']);
        $requestData['filters'][] = [
            'id' => 'sex',
            'name' => 'Гендерна приналежність',
            'options' => [
                [
                    'value' => 'male',
                    'label' => 'чоловік',
                    'checked' => $request->has('filter_parrents_by')
                        && in_array('sex', array_keys($request->input('filter_parrents_by')))
                        && in_array('male', array_values($request->input('filter_parrents_by')['sex']))
                ],
                [
                    'value' => 'female',
                    'label' => 'жінка',
                    'checked' => $request->has('filter_parrents_by')
                        && in_array('sex', array_keys($request->input('filter_parrents_by')))
                        && in_array('female', array_values($request->input('filter_parrents_by')['sex']))
                ]
            ]
        ];
        $citiesList = User::where('active', $active)->whereHas('parrent')->citiesList()->toArray();
        $citiesList = array_map(function ($city) use ($request) {
            $cityString = $city ?? 'null';
            return [
                'value' => $cityString,
                'label' => $city ?? 'Невідомо',
                'checked' => $request->has('filter_parrents_by')
                    && in_array('city', array_keys($request->input('filter_parrents_by')))
                    && in_array($cityString, $request->input('filter_parrents_by')['city'])
            ];
        }, $citiesList);
        $requestData['filters'][] = [
            'id' => 'city',
            'name' => 'Місто',
            'options' => $citiesList
        ];
        $maritalStatusesList = Parrent::whereHas('user', function ($query) use ($active) {
            $query->where('active', $active);
        })->maritalStatuses()->toArray();
        $maritalStatusesList = array_map(function ($maritalStatus) use ($request) {
            $maritalStatusString = $maritalStatus ?? 'without_status';
            return [
                'value' => $maritalStatusString,
                'label' => $maritalStatus ?? 'Без стаусу',
                'checked' => $request->has('filter_parrents_by')
                    && in_array('marital_status', array_keys($request->input('filter_parrents_by')))
                    && in_array($maritalStatusString, $request->input('filter_parrents_by')['marital_status'])
            ];
        }, $maritalStatusesList);
        $requestData['filters'][] = [
            'id' => 'marital_status',
            'name' => 'Соціальний статус',
            'options' => $maritalStatusesList
        ];


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
        return  $this->formatRespData($parrents, $request, false);
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
