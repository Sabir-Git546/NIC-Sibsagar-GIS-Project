<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\UserModel;
use App\Models\Project;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // ✅ Basic Counts
        $totalDepartments = Department::count();
        $totalUsers = UserModel::count();
        $totalProjects = Project::count();

        // ✅ RECENT ACTIVITY (READABLE)
        if (session('roleid') == 1) {

            // 🔴 ADMIN → see all requests
            $recentActivity = DB::table('approval_requests')
                ->leftJoin('users', 'users.userid', '=', 'approval_requests.userid')
                ->leftJoin('projects', 'projects.projectid', '=', 'approval_requests.recordid') // ✅ FIXED
                ->select(
                    'approval_requests.*',
                    'users.username',
                    'projects.projectname'
                )
                ->orderBy('approval_requests.created_at', 'desc')
                ->limit(5)
                ->get();
        } else {

            // 🟢 USER → see only own requests
            $recentActivity = DB::table('approval_requests')
                ->leftJoin('users', 'users.userid', '=', 'approval_requests.userid')
                ->leftJoin('projects', 'projects.projectid', '=', 'approval_requests.recordid')
                ->where('approval_requests.userid', session('userid'))
                ->select(
                    'approval_requests.*',
                    'users.username',
                    'projects.projectname'
                )
                ->orderBy('approval_requests.created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // ✅ RETURN VIEW
        return view('dashboard', compact(
            'totalDepartments',
            'totalUsers',
            'totalProjects',
            'recentActivity'
        ));
    }
}