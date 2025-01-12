<?php

namespace App\Providers;

use App\Interfaces\RepsitotiesInterfaces\ChildrenRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\DashboardRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\EmployeesRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\GroupRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\ParrentsRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\PositionsRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\RoleRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Interfaces\ServicesInterfaces\ChildrenServiceInterface;
use App\Interfaces\ServicesInterfaces\DashboardServiceInterface;
use App\Interfaces\ServicesInterfaces\EmployeesServiceInterface;
use App\Interfaces\ServicesInterfaces\GroupServiceInterface;
use App\Interfaces\ServicesInterfaces\ParrentsServiceInterface;
use App\Interfaces\ServicesInterfaces\PositionsServiceInterface;
use App\Interfaces\ServicesInterfaces\RolesServiceInterface;
use App\Interfaces\ServicesInterfaces\UserServiceInterface;
use App\Repositories\ChildrenRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\GroupRepository;
use App\Repositories\ParrentsRepository;
use App\Repositories\PositionsRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\ChildrenService;
use App\Services\DashboardService;
use App\Services\EmployeesService;
use App\Services\GroupService;
use App\Services\ParrentsService;
use App\Services\PositionsService;
use App\Services\RolesService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ChildrenRepositoryInterface::class, ChildrenRepository::class);
        $this->app->bind(EmployeesRepositoryInterface::class, EmployeesRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(ParrentsRepositoryInterface::class, ParrentsRepository::class);
        $this->app->bind(PositionsRepositoryInterface::class, PositionsRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ChildrenServiceInterface::class, ChildrenService::class);
        $this->app->bind(EmployeesServiceInterface::class, EmployeesService::class);
        $this->app->bind(GroupServiceInterface::class, GroupService::class);
        $this->app->bind(ParrentsServiceInterface::class, ParrentsService::class);
        $this->app->bind(PositionsServiceInterface::class, PositionsService::class);
        $this->app->bind(RolesServiceInterface::class, RolesService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
