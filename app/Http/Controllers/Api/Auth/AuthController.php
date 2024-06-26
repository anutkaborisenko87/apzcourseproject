<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    final public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response(['error' => 'Неввірний логін або пароль'], 401);
        }
        $user = auth()->user();
        if (!$user->active) {
            $user->tokens()->delete();
            return response(['error' => 'Цього користувача було деактивовано'], 401);
        }
        if (!$user->roles()->first()) {
            $user->tokens()->delete();
            return response(['error' => 'У вас немає прав доступу до системи'], 401);
        }
        $newToken = $user->createToken('Personal Access Token');
        $resUser = (new AuthUserResource($user))->resolve();
        return response(['user' => $resUser, 'access_token' => $newToken->accessToken]);

    }
    final public function loggedInUserInfo(): Response
    {
        $user = Auth::guard('api')->user();
        if (!$user->active) {
            $user->tokens()->delete();
            return response(['error' => 'Користувача деактивовано'], 401);
        }
        if (!$user->roles()->first()) {
            $user->tokens()->delete();
            return response(['error' => 'У вас немає прав доступу до системи'], 401);
        }
        $resUser = (new AuthUserResource($user))->resolve();
        return response(['user' => $resUser]);

    }

    final public function logout(Request $request): Response
    {
        $token = $request->bearerToken();
        if ($token) {
            $user = Auth::guard('api')->user();
            $user->tokens()->delete();
        }
        return response(['message' => 'Ви успішно розлогінені']);
    }
}
