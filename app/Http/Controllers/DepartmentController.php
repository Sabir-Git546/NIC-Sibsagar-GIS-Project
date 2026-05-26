<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AdministrativeUnit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

use App\Services\AuditService;

class DepartmentController extends Controller
{
    // show department table in viewdepartment
    public function index()
    {
        try {

            $departments = Department::with('unit')
                ->orderBy('deptid', 'asc')
                ->paginate(10)
                ->withQueryString();

            return view(
                'departments.viewDepartment',
                compact('departments')
            );

        } catch (\Exception $e) {

            Log::error('Department Index Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load departments.'
            );
        }
    }


    // department add form
    public function create()
    {
        try {

            $units = AdministrativeUnit::orderBy(
                'unitname',
                'asc'
            )->get();

            return view(
                'departments.addDepartment',
                compact('units')
            );

        } catch (\Exception $e) {

            Log::error('Department Create Form Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load create department page.'
            );
        }
    }


    // store form data in department table
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'deptname' => 'required|string|max:100',
                'deptdescription' => 'nullable|string|max:255',
                'unitid' => 'required|exists:administrative_units,unitid',
            ]);

            DB::beginTransaction();

            // Duplicate prevention
            $exists = Department::whereRaw(
                'LOWER(deptname) = ?',
                [strtolower(trim($data['deptname']))]
            )->exists();

            if ($exists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Department already exists.'
                );
            }

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

            DB::commit();

            return redirect()
                ->route('department.index')
                ->with(
                    'success',
                    'Department added successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Department Store Database Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while creating department.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Department Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to create department.'
            );
        }
    }


    // department details edit form
    public function edit($deptid)
    {
        try {

            $department = Department::where(
                'deptid',
                $deptid
            )->firstOrFail();

            $units = AdministrativeUnit::orderBy(
                'unitname',
                'asc'
            )->get();

            return view(
                'departments.editDepartment',
                compact('department', 'units')
            );

        } catch (\Exception $e) {

            Log::error('Department Edit Error', [
                'message' => $e->getMessage(),
                'deptid' => $deptid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load edit department page.'
            );
        }
    }


    // department details update in db
    public function update(Request $request, $deptid)
    {
        try {

            $data = $request->validate([
                'deptname' => 'required|string|max:100',
                'deptdescription' => 'nullable|string|max:255',
                'unitid' => 'required|exists:administrative_units,unitid',
            ]);

            DB::beginTransaction();

            $department = Department::where(
                'deptid',
                $deptid
            )->firstOrFail();

            // Duplicate prevention
            $exists = Department::whereRaw(
                'LOWER(deptname) = ?',
                [strtolower(trim($data['deptname']))]
            )
                ->where('deptid', '!=', $deptid)
                ->exists();

            if ($exists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Department name already exists.'
                );
            }

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
                $department->fresh()->toArray()
            );

            DB::commit();

            return redirect()
                ->route('department.index')
                ->with(
                    'success',
                    'Department updated successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Department Update Database Error', [
                'message' => $e->getMessage(),
                'deptid' => $deptid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while updating department.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Department Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'deptid' => $deptid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to update department.'
            );
        }
    }


    // delete a department record from db
    public function destroy($deptid)
    {
        try {

            DB::beginTransaction();

            $department = Department::where(
                'deptid',
                $deptid
            )->firstOrFail();

            $oldData = $department->toArray();

            $department->delete();

            AuditService::log(
                'DELETE',
                'DEPARTMENT',
                'Department deleted: ' . $deptid,
                $oldData,
                null
            );

            DB::commit();

            return redirect()
                ->route('department.index')
                ->with(
                    'success',
                    'Department deleted successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Department Delete Database Error', [
                'message' => $e->getMessage(),
                'deptid' => $deptid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while deleting department.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Department Delete Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'deptid' => $deptid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to delete department.'
            );
        }
    }
}