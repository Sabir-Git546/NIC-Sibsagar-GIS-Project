<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GisController extends Controller
{
    /**
     * Show Google Map page with selectable GeoJSON layers
     *
     * @return \Illuminate\View\View
     */
    public function googleMap()
    {
        // Path to the folder containing GeoJSON files
        $geojsonPath = public_path('geojson');

        // Get all GeoJSON files
        if (File::exists($geojsonPath)) {
            $files = File::files($geojsonPath);

            // Only get filenames, not full paths
            $files = array_map(fn($file) => $file->getFilename(), $files);
        } else {
            $files = []; // If folder doesn't exist, return empty array
        }

        // Return the view with the list of files
        return view('GIS.googleMap', compact('files'));
    }


    public function gisApp()
    {
        // GIS layers with department info
        $layers = DB::table('project_gis_data')
            ->join('projects', 'project_gis_data.projectid', '=', 'projects.projectid')
            ->join('departments', 'projects.deptid', '=', 'departments.deptid')
            ->select(
                'project_gis_data.layername',
                'departments.deptid',
                'departments.deptname'
            )
            ->distinct()
            ->orderBy('project_gis_data.layername')
            ->get();

        // Department list for filter
        $departments = DB::table('departments')
            ->select('deptid','deptname')
            ->orderBy('deptname')
            ->get();

        return view('GIS.gisApp', compact('layers','departments'));
    }

    public function saveGeojson(Request $request)
    {
        $filename = $request->filename;
        $geojson = $request->geojson;

        try {
            // Target folder: public/geojson
            $folder = public_path('geojson'); // D:\SIS MCA 4th Prj\sis_app_nic_sibsagar\public\geojson

            // Create folder if it doesn't exist
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Full path to save the file
            $path = $folder . DIRECTORY_SEPARATOR . $filename;

            file_put_contents($path, json_encode($geojson));

            return response()->json([
                'success' => true,
                'path' => asset('geojson/' . $filename) // URL for browser access
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getLayer($layername)
    {
        $features = DB::select("
            SELECT 
                ST_AsGeoJSON(geometry) as geometry,
                attributes
            FROM project_gis_data
            WHERE layername = ?
        ", [$layername]);

        $geojson = [
            "type" => "FeatureCollection",
            "features" => []
        ];

        foreach ($features as $f) {

            $properties = json_decode($f->attributes, true);

            // Add layername so JS can remove it later
            $properties['layername'] = $layername;

            $geojson["features"][] = [
                "type" => "Feature",
                "geometry" => json_decode($f->geometry),
                "properties" => $properties
            ];
        }

        return response()->json($geojson);
    }

}