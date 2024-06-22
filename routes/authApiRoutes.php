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
                Route::get('/', 'showEmployee');
                Route::post('/fire-employee', 'fireEmployee')->middleware(['employee_hired']);
                Route::get('/reactivate', 'reactivateEmployee')->middleware(['employee_not_active']);
                Route::get('/deactivate', 'deactivateEmployee')->middleware(['employee_active']);
                Route::put('/update', 'update');
                Route::delete('/delete', 'destroy');
            });
    });

Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\PositionsController::class)
    ->prefix('/positions')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/create', 'store');
        Route::middleware(['position_exists'])
            ->prefix('/{position}')
            ->where(['position' => '[0-9]+'])
            ->group(function () {
                Route::get('/', 'show');
                Route::put('/update', 'update');
                Route::delete('/delete', 'destroy');
            });
    });

Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\ParrentsController::class)
    ->prefix('/parrents')
    ->group(function () {
        Route::get('/for-select', 'indexForSelect');
        Route::get('/for-select/{child}', 'indexForSelect')->where(['child' => '[0-9]+']);
        Route::get('/active', 'indexActive');
        Route::get('/not-active', 'indexNotActive');
        Route::post('/create', 'store');
        Route::middleware(['parrent_exists'])
            ->prefix('/{parrent}')
            ->where(['parrent' => '[0-9]+'])
            ->group(function () {
                Route::get('/', 'show');
                Route::get('/deactivate', 'deactivate')->middleware(['parrent_active']);
                Route::get('/reactivate', 'reactivate')->middleware(['parrent_not_active']);
                Route::put('/update', 'update');
                Route::delete('/delete', 'destroy');
            });
    });

Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\ChildrenController::class)
    ->prefix('/children')
    ->group(function () {
        Route::get('/for-select', 'indexForSelect');
        Route::get('/for-select/{parrent}', 'indexForUpdateSelect')->where(['parrent' => '[0-9]+']);
        Route::get('/all', 'indexAllChildren');
        Route::get('/for-enrolment', 'indexForEnrolmentChildren');
        Route::get('/in-training', 'indexInTrainingChildren');
        Route::get('/graduated', 'indexGraduatedChildren');
        Route::post('/create', 'createChild');
        Route::middleware(['child_exists'])
            ->prefix('/{child}')
            ->where(['child' => '[0-9]+'])
            ->group(function () {
                Route::get('/', 'showChild');
                Route::put('/update', 'updateChild');
                Route::delete('/delete', 'deleteChild');
            });
    });

Route::middleware(['is_admin'])
    ->controller(\App\Http\Controllers\Api\GroupsController::class)
    ->prefix('/groups')
    ->group(function () {
        Route::get('/for-select', 'indexSelect');
        Route::get('/', 'index');
        Route::post('/create', 'storeGroupInfo');
        Route::middleware(['group_exists'])
            ->prefix('/{group}')
            ->where(['group' => '[0-9]+'])
            ->group(function () {
                Route::get('/', 'showGroupInfo');
                Route::post('/', 'showFullGroupInfo');
                Route::put('/update', 'updateGroupInfo');
                Route::delete('/delete', 'destroyGroupInfo');
            });
    });
