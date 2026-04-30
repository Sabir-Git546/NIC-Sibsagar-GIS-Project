<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdministrativeUnit;

class DistBoundController extends Controller
{
    /**
     * ===============================
     * SHOW ALL BOUNDARIES (TABLE VIEW)
     * ===============================
     */
    public function index()
    {
        $units = AdministrativeUnit::orderBy('unitid', 'asc')->get();
        return view('dist-bound.index', compact('units'));
    }

    /**
     * ===============================
     * SHOW ADD FORM
     * ===============================
     */
    public function create()
    {
        $units = AdministrativeUnit::all(); // for parent dropdown
        return view('dist-bound.create', compact('units'));
    }

    /**
     * ===============================
     * STORE NEW BOUNDARY
     * ===============================
     */
    public function store(Request $request)
    {
        $request->validate([
            'unitname' => 'required|string|max:200',
            'unittype' => 'required|string|max:100',
            'parent_unitid' => 'nullable|integer',
            'geometry' => 'required|file|mimes:geojson,json,zip'
        ]);

        $file = $request->file('geometry');
        $extension = $file->getClientOriginalExtension();

        // Handle GeoJSON / JSON
        if (in_array($extension, ['geojson', 'json'])) {
            $geometryData = file_get_contents($file);
        } else {
            // Store ZIP file path (for shapefile)
            $geometryData = $file->store('uploads/shapefiles');
        }

        AdministrativeUnit::create([
            'unitname' => $request->unitname,
            'unittype' => $request->unittype,
            'parent_unitid' => $request->parent_unitid,
            'geometry' => $geometryData
        ]);

        return redirect()->route('dist-bound.index')
                         ->with('success', 'Boundary added successfully!');
    }

    /**
     * ===============================
     * SHOW EDIT FORM
     * ===============================
     */
    public function edit($unitid)
    {
        $unit = AdministrativeUnit::findOrFail($unitid);
        $units = AdministrativeUnit::where('unitid', '!=', $unitid)->get();

        return view('dist-bound.edit', compact('unit', 'units'));
    }

    /**
     * ===============================
     * UPDATE BOUNDARY
     * ===============================
     */
    public function update(Request $request, $unitid)
    {
        $request->validate([
            'unitname' => 'required|string|max:200',
            'unittype' => 'required|string|max:100',
            'parent_unitid' => 'nullable|integer',
            'geometry' => 'nullable|file|mimes:geojson,json,zip'
        ]);

        $unit = AdministrativeUnit::findOrFail($unitid);

        $data = [
            'unitname' => $request->unitname,
            'unittype' => $request->unittype,
            'parent_unitid' => $request->parent_unitid,
        ];

        // If new file uploaded
        if ($request->hasFile('geometry')) {
            $file = $request->file('geometry');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['geojson', 'json'])) {
                $data['geometry'] = file_get_contents($file);
            } else {
                $data['geometry'] = $file->store('uploads/shapefiles');
            }
        }

        $unit->update($data);

        return redirect()->route('dist-bound.index')
                         ->with('success', 'Boundary updated successfully!');
    }

    /**
     * ===============================
     * DELETE BOUNDARY
     * ===============================
     */
    public function destroy($unitid)
    {
        $unit = AdministrativeUnit::findOrFail($unitid);
        $unit->delete();

        return redirect()->route('dist-bound.index')
                         ->with('success', 'Boundary deleted successfully!');
    }
}