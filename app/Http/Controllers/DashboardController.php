<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Models\Project;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalDepartments = Department::count();
        $totalUsers = User::count();
        $activeProjects = Project::where('status', 'active')->count();

        return view('dashboard', compact(
            'totalDepartments',
            'totalUsers',
            'activeProjects'
        ));
    }
}