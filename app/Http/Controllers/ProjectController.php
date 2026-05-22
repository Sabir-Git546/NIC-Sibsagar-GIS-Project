<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use App\Services\AuditService;

class ProjectController extends Controller
{
    // =========================
    // LIST PROJECTS
    // =========================
    public function index()
    {
        $user = auth()->user();

        $projects = Project::query();

        // Admin + GIS operator full access
        if (!in_array($user->roleid, [1, 3])) {
            $projects->where('deptid', $user->deptid);
        }

        $projects = $projects->orderByDesc('projectid')->paginate(10)
            ->withQueryString();

        return view('projects.viewProject', compact('projects'));
    }

    // =========================
    // CREATE FORM
    // =========================
    public function create()
    {
        $this->authorizeAccess();

        return view('projects.addProject', [
            'departments' => Department::all(),
            'units' => AdministrativeUnit::all()
        ]);
    }

    // =========================
    // STORE PROJECT
    // =========================
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'projectname' => 'required|string|max:200',
            'status' => 'required|in:planning,ongoing,completed',
            'deptid' => 'required|exists:departments,deptid',
            'location_unitid' => 'required|exists:administrative_units,unitid',
            'description' => 'nullable|string'
        ]);

        // duplicate prevention
        $exists = Project::where('projectname', $data['projectname'])
            ->where('deptid', $data['deptid'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Project already exists in this department.');
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

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($projectid)
    {
        $project = Project::findOrFail($projectid);

        $this->authorizeProjectAccess($project);

        return view('projects.editProject', [
            'project' => $project,
            'departments' => Department::all(),
            'units' => AdministrativeUnit::all()
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $projectid)
    {
        $project = Project::findOrFail($projectid);

        $this->authorizeProjectAccess($project);

        $data = $request->validate([
            'projectname' => 'required|string|max:200',
            'status' => 'required|in:planning,ongoing,completed',
            'deptid' => 'required|exists:departments,deptid',
            'location_unitid' => 'required|exists:administrative_units,unitid',
            'description' => 'nullable|string'
        ]);

        // ================= ADMIN DIRECT UPDATE =================
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

            return back()->with('success', 'Project updated successfully');
        }

        // ================= USER REQUEST FLOW =================

        $pending = DB::table('approval_requests')
            ->where([
                'module' => 'PROJECT',
                'recordid' => $projectid,
                'status' => 'pending'
            ])
            ->exists();

        if ($pending) {
            return back()->with('error', 'A pending request already exists.');
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

        return back()->with('success', 'Update request sent for approval');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy($projectid)
    {
        $project = Project::findOrFail($projectid);

        $this->authorizeProjectAccess($project);

        // ADMIN DELETE
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

            return back()->with('success', 'Deleted successfully');
        }

        // USER REQUEST DELETE
        $pending = DB::table('approval_requests')
            ->where([
                'module' => 'PROJECT',
                'recordid' => $projectid,
                'status' => 'pending'
            ])
            ->exists();

        if ($pending) {
            return back()->with('error', 'Pending request already exists.');
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

        return back()->with('success', 'Delete request sent for approval');
    }

    // =========================
    // SECURITY
    // =========================
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

        if (in_array($user->roleid, [1, 3])) {
            return true;
        }

        if ($project->deptid != $user->deptid) {
            abort(403, 'Access denied');
        }

        return true;
    }
}