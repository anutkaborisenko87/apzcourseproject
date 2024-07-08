<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Laravel\Passport\Token;
use App\Exceptions\AuthControllerException;

class AuthController extends Controller
{
    final public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            throw AuthControllerException::wrongCredentialsError();
        }
        $user = auth()->user();
        if (!$user->active) {
            $user->tokens()->delete();
            throw AuthControllerException::deactivatedAuthUserError();
        }
        if (!$user->roles()->first()) {
            $user->tokens()->delete();
            throw AuthControllerException::hasNotRightsAuthUserError();
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
            throw AuthControllerException::deactivatedAuthUserError();
        }
        if (!$user->roles()->first()) {
            $user->tokens()->delete();
            throw AuthControllerException::hasNotRightsAuthUserError();
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
