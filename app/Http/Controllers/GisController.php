<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Services\AuditService;

class GisController extends Controller
{
    /**
     * =========================
     * GOOGLE MAP VIEW
     * =========================
     */
    public function googleMap()
    {
        // GEOJSON DIRECTORY
        $geojsonPath = public_path('geojson');


        // CHECK DIRECTORY
        if (File::exists($geojsonPath)) {

            $files = File::files($geojsonPath);


            // ONLY FILENAMES
            $files = array_map(

                fn($file) => $file->getFilename(),

                $files

            );

        } else {

            $files = [];
        }


        return view(
            'GIS.googleMap',
            compact('files')
        );
    }


    /**
     * =========================
     * GIS APPLICATION
     * =========================
     */
    public function gisApp()
    {
        // GIS LAYERS
        $layers = DB::table('project_gis_data')

            ->join(
                'projects',
                'project_gis_data.projectid',
                '=',
                'projects.projectid'
            )

            ->join(
                'departments',
                'projects.deptid',
                '=',
                'departments.deptid'
            )

            ->select(

                'project_gis_data.layername',

                'project_gis_data.projectid',

                'departments.deptid',

                'departments.deptname'

            )

            ->distinct()

            ->orderBy('project_gis_data.layername')

            ->get();


        // DEPARTMENT FILTER
        $departments = DB::table('departments')

            ->select(
                'deptid',
                'deptname'
            )

            ->orderBy('deptname')

            ->get();

        // Projects details
        $projects = DB::table('projects')
            ->select('projectid', 'projectname')
            ->get();


        return view(
            'GIS.gisApp',
            compact(
                'layers',
                'departments',
                'projects'
            )
        );
    }


    /**
     * =========================
     * SAVE GEOJSON FILE
     * =========================
     */
    public function saveGeojson(Request $request)
    {
        // VALIDATION
        $request->validate([

            'filename' =>
                'required|string|max:255',

            'geojson' =>
                'required|array'

        ]);


        // SANITIZE FILENAME
        $filename = sanitize_input(
            $request->filename
        );


        // FORCE GEOJSON EXTENSION
        if (
            !str_ends_with(
                strtolower($filename),
                '.geojson'
            )
        ) {
            $filename .= '.geojson';
        }


        $geojson = $request->geojson;


        try {

            // TARGET FOLDER
            $folder = public_path('geojson');


            // CREATE IF NOT EXISTS
            if (!file_exists($folder)) {

                mkdir(
                    $folder,
                    0777,
                    true
                );
            }


            // FULL FILE PATH
            $path = $folder .
                DIRECTORY_SEPARATOR .
                $filename;


            // SAVE FILE
            file_put_contents(
                $path,
                json_encode(
                    $geojson,
                    JSON_PRETTY_PRINT
                )
            );


            // AUDIT LOG
            AuditService::log(

                'CREATE',

                'GIS',

                'GeoJSON file saved: ' .
                $filename,

                null,

                [
                    'filename' => $filename
                ]

            );


            return response()->json([

                'success' => true,

                'path' => asset(
                    'geojson/' . $filename
                )

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }


    /**
     * =========================
     * LOAD GIS LAYER
     * =========================
     */
    public function getLayer($layername)
    {
        // FETCH FEATURES
        $features = DB::select(

            "
            SELECT

                projectid,

                ST_AsGeoJSON(geometry) AS geometry,

                attributes

            FROM project_gis_data

            WHERE layername = ?
            ",

            [$layername]

        );


        // GEOJSON STRUCTURE
        $geojson = [

            "type" => "FeatureCollection",

            "features" => []

        ];


        // BUILD FEATURES
        foreach ($features as $feature) {

            $properties = json_decode(
                $feature->attributes,
                true
            ) ?? [];

            // INCLUDE LAYERNAME
            $properties['layername'] =
                $layername;

            // INCLUDE PROJECT ID
            $properties['projectid'] =
                $feature->projectid;


            $geojson['features'][] = [

                "type" => "Feature",

                "geometry" => json_decode(
                    $feature->geometry
                ),

                "properties" => $properties

            ];
        }


        return response()->json($geojson);
    }
}