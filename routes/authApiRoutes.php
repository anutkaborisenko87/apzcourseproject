<?php

use Illuminate\Support\Facades\Route;

Route::get('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);
Route::get('/logged_user', [\App\Http\Controllers\Api\Auth\AuthController::class, 'loggedInUserInfo']);

Route::get('/user/profile', [\App\Http\Controllers\Api\UsersController::class, 'indexProfile']);
Route::get('/user/{user}', [\App\Http\Controllers\Api\UsersController::class, 'showUser'])->where(['user' => '[0-9]+'])->middleware(['is_admin', 'user_exists']);
Route::get('/roles_list', [\App\Http\Controllers\Api\RolesController::class, 'index'])->middleware(['is_admin']);

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
Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\EmployeesController::class)
    ->prefix('/employees')
    ->group(function () {
        Route::get('/active', 'indexActiveEmployees');
        Route::get('/not_active', 'indexNotActiveEmployees');
        Route::get('/working', 'indexWorkingEmployees');
        Route::post('/create', 'storeEmployee');
        Route::middleware(['employee_exists'])
            ->prefix('/{employee}')
            ->where(['employee' => '[0-9]+'])
            ->group(function () {
                Route::get('/reactivate', 'reactivateEmployee')->middleware(['employee_not_active']);
                Route::get('/deactivate', 'deactivateEmployee')->middleware(['employee_active']);
                Route::put('/update', 'update');
                Route::delete('/delete', 'destroy');
            });
    });
