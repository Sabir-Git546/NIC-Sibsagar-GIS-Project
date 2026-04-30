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

                <div class="accordion" id="gisControlsAccordion">

                    <!-- ================= ADMIN LAYERS ================= -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button"
                                data-bs-toggle="collapse" data-bs-target="#adminLayerSelector">
                                Layer Selector
                            </button>
                        </h2>

                        <div id="adminLayerSelector"
                             class="accordion-collapse collapse show"
                             data-bs-parent="#gisControlsAccordion">

                            <div class="accordion-body">

                                <label class="mb-1">Administrative Layers</label>

                                <input type="text"
                                    id="adminLayerSearch"
                                    class="form-control mb-2"
                                    placeholder="Search layer files..."
                                    onkeyup="filterAdminLayers()">

                                <div id="adminLayerList"
                                    style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:6px; background:#fff;">

                                    <!-- Example (Dynamic from DB later) -->
                                    <div class="form-check layer-item">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">District Boundary</label>
                                    </div>

                                    <div class="form-check layer-item">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Block Boundary</label>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- ================= GIS FILE SELECTOR ================= -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#gisFileSelector">
                                GIS File Selector
                            </button>
                        </h2>

                        <div id="gisFileSelector"
                             class="accordion-collapse collapse"
                             data-bs-parent="#gisControlsAccordion">

                            <div class="accordion-body">

                                <!-- Department -->
                                <label class="mb-1">Department</label>
                                <select id="deptFilter"
                                        class="form-control mb-2"
                                        onchange="filterLayers()">

                                    <option value="">All Departments</option>

                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->deptid }}">
                                            {{ $dept->deptname }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search -->
                                <input type="text"
                                    id="gisLayerSearch"
                                    class="form-control mb-2"
                                    placeholder="Search layer files..."
                                    onkeyup="filterLayers()">

                                <!-- Layer List -->
                                <div id="gisLayerList"
                                    style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:6px; background:#fff;">

                                @if(isset($layers) && $layers->count() > 0)

                                    @foreach($layers as $layer)

                                        <div class="form-check layer-item"
                                             data-dept="{{ $layer->deptid }}">

                                            <input class="form-check-input layer-checkbox"
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
                        </div>
                    </div>


                    <!-- ================= CSV VIEWER ================= -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#csvViewer">
                                CSV File Viewer
                            </button>
                        </h2>

                        <div id="csvViewer"
                             class="accordion-collapse collapse"
                             data-bs-parent="#gisControlsAccordion">

                            <div class="accordion-body">

                                <input type="file" id="csvFile"
                                       class="form-control mb-2" accept=".csv">

                                <button class="btn btn-success btn-custom w-100 mb-2"
                                        onclick="loadCSV()">
                                    Upload & View
                                </button>

                                <button class="btn btn-danger btn-custom w-100"
                                        onclick="clearCSV()">
                                    Clear CSV Layer
                                </button>

                            </div>
                        </div>
                    </div>


                    <!-- ================= MAP TOOLS ================= -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#mapTools">
                                Map Tools
                            </button>
                        </h2>

                        <div id="mapTools"
                             class="accordion-collapse collapse"
                             data-bs-parent="#gisControlsAccordion">

                            <div class="accordion-body">

                                <button id="measureBtn"
                                        class="btn btn-outline-secondary w-100 mb-2"
                                        onclick="toggleMeasure()">
                                    Enable Measure Tool
                                </button>

                                <button class="btn btn-outline-warning w-100 mb-2" onclick="undoLastPoint()">
                                    Undo Last Point
                                </button>

                                <button class="btn btn-outline-danger w-100 mb-2" onclick="clearMeasurement()">
                                    Clear Measurement
                                </button>

                            </div>
                        </div>
                    </div>


                    <!-- ================= FILE CONVERTER ================= -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#fileConverter">
                                File Converter
                            </button>
                        </h2>

                        <div id="fileConverter"
                             class="accordion-collapse collapse"
                             data-bs-parent="#gisControlsAccordion">

                            <div class="accordion-body">

                                <input type="file" id="zipFile"
                                       class="form-control mb-2" accept=".zip">

                                <label>GeoJSON File Name</label>
                                <input type="text" id="filename"
                                       class="form-control mb-2"
                                       placeholder="Enter file name">

                                <button class="btn btn-success w-100"
                                        onclick="convertAndSave()">
                                    GeoJSON Convert
                                </button>

                                <div id="status" class="mt-2"></div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- CONTEXT MENU -->
        <div id="contextMenu" style="
            position:absolute;
            display:none;
            background:#fff;
            border:1px solid #ccc;
            border-radius:6px;
            box-shadow:0 2px 6px rgba(0,0,0,0.2);
            z-index:999;
            padding:8px;
            width:180px;
        ">
            <button class="btn btn-sm btn-primary w-100 mb-1"
                    onclick="openNearbySearch()">
                Find Nearby Projects
            </button>

            <button id="bufferBtn"
                    class="btn btn-sm btn-success w-100"
                    onclick="createBufferArea()">
                Create Buffer
            </button>

            <button class="btn btn-sm btn-danger w-100 mb-1"
                    onclick="clearAllBuffers()">
                Clear All Buffers
            </button>
        </div>


        <!-- BUFFER INPUT PANEL -->
        <div id="bufferPanel" style="
            position:absolute;
            display:none;
            background:#fff;
            border:1px solid #ccc;
            border-radius:8px;
            padding:12px;
            box-shadow:0 3px 10px rgba(0,0,0,0.2);
            z-index:1000;
            width:220px;
        ">
            <label><b>Buffer Distance (meters)</b></label>
            <input type="number" id="bufferDistance"
                class="form-control mt-1 mb-2"
                placeholder="Enter meters">

            <button class="btn btn-sm btn-success w-100 mb-1"
                    onclick="applyBuffer()">
                Apply
            </button>

            <button class="btn btn-sm btn-secondary w-100"
                    onclick="closeBufferPanel()">
                Cancel
            </button>
        </div>

        <!-- MAP AREA -->
        <div class="main-content">
            <div id="map"></div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="{{ asset('js/gisApp-Map.js') }}"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9mTWnANz3Mm-Km933gvoxOv5Wp57P3NM&libraries=geometry&callback=initMap" async defer></script>

@endsection






