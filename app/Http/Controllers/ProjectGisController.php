<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectGisData;

class ProjectGisController extends Controller
{
    // View GIS data of a specific project
    public function view($projectid)
    {
        // Get project info
        $project = DB::table('projects')
                    ->where('projectid', $projectid)
                    ->first();

        // Get GIS data for the project
        $gisdata = DB::table('project_gis_data')
                    ->where('projectid', $projectid)
                    ->get();   // use get() because multiple GIS layers may exist

        return view('projects.gis.view', compact('project','gisdata'));
    }

    // Show GIS upload form
    public function uploadForm($projectid)
    {
        $project = DB::table('projects')
                    ->where('projectid', $projectid)
                    ->first();

        return view('projects.gis.upload', compact('project'));
    }

    // stores geometry and attributes in post-gis
    public function store(Request $request, $projectid)
    {
        // Validate input
        $request->validate([
            'layername' => 'required|string|max:200',
            'gisfile'   => 'required|file|mimes:json,geojson'
        ]);

        // Read uploaded file
        $file = $request->file('gisfile');
        $geojsonContent = file_get_contents($file->getRealPath());

        // Convert JSON to PHP array
        $geojson = json_decode($geojsonContent, true);

        // Check valid GeoJSON
        if (!isset($geojson['features'])) {
            return back()->with('error', 'Invalid GeoJSON file.');
        }

        // Loop through each feature
        foreach ($geojson['features'] as $feature) {

            // Extract geometry
            $geometry = json_encode($feature['geometry']);

            // Extract attributes (properties)
            $attributes = isset($feature['properties']) 
                            ? json_encode($feature['properties']) 
                            : json_encode([]);

            // Insert into PostGIS table
            \DB::insert("
                INSERT INTO project_gis_data 
                (projectid, layername, geometry, attributes)
                VALUES 
                (?, ?, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), ?)
            ", [
                $projectid,
                $request->layername,
                $geometry,
                $attributes
            ]);
        }

        return redirect()
            ->route('gis.view', $projectid)
            ->with('success', 'GIS data uploaded successfully!');
    }

    public function deleteLayer($projectid, $layername)
    {
        // Get existing data (for audit + approval)
        $gisData = DB::table('project_gis_data')
            ->where('projectid', $projectid)
            ->where('layername', $layername)
            ->get();

        // Store request instead of deleting
        DB::table('approval_requests')->insert([
            'userid'    => session('userid'),
            'module'    => 'gis',
            'action'    => 'delete',
            'recordid'  => $projectid,
            'old_data'  => json_encode($gisData),
            'new_data'  => null,
            'status'    => 'pending'
        ]);

        return redirect()->back()
            ->with('success', 'GIS delete request sent to admin');
    }

}