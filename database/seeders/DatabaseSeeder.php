<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin ANAMTA',
                'email' => 'umroh.anamta.admin@gmail.com',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager ANAMTA',
                'email' => 'umroh.anamta.manager@gmail.com',
                'username' => 'manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        User::updateOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff ANAMTA',
                'email' => 'umroh.anamta.staff@gmail.com',
                'username' => 'staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );
    }
}
