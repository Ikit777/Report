<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'Fuelman User',
            'email' => 'fuelman@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'fuelman',
        ]);

        User::create([
            'name' => 'Group Leader User',
            'email' => 'gl@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'group_leader',
        ]);

        User::create([
            'name' => 'Supervisor User',
            'email' => 'spv@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        // 2. Seed Tanks (from the Excel layout)
        $tanks = [
            ['code' => 'SPM1', 'main_hole' => 'TENGAH'],
            ['code' => 'SPM2', 'main_hole' => 'TENGAH'],
            ['code' => 'SPM3', 'main_hole' => 'DEPAN'],
            ['code' => 'SPM3', 'main_hole' => 'BELAKANG'],
            ['code' => 'SPM3', 'main_hole' => '(D+B)/2'],
            ['code' => 'FT05', 'main_hole' => 'TENGAH'],
        ];

        foreach ($tanks as $tank) {
            Tank::create($tank);
        }
    }
}
