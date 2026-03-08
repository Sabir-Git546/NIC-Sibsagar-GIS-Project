<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // show all departments details
    public function index()
    {
        $departments = Department::orderBy('deptid', 'asc')->get();
        return view('departments.viewDepartment', compact('departments'));
    }

    // department add form
    public function create()
    {
        return view('departments.addDepartment');
    }

    // store departments
    public function store(Request $request)
    {
        $request->validate([
            'deptname' => 'required|string|max:100',
            'deptdescription' => 'nullable|string|max:255',
        ]);

        Department::create([
            'deptname' => $request->deptname,
            'deptdescription' => $request->deptdescription,
        ]);

        return redirect()->route('department.index')
                         ->with('success', 'Department added successfully!');
    }

    // show department edit form
    public function edit($deptid)
    {
        $department = Department::where('deptid', $deptid)->firstOrFail();
        return view('departments.editDepartment', compact('department'));
    }

   // update department details 
    public function update(Request $request, $deptid)
    {
        $request->validate([
            'deptname' => 'required|string|max:100',
            'deptdescription' => 'nullable|string|max:255',
        ]);

        $department = Department::where('deptid', $deptid)->firstOrFail();

        $department->update([
            'deptname' => $request->deptname,
            'deptdescription' => $request->deptdescription,
        ]);

        return redirect()->route('department.index')
                         ->with('success', 'Department updated successfully!');
    }

    // delete department
    public function destroy($deptid)
    {
        $department = Department::where('deptid', $deptid)->firstOrFail();

        $department->delete();

        return redirect()->route('department.index')
                         ->with('success', 'Department deleted successfully!');
    }
}