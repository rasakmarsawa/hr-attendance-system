<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class DummyAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $start = Carbon::parse('2025-09-01');
        $end = Carbon::parse('2025-09-30');

        foreach ($users as $user) {
            $date = $start->copy();
            while ($date->lte($end)) {
                if (!$date->isWeekend()) {
                    Attendance::firstOrCreate([
                        'user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                    ], [
                        'check_in' => '09:00:00',
                        'check_out' => '17:00:00',
                        'status' => 'present',
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
