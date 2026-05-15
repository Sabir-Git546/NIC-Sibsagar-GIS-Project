<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Department;

class ReportController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | INDEX PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {

        $departments = Department::orderBy('deptname')
            ->get();

        return view('reports.index', [

            'departments' => $departments,

            'reports' => []

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROJECT REPORT
    |--------------------------------------------------------------------------
    */
    public function projectReport(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */
        $query = DB::table('projects as p')

            ->leftJoin(
                'departments as d',
                'p.deptid',
                '=',
                'd.deptid'
            )

            ->leftJoin(
                'administrative_units as au',
                'p.location_unitid',
                '=',
                'au.unitid'
            )

            ->select(

                'p.projectid',
                'p.projectname',
                'p.description',
                'p.status',
                'p.createdby',
                'p.createdat',

                'd.deptname as departmentname',

                'au.unitname'

            );


        /*
        |--------------------------------------------------------------------------
        | ROLE BASED SECURITY
        |--------------------------------------------------------------------------
        |
        | Admin (roleid=1) -> All Projects
        | Others -> Only Their Department Projects
        |
        */
        if (auth()->user()->roleid != 1) {

            $query->where(
                'p.deptid',
                auth()->user()->deptid
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER: DEPARTMENT
        |--------------------------------------------------------------------------
        */
        if ($request->filled('deptid')) {

            $query->where(
                'p.deptid',
                $request->deptid
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER: STATUS
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {

            $query->where(
                'p.status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER: SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'p.projectname',
                    'ILIKE',
                    '%' . $request->search . '%'
                )

                ->orWhere(
                    'p.description',
                    'ILIKE',
                    '%' . $request->search . '%'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER: FROM DATE
        |--------------------------------------------------------------------------
        */
        if ($request->filled('from_date')) {

            $query->whereDate(
                'p.createdat',
                '>=',
                $request->from_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER: TO DATE
        |--------------------------------------------------------------------------
        */
        if ($request->filled('to_date')) {

            $query->whereDate(
                'p.createdat',
                '<=',
                $request->to_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REPORT TYPE
        |--------------------------------------------------------------------------
        */
        $reportType = $request->report_type ?? 'master';


        /*
        |--------------------------------------------------------------------------
        | MASTER PROJECT REPORT
        |--------------------------------------------------------------------------
        */
        if ($reportType == 'master') {

            $reports = $query
                ->orderBy('p.createdat', 'DESC')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS SUMMARY REPORT
        |--------------------------------------------------------------------------
        */
        elseif ($reportType == 'status') {

            $statusQuery = DB::table('projects')

                ->select(
                    'status',
                    DB::raw('COUNT(*) as total_projects')
                );

            /*
            |--------------------------------------------------------------------------
            | SECURITY FILTER
            |--------------------------------------------------------------------------
            */
            if (auth()->user()->roleid != 1) {

                $statusQuery->where(
                    'deptid',
                    auth()->user()->deptid
                );
            }

            $reports = $statusQuery

                ->groupBy('status')

                ->orderBy('total_projects', 'DESC')

                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT WISE REPORT
        |--------------------------------------------------------------------------
        */
        elseif ($reportType == 'department') {

            $departmentQuery = DB::table('departments as d')

                ->leftJoin(
                    'projects as p',
                    'd.deptid',
                    '=',
                    'p.deptid'
                )

                ->select(

                    'd.deptname as departmentname',

                    DB::raw('COUNT(p.projectid) as total_projects')

                );

            /*
            |--------------------------------------------------------------------------
            | SECURITY FILTER
            |--------------------------------------------------------------------------
            */
            if (auth()->user()->roleid != 1) {

                $departmentQuery->where(
                    'p.deptid',
                    auth()->user()->deptid
                );
            }

            $reports = $departmentQuery

                ->groupBy('d.deptname')

                ->orderBy('total_projects', 'DESC')

                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | GIS LAYER SUMMARY REPORT
        |--------------------------------------------------------------------------
        */
        elseif ($reportType == 'gis') {

            $gisQuery = DB::table('projects as p')

                ->leftJoin(
                    'project_gis_data as g',
                    'p.projectid',
                    '=',
                    'g.projectid'
                )

                ->select(

                    'p.projectname',

                    DB::raw('COUNT(g.gisdataid) as total_layers')

                );

            /*
            |--------------------------------------------------------------------------
            | SECURITY FILTER
            |--------------------------------------------------------------------------
            */
            if (auth()->user()->roleid != 1) {

                $gisQuery->where(
                    'p.deptid',
                    auth()->user()->deptid
                );
            }

            $reports = $gisQuery

                ->groupBy('p.projectname')

                ->orderBy('total_layers', 'DESC')

                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | GEOMETRY TYPE SUMMARY REPORT
        |--------------------------------------------------------------------------
        */
        elseif ($reportType == 'geometry') {

            $geometryQuery = DB::table('project_gis_data as g')

                ->leftJoin(
                    'projects as p',
                    'g.projectid',
                    '=',
                    'p.projectid'
                )

                ->select(

                    DB::raw('GeometryType(g.geometry) as geometry_type'),

                    DB::raw('COUNT(*) as total')

                );

            /*
            |--------------------------------------------------------------------------
            | SECURITY FILTER
            |--------------------------------------------------------------------------
            */
            if (auth()->user()->roleid != 1) {

                $geometryQuery->where(
                    'p.deptid',
                    auth()->user()->deptid
                );
            }

            $reports = $geometryQuery

                ->groupBy(
                    DB::raw('GeometryType(g.geometry)')
                )

                ->orderBy('total', 'DESC')

                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */
        else {

            $reports = [];
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DEPARTMENTS
        |--------------------------------------------------------------------------
        */
        $departments = Department::orderBy('deptname')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('reports.index', [

            'departments' => $departments,

            'reports' => $reports,

            'reportType' => $reportType

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf(Request $request)
    {

        return back()->with(

            'info',

            'PDF export module coming next'

        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */
    public function exportExcel(Request $request)
    {

        return back()->with(

            'info',

            'Excel export module coming next'

        );
    }

}