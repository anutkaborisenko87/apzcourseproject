<?php

namespace App\Providers;

use App\Interfaces\RepsitotiesInterfaces\IChildrenRepository;
use App\Interfaces\RepsitotiesInterfaces\IEmployeesRepository;
use App\Interfaces\RepsitotiesInterfaces\IGroupRepository;
use App\Interfaces\RepsitotiesInterfaces\IParrentsRepository;
use App\Interfaces\RepsitotiesInterfaces\IPositionsRepository;
use App\Interfaces\RepsitotiesInterfaces\IRoleRepository;
use App\Interfaces\RepsitotiesInterfaces\IUserRpository;
use App\Interfaces\ServicesInterfaces\IChildrenService;
use App\Interfaces\ServicesInterfaces\IEmployeesService;
use App\Interfaces\ServicesInterfaces\IGroupService;
use App\Interfaces\ServicesInterfaces\IParrentsService;
use App\Interfaces\ServicesInterfaces\IPositionsService;
use App\Interfaces\ServicesInterfaces\IRolesService;
use App\Interfaces\ServicesInterfaces\IUserService;
use App\Repositories\ChildrenRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\GroupRepository;
use App\Repositories\ParrentsRepository;
use App\Repositories\PositionsRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\ChildrenService;
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
    public function register()
    {
        $this->app->bind(IChildrenRepository::class, ChildrenRepository::class);
        $this->app->bind(IEmployeesRepository::class, EmployeesRepository::class);
        $this->app->bind(IGroupRepository::class, GroupRepository::class);
        $this->app->bind(IParrentsRepository::class, ParrentsRepository::class);
        $this->app->bind(IPositionsRepository::class, PositionsRepository::class);
        $this->app->bind(IRoleRepository::class, RoleRepository::class);
        $this->app->bind(IUserRpository::class, UserRepository::class);
        $this->app->bind(IChildrenService::class, ChildrenService::class);
        $this->app->bind(IEmployeesService::class, EmployeesService::class);
        $this->app->bind(IGroupService::class, GroupService::class);
        $this->app->bind(IParrentsService::class, ParrentsService::class);
        $this->app->bind(IPositionsService::class, PositionsService::class);
        $this->app->bind(IRolesService::class, RolesService::class);
        $this->app->bind(IUserService::class, UserService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
