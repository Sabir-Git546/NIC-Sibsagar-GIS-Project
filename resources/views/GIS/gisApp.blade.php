@php
$hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/gisApp.css') }}">

@endsection

@section('content')

@if(!session()->has('userid'))
<script>
    window.location = "{{ route('login') }}";
</script>
@endif

<div class="dashboard-wrapper">

    <!-- Floating Open Button -->
    <button class="open-btn" id="openPanelBtn" onclick="openPanel()">☰</button>

    <div class="dashboard-container">

        <!-- SIDEBAR -->
        <div class="side-control-panel" id="controlPanel">

            <div class="panel-content">

                <div class="panel-title">
                    <span>GIS Control Panel</span>
                    <button class="close-btn" onclick="closePanel()">✖</button>
                </div>

                <!-- LAYER FILE SELECTOR --> 
                <div class="control-card"> <label class="mb-2">Layer File Selector</label> 
                    <!-- Filter --> 
                    <label class="mb-1">Department</label>
                    <select id="deptFilter" class="form-control mb-2" onchange="filterLayers()">
                        <option value="">All Departments</option>

                        @foreach($departments as $dept)
                            <option value="{{ $dept->deptid }}">
                                {{ $dept->deptname }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Search -->
                    <input type="text" id="layerSearch" class="form-control mb-2" 
                        placeholder="Search layer files..." onkeyup="filterLayers()"> 

                    <!-- Multi-file checkbox dropdown style list --> 
                    <div id="layerList" style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:6px; background:#fff;">

                    @if(isset($layers) && $layers->count() > 0)

                        @foreach($layers as $layer)

                            <div class="form-check layer-item"
                                data-dept="{{ $layer->deptid }}">

                                <input 
                                    class="form-check-input layer-checkbox"
                                    type="checkbox"
                                    value="{{ $layer->layername }}"
                                    id="layer_{{ $loop->index }}">

                                <label class="form-check-label"
                                    for="layer_{{ $loop->index }}">

                                    {{ ucfirst($layer->layername) }}

                                </label>

                            </div>

                        @endforeach

                    @else

                        <p class="text-muted">No GIS Layers Uploaded</p>

                    @endif

                    </div>
                </div> 
                
                <!-- ================= SEARCH LOCATION ================= <div class="control-card"> <label class="mb-2">Search Location</label> <input type="text" class="form-control mb-2" placeholder="Enter place name"> <button class="btn btn-primary btn-custom">Search</button> </div> will work on it lateron-->

                <!-- CSV FILE VIEWER -->
                <div class="control-card">
                    <label class="mb-2">CSV File Viewer</label>
                    <input type="file" id="csvFile" class="form-control mb-2" accept=".csv">
                    <button class="btn btn-success btn-custom" onclick="loadCSV()">Upload & View</button>
                    <button class="btn btn-danger btn-custom mt-2" onclick="clearCSV()">Clear CSV Layer</button>
                </div>

                <!-- MAP TOOLS --> 
                <div class="control-card"> 
                    <label class="mb-2">Map Tools</label> 
                    <button class="btn btn-outline-secondary btn-custom mb-2"> Enable Measure Tool </button> 
                    <button class="btn btn-outline-secondary btn-custom mb-2"> Test Button 1 </button> 
                    <button class="btn btn-outline-secondary btn-custom"> Test Button 2 </button> 
                </div> 
                <!-- File Converter -->
                <div class="control-card"> 
                    <label class="mb-2">File Converter</label> 

                    <input type="file" id="zipFile" class="form-control mb-2" accept=".zip"> 

                    <label>GeoJSON File Name</label>
                    <input type="text" id="filename" class="form-control mb-2" placeholder="Enter file name">

                    <button class="btn btn-success btn-custom" onclick="convertAndSave()">GeoJSON Convert</button> 

                    <!-- Status display -->
                    <div id="status" class="mt-2" style="font-size: 14px; color: #333;"></div>
                </div>

            </div>
        </div>

        <!-- MAP AREA -->
        <div class="main-content">
            <div id="map"></div>
        </div>

    </div>
</div>

<!-- GOOGLE MAP SCRIPT & JS  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

<!-- Shapefile to GeoJSON library -->
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>

<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };
</script>

<script src="{{ asset('js/gisApp-Map.js') }}"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9mTWnANz3Mm-Km933gvoxOv5Wp57P3NM&libraries=geometry&callback=initMap" async defer></script>

@endsection