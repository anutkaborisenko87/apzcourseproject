<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/logged_user', [AuthController::class, 'loggedInUserInfo']);

Route::get('/user/profile', [\App\Http\Controllers\Api\UsersController::class, 'indexProfile']);

Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\UsersController::class)
    ->prefix('/users')
    ->group(function () {
        Route::get('/active', 'indexActiveUsers');
        Route::get('/not_active', 'indexNotActiveUsers');
        Route::post('/create', 'store');
        Route::middleware(['user_exists'])
            ->prefix('/{user}')
            ->where(['user' => '[0-9]+'])
            ->group(function () {
                Route::get('/reactivate', 'reactivateUser')->middleware(['user_not_active']);
                Route::get('/deactivate', 'deactivateUser')->middleware(['user_active']);
                Route::put('/update', 'update');
                Route::delete('/delete', 'destroy');
            });
    });
