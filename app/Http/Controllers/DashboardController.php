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
        // =========================
        // TOTAL COUNTS
        // =========================
        $totalDepartments = Department::count();

        $totalUsers = UserModel::count();

        $totalProjects = Project::count();


        // =========================
        // CURRENT USER
        // =========================
        $user = Auth::user();


        // =========================
        // RECENT ACTIVITY
        // =========================

        if ($user && $user->roleid == 1) {

            // ADMIN → SEE ALL ACTIVITIES
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

                ->orderBy(
                    'approval_requests.created_at',
                    'desc'
                )

                ->limit(5)

                ->get();

        } else {

            // USER → ONLY OWN ACTIVITIES
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

                ->orderBy(
                    'approval_requests.created_at',
                    'desc'
                )

                ->limit(5)

                ->get();
        }


        // =========================
        // DEPARTMENT PROJECT STATS
        // =========================
        $departmentStats = DB::table('projects')

            ->join(
                'departments',
                'projects.deptid',
                '=',
                'departments.deptid'
            )

            ->select(
                'departments.deptname',
                DB::raw(
                    'COUNT(projects.projectid) as total'
                )
            )

            ->groupBy(
                'departments.deptname'
            )

            ->get();


        // =========================
        // PROJECT STATUS STATS
        // =========================
        $statusStats = DB::table('projects')

            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )

            ->groupBy('status')

            ->get();


        // =========================
        // RETURN DASHBOARD VIEW
        // =========================
        return view('dashboard', compact(

            'totalDepartments',

            'totalUsers',

            'totalProjects',

            'recentActivity',

            'departmentStats',

            'statusStats'
        ));
    }


    // =========================
    // DEPARTMENT PROJECT LIST
    // =========================
    public function departmentProjects($deptname)
    {
        $projects = DB::table('projects')

            ->join(
                'departments',
                'projects.deptid',
                '=',
                'departments.deptid'
            )

            ->where(
                'departments.deptname',
                $deptname
            )

            ->select(
                'projects.projectid',
                'projects.projectname',
                'projects.status',
                'departments.deptname'
            )

            ->paginate(5);


        return view(
            'partials.department-projects',
            compact(
                'projects',
                'deptname'
            )
        );
    }

    // =========================
    // STATUS PROJECT LIST
    // =========================
    public function statusProjects($status)
    {
        $projects = DB::table('projects')

            ->join(
                'departments',
                'projects.deptid',
                '=',
                'departments.deptid'
            )

            ->where(
                'projects.status',
                $status
            )

            ->select(
                'projects.projectid',
                'projects.projectname',
                'projects.status',
                'departments.deptname'
            )

            ->paginate(5);

        return view(
            'partials.status-projects',
            compact(
                'projects',
                'status'
            )
        );
    }
}