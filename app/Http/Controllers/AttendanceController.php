<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('date', date('Y-m-d'))->get();
        return view('attendance.index', compact('attendances'));
    }

    public function store(Request $request)
    {
        $employees = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })
            ->get();
        $today = date('Y-m-d');

        foreach ($employees as $employee) {
            Attendance::firstOrCreate(
                ['user_id' => $employee->id, 'date' => $today],
                ['status' => 'absent']
            );
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance records pre-filled for today.');
    }

    public function checkIn(Request $request)
    {   
        $user = auth()->user();

        if($user->employee == NULL || $user->employee->status != 'active'){
            return redirect()->back()->with('error', 'Your employee status is not active. You cannot check in.');
        }

        $today = date('Y-m-d');
        $currentTime = Carbon::now();
        $cutoff = Carbon::createFromTime(10, 0, 0);

        $status = $currentTime->gt($cutoff) ? 'late' : 'present';

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => $status]
        );

        $attendance->check_in = date('H:i:s');
        $attendance->save();

        return redirect()->back()->with('success', 'Checked in successfully at ' . $attendance->check_in);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $today = date('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return redirect()->back()->with('error', 'You need to check in first before checking out.');
        }

        $attendance->check_out = date('H:i:s');
        $attendance->save();

        return redirect()->back()->with('success', 'Checked out successfully at ' . $attendance->check_out);
    }

    public function report(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendances = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('user_id');

        $report = $attendances->map(function ($records) {
            return [
                'user_id' => $records->first()->user_id,
                'name' => $records->first()->user->name,
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'total_days' => $records->count(),
            ];
        });

        return view('attendance.report', compact('report', 'month', 'year'));
    }
    
    public function detail($user_id, $month, $year)
    {
        $attendances = Attendance::where('user_id', $user_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $user = User::findOrFail($user_id);

        return view('attendance.detail', compact('attendances', 'user', 'month', 'year'));
    }
}
