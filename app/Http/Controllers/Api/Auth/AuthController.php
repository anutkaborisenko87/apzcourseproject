<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\AuthUserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    final public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response(['error' => 'Invalid credentials'], 401);
        }
        $user = auth()->user();
        if (!$user->active) {
            $user->tokens()->delete();
            return response(['error' => 'User deactivated'], 401);
        }
        if (!$user->roles()->first()) {
            $user->tokens()->delete();
            return response(['error' => 'You do not have access rights'], 401);
        }
        $newToken = $user->createToken('Personal Access Token');
        $resUser = (new AuthUserResource($user))->resolve();
        return response(['user' => $resUser, 'access_token' => $newToken->accessToken]);

    }
}
