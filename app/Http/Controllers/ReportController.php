<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX PAGE
    |--------------------------------------------------------------------------
    */
    private function baseProjectQuery(Request $request)
    {
        $query = DB::table('projects as p')
            ->leftJoin('departments as d', 'p.deptid', '=', 'd.deptid')
            ->leftJoin('administrative_units as au', 'p.location_unitid', '=', 'au.unitid')
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

        if (auth()->user()->roleid != 1) {
            $query->where('p.deptid', auth()->user()->deptid);
        }

        if ($request->filled('deptid')) {
            $query->where('p.deptid', $request->deptid);
        }

        if ($request->filled('status')) {
            $query->where('p.status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('p.projectname', 'LIKE', "%{$request->search}%")
                ->orWhere('p.description', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('p.createdat', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('p.createdat', '<=', $request->to_date);
        }

        return $query;
    }


    public function index()
    {
        $departments = Department::orderBy('deptname')->get();

        return view('reports.index', [
            'departments' => $departments,
            'reports' => collect()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PROJECT REPORT
    |--------------------------------------------------------------------------
    */
    public function projectReport(Request $request)
    {
        $query = $this->baseProjectQuery($request);

        /*
        |--------------------------------------------------------------------------
        | ROLE BASED ACCESS
        |--------------------------------------------------------------------------
        */
        if (auth()->user()->roleid != 1) {
            $query->where('p.deptid', auth()->user()->deptid);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('deptid')) {
            $query->where('p.deptid', $request->deptid);
        }

        if ($request->filled('status')) {
            $query->where('p.status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('p.projectname', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('p.description', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('p.createdat', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('p.createdat', '<=', $request->to_date);
        }

        /*
        |--------------------------------------------------------------------------
        | REPORT TYPE
        |--------------------------------------------------------------------------
        */
        $reportType = $request->report_type ?? 'master';

        if ($reportType == 'master') {

            $reports = $query->orderBy('p.createdat', 'DESC')->paginate(10)
                ->withQueryString();

        } elseif ($reportType == 'status') {

            $statusQuery = DB::table('projects')
                ->select('status', DB::raw('COUNT(*) as total_projects'));

            if (auth()->user()->roleid != 1) {
                $statusQuery->where('deptid', auth()->user()->deptid);
            }

            $reports = $statusQuery
                ->groupBy('status')
                ->orderBy('total_projects', 'DESC')
                ->get();

        } elseif ($reportType == 'department') {

            $departmentQuery = DB::table('departments as d')
                ->leftJoin('projects as p', 'd.deptid', '=', 'p.deptid')
                ->select(
                    'd.deptname as departmentname',
                    DB::raw('COUNT(p.projectid) as total_projects')
                );

            if (auth()->user()->roleid != 1) {
                $departmentQuery->where('p.deptid', auth()->user()->deptid);
            }

            $reports = $departmentQuery
                ->groupBy('d.deptname')
                ->orderBy('total_projects', 'DESC')
                ->get();

        } elseif ($reportType == 'gis') {

            $gisQuery = DB::table('projects as p')
                ->leftJoin('project_gis_data as g', 'p.projectid', '=', 'g.projectid')
                ->select(
                    'p.projectname',
                    DB::raw('COUNT(g.gisdataid) as total_layers')
                );

            if (auth()->user()->roleid != 1) {
                $gisQuery->where('p.deptid', auth()->user()->deptid);
            }

            $reports = $gisQuery
                ->groupBy('p.projectname')
                ->orderBy('total_layers', 'DESC')
                ->get();

        } elseif ($reportType == 'geometry') {

            $geometryQuery = DB::table('project_gis_data as g')
                ->leftJoin('projects as p', 'g.projectid', '=', 'p.projectid')
                ->select(
                    DB::raw('GeometryType(g.geometry) as geometry_type'),
                    DB::raw('COUNT(*) as total')
                );

            if (auth()->user()->roleid != 1) {
                $geometryQuery->where('p.deptid', auth()->user()->deptid);
            }

            $reports = $geometryQuery
                ->groupBy(DB::raw('GeometryType(g.geometry)'))
                ->orderBy('total', 'DESC')
                ->get();

        } else {
            $reports = collect();
        }

        $departments = Department::orderBy('deptname')->get();

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
        /*
        |--------------------------------------------------------------------------
        | BASE QUERY (SAME LOGIC AS LIST VIEW)
        |--------------------------------------------------------------------------
        */
        $query = DB::table('projects as p')
            ->leftJoin('departments as d', 'p.deptid', '=', 'd.deptid')
            ->leftJoin('administrative_units as au', 'p.location_unitid', '=', 'au.unitid')
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
        | ROLE BASED ACCESS
        |--------------------------------------------------------------------------
        */
        if (auth()->user()->roleid != 1) {
            $query->where('p.deptid', auth()->user()->deptid);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS (SAME AS REPORT PAGE)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('deptid')) {
            $query->where('p.deptid', $request->deptid);
        }

        if ($request->filled('status')) {
            $query->where('p.status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('p.projectname', 'LIKE', '%' . $request->search . '%')
                ->orWhere('p.description', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('p.createdat', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('p.createdat', '<=', $request->to_date);
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL DATA
        |--------------------------------------------------------------------------
        */
        $reports = $query
            ->orderBy('p.createdat', 'DESC')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */
        $pdf = Pdf::loadView(
            'reports.pdf.project-report-pdf',
            compact('reports')
        );

        return $pdf->download('project-report.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL (PLACEHOLDER)
    |--------------------------------------------------------------------------
    */
    public function exportExcel()
    {
        return back()->with('info', 'Excel export coming next');
    }
}