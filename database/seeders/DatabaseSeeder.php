<?php

namespace Database\Seeders;

use App\Models\Children;
use App\Models\Employee;
use App\Models\Group;
use App\Models\Parrent;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    final public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PositionsSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $parrentRole = Role::where('name', 'user')->first();

        $superAdminUser = User::where('email', 'superAdmin@gmail.com')->first();
        $superAdminUser->assignRole($superAdminRole);

        $teacherPosition = Position::where('position_title', 'teacher')->first();

        for ($i = 0; $i <= 10; $i++) {
            $user = User::factory()
                ->create();
            $user->assignRole($teacherRole);

            $employee = Employee::factory()
                ->for($user, 'user')
                ->for($teacherPosition, 'position')
                ->create();
        }
        $group = Group::factory()
            ->create();

        for ($k = 0; $k <= 10; $k++) {
            $parentUser = User::factory()
                ->create();
            $parentUser->assignRole($parrentRole);
            $parent = Parrent::factory()
            ->for($parentUser, 'user')
            ->create();

            $childUser = User::factory()
                ->create();

            $child = Children::factory()
                ->for($childUser, 'user')
                ->for($group, 'group')
                ->create();

            $child->parrent_relations()->attach($parent, ['relations' => 'parent']);
        }
    }
}
