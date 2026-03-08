@extends('layouts.master')

@section('content')

<div class="container mt-4">

    <h4>Upload GIS File for: {{ $project->projectname }}</h4>

    <form action="{{ route('projects.gis.store', $project->projectid) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="mb-3">
            <label class="form-label">GIS File (GeoJSON / Shapefile ZIP)</label>
            <input type="file" name="gis_file" class="form-control" required>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">
                Upload
            </button>

            <a href="{{ route('projects.view', $project->projectid) }}"
               class="btn btn-secondary">
               Cancel
            </a>
        </div>

    </form>

</div>

@endsection