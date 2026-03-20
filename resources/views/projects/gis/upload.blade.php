@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

@include('layouts.left-nav')

<div class="main-content">

    <!-- Page Title -->
    <div class="mb-4">
        <h3>Upload Project GIS Data</h3>
        <p class="text-muted fs-4">
            Project ID : <strong>{{ $project->projectid }}</strong><br>
            Project Name : <strong>{{ $project->projectname }}</strong>
        </p>
    </div>

    <!-- Upload Form -->
    <div class="card">
        <div class="card-header bg-success text-white">
            Upload GIS Layer
        </div>

        <div class="card-body">

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('gis.upload.store', $project->projectid) }}" 
                  method="POST" 
                  enctype="multipart/form-data">

                @csrf

                <!-- Project ID -->
               <!-- <div class="mb-3">
                    <label class="form-label">Project ID</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $project->projectid }}"
                           readonly>
                </div> -->

                <!-- Layer Name -->
                <div class="mb-3">
                    <label class="form-label">Layer Name</label>
                    <input type="text"
                           name="layername"
                           class="form-control"
                           placeholder="Enter GIS Layer Name"
                           required>
                </div>

                <!-- GIS File Upload -->
                <div class="mb-3">
                    <label class="form-label">Upload GIS File</label>
                    <input type="file"
                           name="gisfile"
                           class="form-control"
                           accept=".geojson,.json,.zip"
                           required>

                    <small class="text-muted">
                        Supported formats: GeoJSON, JSON, Zipped Shapefile
                    </small>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">

                    <a href="{{ route('gis.view', $project->projectid) }}"
                       class="btn btn-secondary">
                        Back
                    </a>

                    <button type="submit"
                            class="btn btn-success">
                        Upload GIS
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

</div>

@endsection