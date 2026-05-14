<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Department;
use App\Models\UserModel;
use App\Models\Project;

class DashboardController extends Controller
{
    // =========================
    // DASHBOARD
    // =========================
    public function dashboard()
    {
        // COUNTS
        $totalDepartments = Department::count();

        $totalUsers = UserModel::count();

        $totalProjects = Project::count();

        // CURRENT USER
        $user = Auth::user();

        // =========================
        // RECENT ACTIVITY
        // =========================

        if ($user && $user->roleid == 1) {

            // ADMIN → SEE ALL
            $recentActivity = DB::table('approval_requests')

                ->leftJoin(
                    'users',
                    'users.userid',
                    '=',
                    'approval_requests.userid'
                )

                ->leftJoin(
                    'projects',
                    'projects.projectid',
                    '=',
                    'approval_requests.recordid'
                )

                ->select(
                    'approval_requests.*',
                    'users.username',
                    'projects.projectname'
                )

                ->orderBy('approval_requests.created_at', 'desc')

                ->limit(5)

                ->get();

        } else {

            // USER → ONLY OWN
            $recentActivity = DB::table('approval_requests')

                ->leftJoin(
                    'users',
                    'users.userid',
                    '=',
                    'approval_requests.userid'
                )

                ->leftJoin(
                    'projects',
                    'projects.projectid',
                    '=',
                    'approval_requests.recordid'
                )

                ->where(
                    'approval_requests.userid',
                    $user->userid
                )

                ->select(
                    'approval_requests.*',
                    'users.username',
                    'projects.projectname'
                )

                ->orderBy('approval_requests.created_at', 'desc')

                ->limit(5)

                ->get();
        }

        return view('dashboard', compact(

            'totalDepartments',

            'totalUsers',

            'totalProjects',

            'recentActivity'

        ));
    }
}