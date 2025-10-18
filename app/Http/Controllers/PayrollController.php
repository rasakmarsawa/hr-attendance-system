<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Attendance;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index($month, $year)
    {
        $payrolls = Payroll::select('payrolls.*')
            ->join('employees', 'employees.id', '=', 'payrolls.employee_id')
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->where('payrolls.month', $month)
            ->where('payrolls.year', $year)
            ->orderBy('departments.name', 'asc')
            ->with(['employee.user', 'employee.department'])
            ->paginate(10);

        return view('payroll.index', compact('payrolls', 'month', 'year'));
    }

    public function edit(payroll $payroll)
    {        
        return view('payroll.edit', compact('payroll'));
    }

    public function update(Request $request, payroll $payroll)
    {
        if($payroll->status == 'finalized'){
            return redirect()->back()->with('error', 'Cannot edit finalized payroll for ' . $payroll->employee->user->name);
        }

        $request->validate([
            'total_present' => 'required|integer|min:0',
            'total_absent'  => 'required|integer|min:0',
            'total_late'    => 'required|integer|min:0',
            'daily_rate'    => 'required|numeric|min:0',
            'total_pay'     => 'required|numeric|min:0',
        ]);

        $payroll->update([
            'total_present' => $request->total_present,
            'total_absent'  => $request->total_absent,
            'total_late'    => $request->total_late,
            'daily_rate'    => $request->daily_rate,
            'total_pay'     => $request->total_pay,
        ]);

        return redirect()->route('payroll.index', ['month' => $payroll->month, 'year' => $payroll->year])
                         ->with('success', 'Payroll updated successfully for ' . $payroll->employee->user->name);
    }

    public function generate(Request $request, $month, $year)
    {    
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

            Payroll::updateOrCreate([
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
                'issued_at'    => null,
                'issued_by'    => null,
                'payment_datetime' => null,

            ]);
        }
        return redirect()->back()->with('success', 'Payroll generated successfully for ' . $month . '/' . $year);
    }

    public function finalize(Request $request, payroll $payroll)    
    {
        if($payroll->status == 'finalized'){
            return redirect()->back()->with('error', 'Payroll is already finalized for ' . $payroll->employee->user->name);
        }

        $payroll->update([
            'status' => 'finalized',
            'issued_at' => now(),
            'issued_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Payroll finalized successfully for ' . $payroll->employee->user->name);
    }

    public function finalizeAll(Request $request, $month, $year)
    {        
        Payroll::where('month', $month)
            ->where('year', $year)
            ->where('status', 'draft')
            ->update([
                'status' => 'finalized',
                'issued_at' => now(),
                'issued_by' => auth()->id(),
            ]);

        return redirect()->back()->with('success', 'All payrolls finalized successfully for ' . $month . '/' . $year);
    }

    public function export($month, $year)
    {
        $payrolls = Payroll::with(['employee.user', 'employee.department'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('employee_id')
            ->get();

        $pdf = Pdf::loadView('payroll.pdf', compact('payrolls', 'month', 'year'))
            ->setPaper('a4', 'portrait');

        $filename = "Payroll_{$month}_{$year}.pdf";

        return $pdf->download($filename);
    }   
    
    public function exportOne(payroll $payroll)
    {
        $pdf = Pdf::loadView('payroll.single_pdf', compact('payroll'))
            ->setPaper('a4', 'portrait');

        $filename = "Payroll_{$payroll->employee->user->name}_{$payroll->month}_{$payroll->year}.pdf";

        return $pdf->download($filename);
    }

    public function pay(Request $request, payroll $payroll)
    {
        $payroll->update([
            'payment_datetime' => now(),
        ]);

        return redirect()->back()->with('success', 'Payroll marked as paid for ' . $payroll->employee->user->name);
    }   

    public function myPayroll()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'No employee record found for your account.');
        }

        $payrolls = Payroll::where('employee_id', $employee->id)
            ->where('status', 'finalized')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('payroll.my_payroll', compact('payrolls'));
    }
}
