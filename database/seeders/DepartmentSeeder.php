<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'code_prefix' => 'HR', 'description' => 'Handles HR activities'],
            ['name' => 'Information Technology', 'code_prefix' => 'IT', 'description' => 'Handles IT systems'],
            ['name' => 'Finance', 'code_prefix' => 'FIN', 'description' => 'Handles financial operations'],
            ['name' => 'Marketing', 'code_prefix' => 'MKT', 'description' => 'Handles marketing activities'],
            ['name' => 'Sales', 'code_prefix' => 'SLS', 'description' => 'Handles sales and customers'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }        
    }
}
