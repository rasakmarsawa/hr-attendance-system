<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::all();

        foreach ($employees as $user) {
            Employee::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'department_id' => rand(1, 5), // if you have departments
                'position' => 'Staff',
                'daily_rate' => rand(150_000, 300_000), // daily rate in IDR
                'join_date' => now()->subMonths(rand(1, 12)),
            ]);
        }
    }
}
