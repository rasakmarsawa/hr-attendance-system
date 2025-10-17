<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Admin user
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        // Employee users
        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate([
                'email' => "employee{$i}@example.com",
            ], [
                'name' => "Employee {$i}",
                'password' => Hash::make('password'),
                'role_id' => 2,
            ]);
        }
    }
}
