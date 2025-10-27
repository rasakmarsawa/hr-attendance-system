<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class attendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        // Random check-in and check-out times
        $checkIn = $this->faker->time('H:i:s', '09:00:00');
        $checkOut = $this->faker->time('H:i:s', '18:00:00');

        // Random status
        $statusOptions = ['present', 'absent', 'late'];

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->date(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $this->faker->randomElement($statusOptions),
        ];
    }

    /**
     * Optional: mark as absent with no check-in/check-out
     */
    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_in' => null,
                'check_out' => null,
                'status' => 'absent',
            ];
        });
    }

    /**
     * Optional: mark as present with normal times
     */
    public function present()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'present',
                'check_in' => '08:30:00',
                'check_out' => '17:30:00',
            ];
        });
    }

    /**
     * Optional: mark as late
     */
    public function late()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'late',
                'check_in' => '10:30:00',
                'check_out' => '18:00:00',
            ];
        });
    }
}
