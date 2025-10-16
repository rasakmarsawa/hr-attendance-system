<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::paginate(10);
        return view('department.index', compact('departments'));
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code_prefix' => 'nullable|string|max:10|unique:departments,code_prefix',
            'description' => 'nullable|string|max:1000',
        ]);

        $department = Department::create($data);

        return redirect()
            ->route('department.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('department.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code_prefix' => 'nullable|string|max:10|unique:departments,code_prefix,' . $department->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $department->update($data);

        return redirect()
            ->route('department.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()
            ->route('department.index')
            ->with('success', 'Department deleted successfully.');
    }
}
