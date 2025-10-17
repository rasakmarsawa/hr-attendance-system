<?php

namespace App\Http\Controllers;

use App\Models\payroll;
use App\Models\Attendance;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = payroll::with('employee.user')
            ->latest()
            ->get();
            
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(payroll $payroll)
    {
        //
    }

    public function generate(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $attendances = Attendance::with('user.employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('user_id');

        foreach ($attendances as $records) {
            $employee = $records->first()->user->employee;
            if (!$employee) {
                continue; // Skip if no associated employee
            }
                
            $total_present = $records->where('status', 'present')->count();
            $total_absent  = $records->where('status', 'absent')->count();
            $total_late    = $records->where('status', 'late')->count();
            $total_pay     = $employee->daily_rate * ($total_present+$total_late);

            payroll::updateOrCreate([
                'employee_id'   => $employee->id,
                'month'         => $month,
                'year'          => $year,
            ],
            [                
                'total_present' => $total_present,
                'total_absent'  => $total_absent,
                'total_late'    => $total_late,
                'daily_rate'    => $employee->daily_rate,
                'total_pay'     => $total_pay,
                'status'        => 'draft',
            ]);
        }
        return redirect()->back()->with('success', 'Payroll generated successfully for ' . $month . '/' . $year);
    }

    public function finalize(Request $request, payroll $payroll)
    {
        //
    }

    public function finalizeAll(Request $request)
    {
        //
    }

    public function monthly(Request $request)
    {
        $payrolls = payroll::whereMonth('date',$request->month)
            ->whereYear('date',$request->year)
            ->get();
        return view('payroll.monthly', compact('payrolls'));
    }
}
