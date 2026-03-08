@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">

<style>

/* WRAPPER */
.dashboard-wrapper {
    position: relative;
    margin-top: 10px;
}

/* MAIN DASHBOARD LAYOUT */
.dashboard-container {
    display: flex;
    height: 750px;       /* or use 100vh for full viewport */
    width: 100%;
    background: #f4f6f9;
}

/* MAP AREA */
.main-content {
    flex: 1 1 auto;       /* grow/shrink automatically with sidebar */
    min-width: 0;         /* critical for flex shrinking */
    height: 100%;
    position: relative;
}

/* Map container */
#map {
    height: 100%;
    width: 100%;
}

/* SIDEBAR */
.side-control-panel {
    width: 320px;
    flex-shrink: 0;       /* prevent flexbox from shrinking it below 320px */
    background: #ffffff;
    padding: 20px;
    overflow-y: auto;
    box-shadow: -2px 0 8px rgba(0,0,0,0.05);
    transition: width 0.3s ease, padding 0.3s ease;
}

/* Collapsed State */
.side-control-panel.collapsed {
    width: 0;
    padding: 0;
}

/* Hide content smoothly */
.side-control-panel.collapsed .panel-content {
    display: none;
}

/* Panel header */
.panel-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 20px;
    color: #2c3e50;
}

/* Close Button (X) */
.close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #2c3e50;
}

/* Floating Open Button (☰) */
.open-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
    background: #2c3e50;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
    display: none;
}

/* Cards */
.control-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #e3e6ea;
}

.control-card label {
    font-weight: 500;
    font-size: 14px;
}

.btn-custom {
    width: 100%;
    margin-top: 8px;
}

</style>
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