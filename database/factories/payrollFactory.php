<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class payrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition()
    {
        $employee = Employee::factory()->create(); // Assumes you have Employee factory

        $month = $this->faker->numberBetween(1, 12);
        $year  = $this->faker->year();

        $total_present = $this->faker->numberBetween(0, 22);
        $total_absent  = $this->faker->numberBetween(0, 5);
        $total_late    = $this->faker->numberBetween(0, 3);
        $daily_rate    = $employee->daily_rate ?? $this->faker->randomFloat(2, 50, 500);
        $total_pay     = $daily_rate * ($total_present + $total_late);

        return [
            'employee_id' => $employee->id,
            'month'       => $month,
            'year'        => $year,
            'total_present' => $total_present,
            'total_absent'  => $total_absent,
            'total_late'    => $total_late,
            'daily_rate'    => $daily_rate,
            'total_pay'     => $total_pay,
            'status'        => 'draft',
            'issued_by'     => null,
            'issued_at'     => null,
            'payment_datetime' => null,
            'department_name' => $employee->department ? $employee->department->name : null,
        ];
    }

    /**
     * Mark payroll as finalized
     */
    public function finalized()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'finalized',
                'issued_by' => 1, // you can override
                'issued_at' => now(),
            ];
        });
    }

    /**
     * Mark payroll as paid
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_datetime' => now(),
            ];
        });
    }
}
