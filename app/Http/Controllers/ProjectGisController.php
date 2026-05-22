<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\ProjectGisData;

use App\Services\AuditService;

class ProjectGisController extends Controller
{
    // =========================
    // VIEW GIS DATA
    // =========================
    public function view($projectid)
    {
        // PROJECT INFO
        $project = DB::table('projects')
            ->where('projectid', $projectid)
            ->first();

        // GIS LAYERS
        $gisdata = DB::table('project_gis_data')

            ->where('projectid', $projectid)
            ->orderBy('gisdataid', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view(
            'projects.gis.view',
            compact('project', 'gisdata')
        );
    }

    // =========================
    // GIS UPLOAD FORM
    // =========================
    public function uploadForm($projectid)
    {
        // PROJECT
        $project = DB::table('projects')
            ->where('projectid', $projectid)
            ->first();

        // GIS DATA
        $gisdata = DB::table('project_gis_data')
            ->where('projectid', $projectid)
            ->get();

        return view(
            'projects.gis.upload',
            compact(
                'project',
                'gisdata'
            )
        );
    }

    // =========================
    // STORE GIS DATA
    // =========================
    public function store(
        Request $request,
        $projectid
    ) {

        // VALIDATION
        $request->validate([

            'layername' =>
                'required|string|max:200',

            'gisfile' =>
                'required|file|mimes:json,geojson'

        ]);

        // READ FILE
        $file = $request->file('gisfile');

        $geojsonContent = file_get_contents(
            $file->getRealPath()
        );

        // CONVERT JSON
        $geojson = json_decode(
            $geojsonContent,
            true
        );

        // INVALID GEOJSON
        if (
            !$geojson ||
            !isset($geojson['features'])
        ) {
            return back()->with(
                'error',
                'Invalid GeoJSON file.'
            );
        }

        // =========================
        // ADMIN DIRECT INSERT
        // =========================
        if (auth()->user()->roleid == 1) {

            // INSERT FEATURES
            foreach ($geojson['features'] as $feature) {

                // GEOMETRY
                $geometry = json_encode(
                    $feature['geometry']
                );

                // ATTRIBUTES
                $attributes = isset($feature['properties'])
                    ? $feature['properties']
                    : [];

                // INSERT INTO POSTGIS
                DB::insert(

                    "
                    INSERT INTO project_gis_data
                    (
                        projectid,
                        layername,
                        geometry,
                        attributes
                    )

                    VALUES
                    (
                        ?,
                        ?,
                        ST_SetSRID(
                            ST_GeomFromGeoJSON(?),
                            4326
                        ),
                        ?
                    )
                    ",

                    [
                        $projectid,

                        sanitize_input(
                            $request->layername
                        ),

                        $geometry,

                        json_encode($attributes)
                    ]
                );
            }

            // AUDIT LOG
            AuditService::log(

                'CREATE',

                'GIS',

                'GIS layer uploaded directly by admin: ' .
                $request->layername,

                null,

                [
                    'projectid' => $projectid,
                    'layername' => $request->layername
                ]

            );

            return redirect()
                ->route('gis.view', $projectid)
                ->with(
                    'success',
                    'GIS data uploaded successfully!'
                );
        }

        // =========================
        // USER APPROVAL FLOW
        // =========================

        // CHECK DUPLICATE PENDING REQUEST
        $pending = DB::table('approval_requests')

            ->where('module', 'GIS')

            ->where('recordid', $projectid)

            ->where('layername', $request->layername)

            ->where('status', 'pending')

            ->exists();

        if ($pending) {

            return back()->with(
                'error',
                'A pending GIS request already exists.'
            );
        }

        // STORE REQUEST ONLY
        DB::table('approval_requests')->insert([

            'userid' =>
                auth()->user()->userid,

            'module' =>
                'GIS',

            'action' =>
                'upload_request',

            'recordid' =>
                $projectid,

            'old_data' =>
                null,

            'new_data' =>
                json_encode([
                    'projectid' => $projectid,
                    'layername' => $request->layername,
                    'geojson' => $geojson
                ]),

            'status' =>
                'pending',

            'layername' =>
                $request->layername,

            'created_at' =>
                now()

        ]);

        // AUDIT LOG
        AuditService::log(

            'CREATE_REQUEST',

            'GIS',

            'GIS upload requested: ' .
            $request->layername,

            null,

            [
                'projectid' => $projectid,
                'layername' => $request->layername
            ]

        );

        return redirect()

            ->route('gis.view', $projectid)

            ->with(
                'success',
                'GIS upload request sent for approval'
            );
    }

    // =========================
    // DELETE GIS LAYER
    // =========================
    public function deleteLayer(
        $projectid,
        $layername
    ) {

        // EXISTING GIS DATA
        $gisData = DB::table('project_gis_data')

            ->where('projectid', $projectid)

            ->where('layername', $layername)

            ->get();

        // =========================
        // ADMIN DIRECT DELETE
        // =========================
        if (auth()->user()->roleid == 1) {

            DB::table('project_gis_data')

                ->where('projectid', $projectid)

                ->where('layername', $layername)

                ->delete();

            // AUDIT LOG
            AuditService::log(

                'DELETE',

                'GIS',

                'GIS deleted directly by admin: ' .
                $layername,

                $gisData->toArray(),

                null

            );

            return redirect()

                ->back()

                ->with(
                    'success',
                    'GIS layer deleted successfully'
                );
        }

        // =========================
        // USER APPROVAL FLOW
        // =========================

        // CHECK DUPLICATE PENDING REQUEST
        $pending = DB::table('approval_requests')

            ->where('module', 'GIS')

            ->where('recordid', $projectid)

            ->where('layername', $layername)

            ->where('status', 'pending')

            ->exists();

        if ($pending) {

            return back()->with(
                'error',
                'A pending GIS delete request already exists.'
            );
        }

        // STORE APPROVAL REQUEST
        DB::table('approval_requests')->insert([

            'userid' =>
                auth()->user()->userid,

            'module' =>
                'GIS',

            'action' =>
                'delete_request',

            'recordid' =>
                $projectid,

            'old_data' =>
                json_encode($gisData->toArray()),

            'new_data' =>
                null,

            'status' =>
                'pending',

            'layername' =>
                $layername,

            'created_at' =>
                now()

        ]);

        // AUDIT LOG
        AuditService::log(

            'DELETE_REQUEST',

            'GIS',

            'GIS delete requested: ' .
            $layername,

            $gisData->toArray(),

            null

        );

        return redirect()

            ->back()

            ->with(
                'success',
                'GIS delete request sent to admin'
            );
    }
}