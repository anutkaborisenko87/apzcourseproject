<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    final public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = Auth::attempt($credentials);

    }
}
