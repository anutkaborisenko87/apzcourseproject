<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    final public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin'
            ],
            [
                'name' => 'teacher'
            ],
            [
                'name' => 'user'
            ]
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
