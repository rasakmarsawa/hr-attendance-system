<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class AttendanceExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // $attendances = Attendance::select('attendances.*')
        //     ->join('employees', 'employees.user_id', '=', 'attendances.user_id')
        //     ->orderBy('employees.department_id')
        //     ->orderBy('attendances.user_id')
        //     ->orderBy('attendances.date')
        //     ->with(['user.employee.department'])
        //     ->whereMonth('attendances.date', $month)
        //     ->whereYear('attendances.date', $year)
        //     ->get();


        // $rows = $attendances->map(function ($attendance) {
        //     return [
        //         'Employee ID' => 'EMP-'.$attendance->user_id ?? '-',
        //         'Name' => $attendance->user->name ?? '-',
        //         'Department' => $attendance->user->employee->department->name ?? '-',
        //         'Date' => carbon::parse($attendance->date)->format('d M Y'),
        //         'Check In' => $attendance->check_in,
        //         'Check Out' => $attendance->check_out,
        //         'Status' => ucfirst($attendance->status ?? '-'),
        //     ];
        // });

        $attendances = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('user_id');

        $rows = $attendances->map(function ($records) {
            return [
                'name' => $records->first()->user->name,
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'total_days' => $records->count(),
            ];
        });        

        $fileName = "attendance_report_{$year}_{$month}.xlsx";
        $filePath = storage_path("app/{$fileName}");

        SimpleExcelWriter::create($filePath)->addRows($rows->toArray());

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
