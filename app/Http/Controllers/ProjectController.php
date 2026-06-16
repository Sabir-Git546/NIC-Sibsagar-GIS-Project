<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

use App\Models\Project;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Services\AuditService;

class ProjectController extends Controller
{
    // list of projects in viewprojects
    public function index(Request $request)
{
    try {

        $user = auth()->user();

        $projects = Project::with([
            'department',
            'locationUnit'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Role Restriction
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->roleid, [1, 3])) {

            $projects->where(
                'deptid',
                $user->deptid
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Universal Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $projects->where(function ($query) use ($search) {

                $query->where(
                    'projectname',
                    'LIKE',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'LIKE',
                    "%{$search}%"
                )

                ->orWhere(
                    'status',
                    'LIKE',
                    "%{$search}%"
                )

                ->orWhereHas('department', function ($q) use ($search) {

                    $q->where(
                        'deptname',
                        'LIKE',
                        "%{$search}%"
                    );
                })

                ->orWhereHas('locationUnit', function ($q) use ($search) {

                    $q->where(
                        'unitname',
                        'LIKE',
                        "%{$search}%"
                    );
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $projects->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('deptid')) {

            $projects->where(
                'deptid',
                $request->deptid
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $projects = $projects
            ->orderByDesc('projectid')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Departments For Filter
        |--------------------------------------------------------------------------
        */

        $departments = Department::orderBy(
            'deptname',
            'asc'
        )->get();

        return view(
            'projects.viewProject',
            compact(
                'projects',
                'departments'
            )
        );

    } catch (\Exception $e) {

        Log::error('Project Index Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user' => auth()->id()
        ]);

        return back()->with(
            'error',
            'Unable to load projects.'
        );
    }
}


    // create project
    public function create()
    {
        try {

            $this->authorizeAccess();

            return view('projects.addProject', [
                'departments' => Department::all(),
                'units' => AdministrativeUnit::all()
            ]);

        } catch (\Exception $e) {

            Log::error('Project Create Form Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load create project page.'
            );
        }
    }


    // store project in db
    public function store(Request $request)
    {
        try {

            $this->authorizeAccess();

            $data = $request->validate([
                'projectname' => 'required|string|max:200',
                'status' => 'required|in:planning,ongoing,completed',
                'deptid' => 'required|exists:departments,deptid',
                'location_unitid' => 'required|exists:administrative_units,unitid',
                'description' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Duplicate prevention
            $exists = Project::where(
                'projectname',
                $data['projectname']
            )
                ->where('deptid', $data['deptid'])
                ->exists();

            if ($exists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Project already exists in this department.'
                );
            }

            $project = Project::create([
                ...$data,
                'createdby' => auth()->user()->userid,
            ]);

            AuditService::log(
                'CREATE',
                'PROJECT',
                'Project created: ' . $project->projectid,
                null,
                $project->toArray()
            );

            DB::commit();

            return redirect()
                ->route('projects.index')
                ->with(
                    'success',
                    'Project created successfully'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Project Store Database Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while creating project.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Project Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to create project.'
            );
        }
    }


    // edit project details
    public function edit($projectid)
    {
        try {

            $project = Project::findOrFail($projectid);

            $this->authorizeProjectAccess($project);

            return view('projects.editProject', [
                'project' => $project,
                'departments' => Department::all(),
                'units' => AdministrativeUnit::all()
            ]);

        } catch (\Exception $e) {

            Log::error('Project Edit Error', [
                'message' => $e->getMessage(),
                'projectid' => $projectid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load project edit page.'
            );
        }
    }


    // update th edit in db
    public function update(Request $request, $projectid)
    {
        try {

            $project = Project::findOrFail($projectid);

            $this->authorizeProjectAccess($project);

            $data = $request->validate([
                'projectname' => 'required|string|max:200',
                'status' => 'required|in:planning,ongoing,completed',
                'deptid' => 'required|exists:departments,deptid',
                'location_unitid' => 'required|exists:administrative_units,unitid',
                'description' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // admin update directly
            if (auth()->user()->roleid == 1) {

                $old = $project->toArray();

                $project->update($data);

                AuditService::log(
                    'UPDATE',
                    'PROJECT',
                    'Admin updated project: ' . $projectid,
                    $old,
                    $project->fresh()->toArray()
                );

                DB::commit();

                return back()->with(
                    'success',
                    'Project updated successfully'
                );
            }

            // user sends request to admine to update

            $pending = DB::table('approval_requests')
                ->where([
                    'module' => 'PROJECT',
                    'recordid' => $projectid,
                    'status' => 'pending'
                ])
                ->exists();

            if ($pending) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'A pending request already exists.'
                );
            }

            DB::table('approval_requests')->insert([
                'userid' => auth()->user()->userid,
                'module' => 'PROJECT',
                'action' => 'update_request',
                'recordid' => $projectid,
                'old_data' => json_encode($project->toArray()),
                'new_data' => json_encode($data),
                'status' => 'pending',
                'created_at' => now()
            ]);

            AuditService::log(
                'UPDATE_REQUEST',
                'PROJECT',
                'Update requested: ' . $projectid,
                $project->toArray(),
                $data
            );

            DB::commit();

            return back()->with(
                'success',
                'Update request sent for approval'
            );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Project Update Database Error', [
                'message' => $e->getMessage(),
                'projectid' => $projectid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while updating project.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Project Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'projectid' => $projectid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to update project.'
            );
        }
    }


    // delete a project
    public function destroy($projectid)
    {
        try {

            $project = Project::findOrFail($projectid);

            $this->authorizeProjectAccess($project);

            DB::beginTransaction();

            // admine directly delete
            if (auth()->user()->roleid == 1) {

                $old = $project->toArray();

                $project->delete();

                AuditService::log(
                    'DELETE',
                    'PROJECT',
                    'Admin deleted project: ' . $projectid,
                    $old,
                    null
                );

                DB::commit();

                return back()->with(
                    'success',
                    'Deleted successfully'
                );
            }

            // user request admin to delete prj
            $pending = DB::table('approval_requests')
                ->where([
                    'module' => 'PROJECT',
                    'recordid' => $projectid,
                    'status' => 'pending'
                ])
                ->exists();

            if ($pending) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Pending request already exists.'
                );
            }

            DB::table('approval_requests')->insert([
                'userid' => auth()->user()->userid,
                'module' => 'PROJECT',
                'action' => 'delete_request',
                'recordid' => $projectid,
                'old_data' => json_encode($project->toArray()),
                'new_data' => null,
                'status' => 'pending',
                'created_at' => now()
            ]);

            AuditService::log(
                'DELETE_REQUEST',
                'PROJECT',
                'Delete requested: ' . $projectid,
                $project->toArray(),
                null
            );

            DB::commit();

            return back()->with(
                'success',
                'Delete request sent for approval'
            );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('Project Delete Database Error', [
                'message' => $e->getMessage(),
                'projectid' => $projectid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while deleting project.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Project Delete Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'projectid' => $projectid,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to delete project.'
            );
        }
    }


    // authorization security
    private function authorizeAccess()
    {
        if (!auth()->check()) {

            abort(403);
        }

        if (!in_array(auth()->user()->roleid, [1, 2, 3])) {

            abort(403);
        }
    }


    private function authorizeProjectAccess($project)
    {
        $user = auth()->user();

        // Admin + GIS operator
        if (in_array($user->roleid, [1, 3])) {

            return true;
        }

        // Unauthorized access logging
        if ($project->deptid != $user->deptid) {

            Log::warning('Unauthorized Project Access Attempt', [
                'user' => $user->userid,
                'projectid' => $project->projectid
            ]);

            abort(403, 'Access denied');
        }

        return true;
    }
}