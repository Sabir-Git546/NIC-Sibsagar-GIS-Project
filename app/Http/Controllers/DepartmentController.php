<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AdministrativeUnit;
use Illuminate\Http\Request;
use App\Services\AuditService;

class DepartmentController extends Controller
{
    // =========================
    // LIST DEPARTMENTS
    // =========================
    public function index()
    {
        $departments = Department::with('unit')
            ->orderBy('deptid', 'asc')
            ->paginate(10)

            ->withQueryString();

        return view('departments.viewDepartment', compact('departments'));
    }

    // =========================
    // CREATE FORM
    // =========================
    public function create()
    {
        $units = AdministrativeUnit::orderBy('unitname', 'asc')->get();

        return view('departments.addDepartment', compact('units'));
    }

    // =========================
    // STORE DEPARTMENT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'deptname' => 'required|string|max:100',
            'deptdescription' => 'nullable|string|max:255',
            'unitid' => 'required|exists:administrative_units,unitid',
        ]);

        $department = Department::create([
            'deptname' => trim($request->deptname),
            'deptdescription' => trim($request->deptdescription),
            'unitid' => $request->unitid,
        ]);

        AuditService::log(
            'CREATE',
            'DEPARTMENT',
            'Department created: ' . $department->deptid,
            null,
            $department->toArray()
        );

        return redirect()
            ->route('department.index')
            ->with('success', 'Department added successfully!');
    }

    // =========================
    // EDIT FORM
    // =========================
    public function edit($deptid)
    {
        $department = Department::where('deptid', $deptid)->firstOrFail();

        $units = AdministrativeUnit::orderBy('unitname', 'asc')->get();

        return view('departments.editDepartment', compact('department', 'units'));
    }

    // =========================
    // UPDATE DEPARTMENT
    // =========================
    public function update(Request $request, $deptid)
    {
        $request->validate([
            'deptname' => 'required|string|max:100',
            'deptdescription' => 'nullable|string|max:255',
            'unitid' => 'required|exists:administrative_units,unitid',
        ]);

        $department = Department::where('deptid', $deptid)->firstOrFail();

        $oldData = $department->toArray();

        $department->update([
            'deptname' => trim($request->deptname),
            'deptdescription' => trim($request->deptdescription),
            'unitid' => $request->unitid,
        ]);

        AuditService::log(
            'UPDATE',
            'DEPARTMENT',
            'Department updated: ' . $deptid,
            $oldData,
            $department->toArray()
        );

        return redirect()
            ->route('department.index')
            ->with('success', 'Department updated successfully!');
    }

    // =========================
    // DELETE DEPARTMENT
    // =========================
    public function destroy($deptid)
    {
        $department = Department::where('deptid', $deptid)->firstOrFail();

        $oldData = $department->toArray();

        $department->delete();

        AuditService::log(
            'DELETE',
            'DEPARTMENT',
            'Department deleted: ' . $deptid,
            $oldData,
            null
        );

        return redirect()
            ->route('department.index')
            ->with('success', 'Department deleted successfully!');
    }
}