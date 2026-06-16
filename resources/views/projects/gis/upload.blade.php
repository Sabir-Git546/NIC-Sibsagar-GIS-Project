@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

@auth

<div class="dashboard-container">

    {{-- LEFT SIDEBAR --}}
    @include('layouts.left-nav')

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        {{-- PAGE HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h3>
                Upload GIS File :
                {{ $project->projectname }}
            </h3>

            <a href="{{ route('gis.view', $project->projectid) }}"
               class="btn btn-warning">
                Back
            </a>

        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        {{-- ERROR MESSAGE --}}
        @if(session('error'))

            <div class="alert alert-danger">
                {{ session('error') }}
            </div>

        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        {{-- UPLOAD CARD --}}
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                GIS Upload Form
            </div>

            <div class="card-body">

                <form id="shpUploadForm">

                    @csrf

                    {{-- LAYER NAME --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Layer Name
                        </label>

                        <input type="text"
                                id="geojsonName"
                                class="form-control"
                                placeholder="Enter GIS layer name"
                                required>

                    </div>

                    {{-- FILE --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Shapefile ZIP
                        </label>

                        <input type="file"
                            id="zipFile"
                            class="form-control"
                            accept=".zip"
                            required>

                        <small class="text-muted">
                            Upload ZIP containing .shp, .dbf and .shx files
                        </small>

                    </div>

                    {{-- SUBMIT --}}
                    <button type="button"
                            class="btn btn-success"
                            onclick="GISTools.convertShp()">

                        Convert & Upload

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
    window.projectId = {{ $project->projectid }};
</script>

<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>

<script src="{{ asset('js/gis/tools.js') }}"></script>

@endauth

@endsection