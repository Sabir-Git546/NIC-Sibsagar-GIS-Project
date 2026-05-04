<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Department;
use App\Models\AdministrativeUnit;

class ProjectController extends Controller
{
    // =========================
    // LIST PROJECTS
    // =========================
    public function index()
    {
        $projects = Project::orderBy('projectid', 'asc')->get();

        return view('projects.viewProject', compact('projects'));
    }

    // =========================
    // CREATE FORM
    // =========================
    public function create()
    {
        $departments = Department::all();
        $units = AdministrativeUnit::all();

        return view('projects.addProject', compact('departments', 'units'));
    }

    // =========================
    // STORE PROJECT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'projectname' => 'required|string|max:200',
            'status' => 'required|string|max:50',
            'deptid' => 'required|integer',
            'location_unitid' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $project = Project::create([
            'projectname' => $request->projectname,
            'description' => $request->description,
            'status' => $request->status,
            'deptid' => $request->deptid,
            'location_unitid' => $request->location_unitid,
            'createdby' => session('userid'),
        ]);

        audit_log(
            'create',
            'project',
            $project->projectid,
            null,
            $project->toArray()
        );

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully');
    }

    // =========================
    // EDIT FORM
    // =========================
    public function edit($projectid)
    {
        $project = Project::findOrFail($projectid);
        $departments = Department::all();
        $units = AdministrativeUnit::all();

        return view('projects.editProject', compact('project', 'departments', 'units'));
    }

    // =========================
    // UPDATE REQUEST (NOT DIRECT UPDATE)
    // =========================
    public function update(Request $request, $projectid)
    {
        $project = Project::findOrFail($projectid);

        $newData = [
            'projectname' => $request->projectname,
            'description' => $request->description,
            'status' => $request->status,
            'deptid' => $request->deptid,
            'location_unitid' => $request->location_unitid
        ];

        DB::table('approval_requests')->insert([
            'userid' => session('userid'),
            'module' => 'project',
            'action' => 'update_request',
            'recordid' => $projectid,
            'old_data' => json_encode($project->toArray()),
            'new_data' => json_encode($newData),
            'status' => 'pending',
            'created_at' => now()
        ]);

        audit_log(
            'update_request',
            'project',
            $projectid,
            $project->toArray(),
            $newData
        );

        return redirect()->route('projects.index')
            ->with('success', 'Update request sent for approval');
    }

    // =========================
    // DELETE REQUEST
    // =========================
    public function destroy($projectid)
    {
        $project = Project::findOrFail($projectid);

        DB::table('approval_requests')->insert([
            'userid' => session('userid'),
            'module' => 'project',
            'action' => 'delete_request',
            'recordid' => $projectid,
            'old_data' => json_encode($project->toArray()),
            'new_data' => null,
            'status' => 'pending',
            'created_at' => now()
        ]);

        audit_log(
            'delete_request',
            'project',
            $projectid,
            $project->toArray(),
            null
        );

        return redirect()->route('projects.index')
            ->with('success', 'Delete request sent for approval');
    }
}