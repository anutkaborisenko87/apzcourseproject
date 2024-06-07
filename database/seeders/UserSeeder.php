<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    final public function run(): void
    {
        $admin = [
            'last_name' => 'Admin',
            'first_name' => 'Super',
            'sex' => 'male',
            'email' => 'superAdmin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('super_admin_password')

        ];
        User::create($admin);
    }
}
