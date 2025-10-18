<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function create($user)
    {
        $departments = Department::all();
        return view('employee.create',compact('departments','user'));
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'join_date' => 'required|date',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        Employee::create($data);

        return redirect()->route('user.show', $request->user_id)
                         ->with('success', 'Employee HR data created successfully.');
    }

    public function edit($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $departments = Department::all();
        return view('employee.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'join_date' => 'required|date',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($data);

        return redirect()->route('user.show', $employee->user_id)
                         ->with('success', 'Employee HR data updated successfully.');
    }

    public function destroy($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $user_id = $employee->user_id;
        $employee->delete();

        return redirect()->route('user.show', $user_id)
                         ->with('success', 'Employee HR data deleted successfully.');
    }
}
