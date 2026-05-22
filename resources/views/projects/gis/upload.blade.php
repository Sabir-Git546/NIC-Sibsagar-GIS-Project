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

                <form action="{{ route('gis.upload.store', $project->projectid) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf

                    {{-- LAYER NAME --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Layer Name
                        </label>

                        <input type="text"
                               name="layername"
                               class="form-control"
                               placeholder="Enter GIS layer name"
                               required>

                    </div>

                    {{-- FILE --}}
                    <div class="mb-3">

                        <label class="form-label">
                            GeoJSON File
                        </label>

                        <input type="file"
                               name="gisfile"
                               class="form-control"
                               accept=".json,.geojson"
                               required>

                        <small class="text-muted">
                            Supported formats:
                            .json, .geojson
                        </small>

                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit"
                            class="btn btn-success">

                        Upload GIS File

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endauth

@endsection