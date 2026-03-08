<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Department;
use App\Models\AdministrativeUnit;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Show all projects
    public function index()
    {
        $projects = Project::orderBy('projectid', 'asc')->get();
        return view('projects.viewProject', compact('projects'));
    }

    // Show add project form
    public function create()
    {
        $departments = Department::all();
        $units = AdministrativeUnit::all();
        return view('projects.addProject', compact('departments', 'units'));
    }

    // Store project
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

        return redirect()->route('projects.index')
                         ->with('success', 'Project created successfully!');
    }

    // Show edit form
    public function edit($projectid)
    {
        $project = Project::findOrFail($projectid);
        $departments = Department::all();
        $units = AdministrativeUnit::all();
        return view('projects.editProject', compact('project', 'departments', 'units'));
    }

    // Update project
    public function update(Request $request, $projectid)
    {
        $request->validate([
            'projectname' => 'required|string|max:200',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'deptid' => 'required|integer',
            'location_unitid' => 'required|integer'
        ]);

        $project = Project::findOrFail($projectid);

        $project->update([
            'projectname' => $request->projectname,
            'description' => $request->description,
            'status' => $request->status,
            'deptid' => $request->deptid,
            'location_unitid' => $request->location_unitid
        ]);

        return redirect()->route('projects.index')
                        ->with('success', 'Project updated successfully!');
    }

    // Delete project
    public function destroy($projectid)
    {
        $project = Project::findOrFail($projectid);
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted successfully!');
    }
}