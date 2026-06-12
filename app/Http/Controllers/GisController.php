<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use ZipArchive;

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
     * LOAD GIS LAYER           [temporily not in use]
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

    // FUNCTION TO EXPORT GIS LAYERS IN TO KML FILE
    public function exportKml($projectid, $layername)
    {
        $features = DB::table('project_gis_data')
            ->selectRaw("
                gisdataid,
                attributes,
                ST_AsKML(geometry) AS kml
            ")
            ->where('projectid', $projectid)
            ->where('layername', $layername)
            ->get();

        if ($features->isEmpty()) {

            abort(404, 'Layer not found');
        }

        $kml = '<?xml version="1.0" encoding="UTF-8"?>';
        $kml .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
        $kml .= '<Document>';
        $kml .= '<name>' . htmlspecialchars($layername) . '</name>';

        foreach ($features as $feature) {

            $attrs = [];

            if (!empty($feature->attributes)) {

                $decoded = json_decode(
                    $feature->attributes,
                    true
                );

                if (is_array($decoded)) {

                    $attrs = $decoded;
                }
            }

            $name = $attrs['name'] ?? 'Feature';

            $kml .= '<Placemark>';

            $kml .= '<name>' .
                htmlspecialchars($name) .
                '</name>';

            $kml .= $feature->kml;

            $kml .= '</Placemark>';
        }

        $kml .= '</Document>';
        $kml .= '</kml>';

        return response(
            $kml,
            200,
            [
                'Content-Type' =>
                    'application/vnd.google-earth.kml+xml',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    preg_replace(
                        '/[^A-Za-z0-9_-]/',
                        '_',
                        $layername
                    ) .
                    '.kml"'
            ]
        );
    }

    // FUNCTION TO EXPORT GIS LAYERS TO SHP(.zip)
public function exportShapefile($projectid, $layername)
{
    $safeLayer = preg_replace(
        '/[^A-Za-z0-9_-]/',
        '_',
        $layername
    );

    $tempDir = storage_path(
        'app/temp_shp_' . uniqid()
    );

    File::makeDirectory(
        $tempDir,
        0755,
        true
    );

    // =====================================
    // FETCH FEATURES
    // =====================================

    $features = DB::table('project_gis_data')
        ->selectRaw("
            gisdataid,
            attributes,
            ST_AsGeoJSON(geometry) AS geometry
        ")
        ->where('projectid', $projectid)
        ->where('layername', $layername)
        ->get();

    if ($features->isEmpty()) {

        File::deleteDirectory($tempDir);

        abort(404, 'Layer not found');
    }

    // =====================================
    // BUILD GEOJSON
    // =====================================

    $geojson = [
        'type' => 'FeatureCollection',
        'features' => []
    ];

    foreach ($features as $feature) {

        $properties = [];

        if (!empty($feature->attributes)) {

            $decoded = json_decode(
                $feature->attributes,
                true
            );

            if (is_array($decoded)) {

                $properties = $decoded;
            }
        }

        $geojson['features'][] = [
            'type' => 'Feature',
            'properties' => $properties,
            'geometry' => json_decode(
                $feature->geometry,
                true
            )
        ];
    }

    $geojsonPath =
        $tempDir .
        DIRECTORY_SEPARATOR .
        $safeLayer .
        '.geojson';

    file_put_contents(
        $geojsonPath,
        json_encode(
            $geojson,
            JSON_PRETTY_PRINT
        )
    );

    // =====================================
    // GEOJSON -> SHAPEFILE
    // =====================================

    $ogr2ogr =
        '"C:\\Program Files\\PostgreSQL\\17\\bin\\ogr2ogr.exe"';

    $command =
        $ogr2ogr .
        ' -f "ESRI Shapefile" "' .
        $tempDir .
        '" "' .
        $geojsonPath .
        '" 2>&1';

    $output = [];
    $resultCode = 0;

    exec(
        $command,
        $output,
        $resultCode
    );

    if ($resultCode !== 0) {

        File::deleteDirectory($tempDir);

        dd([
            'command' => $command,
            'resultCode' => $resultCode,
            'output' => $output
        ]);
    }

    // =====================================
    // CREATE ZIP
    // =====================================

    $zipPath =
        storage_path(
            'app/' .
            $safeLayer .
            '_' .
            time() .
            '.zip'
        );

    $zip = new ZipArchive();

    if (
        $zip->open(
            $zipPath,
            ZipArchive::CREATE |
            ZipArchive::OVERWRITE
        ) !== true
    ) {

        File::deleteDirectory($tempDir);

        abort(
            500,
            'Unable to create ZIP'
        );
    }

    foreach (
        glob($tempDir . DIRECTORY_SEPARATOR . '*')
        as $file
    ) {

        $extension = strtolower(
            pathinfo(
                $file,
                PATHINFO_EXTENSION
            )
        );

        if (
            in_array(
                $extension,
                [
                    'shp',
                    'shx',
                    'dbf',
                    'prj',
                    'cpg'
                ]
            )
        ) {

            $zip->addFile(
                $file,
                basename($file)
            );
        }
    }

    $zip->close();

    File::deleteDirectory($tempDir);

    return response()
        ->download(
            $zipPath,
            $safeLayer . '.zip'
        )
        ->deleteFileAfterSend(true);
}
}