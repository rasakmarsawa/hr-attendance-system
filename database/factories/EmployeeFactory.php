<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // automatically creates a user if not provided
            'department_id' => Department::factory(),
            'position' => $this->faker->jobTitle(),
            'daily_rate' => $this->faker->numberBetween(100000, 500000),
            'join_date' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
        ];
    }
}
