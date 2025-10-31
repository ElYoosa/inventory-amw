<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@amw.test'],
            [
                'name' => 'Admin AMW',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'staff@amw.test'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // Manajer
        User::updateOrCreate(
            ['email' => 'manager@amw.test'],
            [
                'name' => 'Manajer Operasional',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'is_active' => true,
            ]
        );
    }
}
