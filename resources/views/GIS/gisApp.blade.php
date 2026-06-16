@php
$hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')

<link rel="stylesheet" href="{{ asset('css/gisApp.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endsection

@section('content')

@auth

<div class="dashboard-wrapper">

    <button class="open-btn" id="openPanelBtn" onclick="GISUI.openPanel()">☰</button>

    <div class="dashboard-container">

        <!-- ================= SIDEBAR ================= -->
        <div class="side-control-panel" id="controlPanel">
        <br>
            <a href="{{ route('dashboard') }}"
            class="btn btn-success d-flex align-items-center gap-2 mb-4 shadow-sm rounded-pill px-3 py-2">

                <i class="bi bi-arrow-left"></i>

                <span><b>Dashboard</b></span>

            </a>
            <div class="panel-content">
                
                <div class="panel-title">
                    <span>GIS Control Panel</span>
                    <button class="close-btn" onclick="GISUI.closePanel()">✖</button>
                </div>

                <div class="accordion">

                    <!-- ================= LAYERS ================= -->
                    <div class="accordion-item">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#layers">
                            GIS Layers
                        </button>

                        <div id="layers" class="accordion-collapse collapse show">

                            <div class="accordion-body">

                                <select id="deptFilter" class="form-control mb-2" onchange="GISLayer.filter()">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->deptid }}">
                                            {{ $dept->deptname }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="text"
                                       class="form-control mb-2"
                                       placeholder="Search layers..."
                                       onkeyup="GISLayer.search(this.value)">

                                <div>
                                    <input type="checkbox" id="selectAllLayers" onclick="GISLayer.toggleAll(this)">
                                    <label>Select All</label>
                                </div>

                                <div id="layerList">

                                    @foreach($layers as $layer)

                                        <div class="layer-item d-flex justify-content-between align-items-center mb-2"
                                            data-dept="{{ $layer->deptid }}"
                                            data-name="{{ strtolower($layer->layername) }}">

                                            <!-- CHECKBOX -->
                                            <div>
                                                <input type="checkbox"
                                                    value="{{ $layer->layername }}"
                                                    onchange="GISLayer.toggle(this)">

                                                <label>{{ ucfirst($layer->layername) }}</label>
                                            </div>

                                            <!-- LAYERS -->
                                            <div class="d-flex gap-1">

                                                <!-- EXPORT DROPDOWN -->
                                                <div class="dropdown">

                                                    <button type="button"
                                                            class="btn btn-sm btn-secondary p-1 d-flex align-items-center justify-content-center"
                                                            style="width:22px; height:22px;"
                                                            data-bs-toggle="dropdown">

                                                        <i class="bi bi-download" style="font-size:12px;"></i>

                                                    </button>

                                                    <ul class="dropdown-menu">

                                                        <li>
                                                            <a class="dropdown-item"
                                                            href="/gis/export/kml/{{ $layer->projectid }}/{{ rawurlencode($layer->layername) }}">

                                                                Export KML

                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                            href="/gis/export/shapefile/{{ $layer->projectid }}/{{ rawurlencode($layer->layername) }}">

                                                                Export Shapefile (.zip)

                                                            </a>
                                                        </li>

                                                    </ul>


                                                </div>

                                                <!-- DELETE BUTTON -->
                                                <button type="button"
                                                        class="btn btn-sm btn-danger p-1 d-flex align-items-center justify-content-center"
                                                        style="width:22px; height:22px;"
                                                        onclick="GISLayer.deleteLayer({{ $layer->projectid }}, @js($layer->layername))">

                                                    <i class="bi bi-trash" style="font-size:14px;"></i>

                                                </button>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= TOOLS ================= -->
                    <div class="accordion-item">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#tools">
                            GIS Tools
                        </button>

                        <div id="tools" class="accordion-collapse collapse">

                            <div class="accordion-body">

                                <!-- ================= SPATIAL ANALYSIS ================= -->

                                <div class="control-card spatial-analysis-card">

                                    <label class="mb-2 d-block">
                                        Spatial Analysis
                                    </label>

                                    <select id="analysisType" class="form-control analysis-select mb-3">

                                        <option value="">Select Analysis</option>

                                        <option value="distance">
                                            Distance Analysis
                                        </option>

                                        <option value="buffer">
                                            Buffer Analysis
                                        </option>

                                    <!--    <option value="overlap">
                                            Overlap Analysis
                                        </option>

                                        <option value="area">
                                            Area Measurement
                                        </option>

                                        <option value="perimeter">
                                            Perimeter Measurement
                                        </option>   -->

                                    </select>   

                                    <button class="btn btn-success w-100 mb-2"
                                            onclick="GISAnalysis.startSelectedAnalysis()">
                                        Run Analysis
                                    </button>

                                     <button id="saveLayerBtn" class="btn btn-success w-100 mb-2 d-none"
                                            onclick="GISAnalysis.openSaveLayerCard()">
                                        Save Layer
                                    </button>

                                    <button class="btn btn-danger w-100 mb-2"
                                            onclick="GISAnalysis.clearAnalysis()">
                                        Clear Analysis
                                    </button>

                                    <button
                                        id="createLayerBtn"
                                        class="btn btn-primary w-100"
                                        onclick="GISDrawing.openCreateLayerModal()">

                                        + Create Layer

                                    </button>

                                    <button
                                        id="finishLineBtn"
                                        class="btn btn-warning w-100 mt-2 d-none"
                                        onclick="GISDrawing.finishDrawing()">

                                        Finish Drawing

                                    </button>

                                    <button
                                        id="saveDrawLayerBtn"
                                        class="btn btn-success w-100 mt-2 d-none"
                                        onclick="GISDrawing.saveDrawnLayer()">

                                        Save Drawn Layer

                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= CSV VIEWER ================= -->
                    <div class="accordion-item">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#csv">
                            CSV Viewer
                        </button>

                        <div id="csv" class="accordion-collapse collapse">

                            <div class="accordion-body">

                                <input type="file" id="csvFile" class="form-control mb-2">

                                <button class="btn btn-success w-100 mb-2"
                                        onclick="GISTools.loadCSV()">
                                    Load CSV
                                </button>

                                <button class="btn btn-danger w-100 mb-2"
                                        onclick="GISTools.clearCSV()">
                                    Clear CSV
                                </button>

                                <button class="btn btn-primary w-100 mb-2"
                                        onclick="GISTools.openCSVLayerModal()">

                                    Save CSV as Layer

                                </button>

                            </div>
                        </div>
                    </div>

                    <!-- ================= CSV SAVE MODAL ================= -->
                    <div class="modal fade"
                        id="csvLayerModal"
                        tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">
                                        Save CSV as GIS Layer
                                    </h5>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    <!-- PROJECT -->
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Project
                                        </label>

                                        <select id="csvProjectId"
                                                class="form-select">

                                            <option value="">
                                                Select Project
                                            </option>

                                            @foreach($projects as $project)

                                                <option value="{{ $project->projectid }}">
                                                    {{ $project->projectname }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                    <!-- LAYER NAME -->
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Layer Name
                                        </label>

                                        <input type="text"
                                            id="csvLayerName"
                                            class="form-control">

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">

                                        Cancel

                                    </button>

                                    <button type="button"
                                            class="btn btn-success"
                                            onclick="GISTools.saveCSVLayer()">

                                        Create Layer

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================= KML VIEWER ================= -->

                    <div class="accordion-item">

                        <button
                            class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#kml">

                            KML Viewer

                        </button>

                        <div
                            id="kml"
                            class="accordion-collapse collapse">

                            <div class="accordion-body">

                                <input
                                    type="file"
                                    id="kmlFile"
                                    class="form-control mb-2"
                                    accept=".kml">

                                <button
                                    class="btn btn-success w-100 mb-2"
                                    onclick="GISTools.loadKML()">

                                    Load KML

                                </button>

                                <button
                                    class="btn btn-danger w-100 mb-2"
                                    onclick="GISTools.clearKML()">

                                    Clear KML

                                </button>

                                <button
                                    class="btn btn-primary w-100"
                                    onclick="GISTools.openKMLLayerModal()">

                                    Save KML as Layer

                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= CSV SAVE MODAL ================= -->
                    <div class="modal fade"
                        id="kmlLayerModal"
                        tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">
                                        Save kml as GIS Layer
                                    </h5>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    <!-- PROJECT -->
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Project
                                        </label>

                                        <select id="kmlProjectId"
                                                class="form-select">

                                            <option value="">
                                                Select Project
                                            </option>

                                            @foreach($projects as $project)

                                                <option value="{{ $project->projectid }}">
                                                    {{ $project->projectname }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                    <!-- LAYER NAME -->
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Layer Name
                                        </label>

                                        <input type="text"
                                            id="kmlLayerName"
                                            class="form-control">

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">

                                        Cancel

                                    </button>

                                    <button type="button"
                                            class="btn btn-success"
                                            onclick="GISTools.saveKMLLayer()">

                                        Create Layer

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- ================= FILE CONVERTER ================= -->
  <!--                  <div class="accordion-item">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#convert">
                                File Converter
                            </button>

                            <div id="convert" class="accordion-collapse collapse">

                                <div class="accordion-body">

                                    <input type="file" id="zipFile" class="form-control mb-2">

                                    <input type="text"
                                        id="geojsonName"
                                        class="form-control mb-2"
                                        placeholder="GeoJSON name">

                                    <button class="btn btn-primary w-100"
                                            onclick="GISTools.convertShp()">
                                        Convert File
                                    </button>

                                </div>
                            </div>
                    </div>                          -->

                </div>
            </div>
        </div>

        <!-- ================= MAP ================= -->
        <div class="main-content">
            <div id="map"></div>
        </div>

    </div>
</div>

<div id="saveLayerCard" class="gis-modal d-none">

    <div class="gis-modal-backdrop" onclick="GISAnalysis.closeSaveLayerCard()"></div>

    <div class="gis-modal-window">

        <div class="gis-modal-header">
            <h5>Save Buffered Layer</h5>
            <button onclick="GISAnalysis.closeSaveLayerCard()">✖</button>
        </div>

        <div class="gis-modal-body">

            <!-- ONLY LAYER NAME -->
            <input
                type="text"
                id="newLayerName"
                placeholder="Enter layer name (e.g. buffer_100m)"
                class="form-control mb-2"
            />

            <!-- OPTIONAL -->
            <textarea
                id="layerDescription"
                placeholder="Description (optional)"
                class="form-control mb-2"
            ></textarea>

        </div>

        <div class="gis-modal-footer">

            <button class="btn btn-secondary"
                onclick="GISAnalysis.closeSaveLayerCard()">
                Cancel
            </button>

            <button class="btn btn-primary"
                onclick="GISAnalysis.saveLayer()">
                Save
            </button>

        </div>

    </div>

</div>

<div
    id="createLayerModal"
    class="card d-none"
    style="
        position:absolute;
        top:100px;
        left:50%;
        transform:translateX(-50%);
        width:400px;
        z-index:9999;
    "
>

    <div class="card-header">

        Create GIS Layer

    </div>

    <div class="card-body">

        <label>Project</label>

        <select id="drawingProject" class="form-control mb-2">
            <option value="">Select Project</option>

            @foreach($projects as $project)
                <option value="{{ $project->projectid }}">
                    {{ $project->projectname }}
                </option>
            @endforeach
        </select>

        <label>Layer Name</label>

        <input
            type="text"
            id="drawingLayerName"
            class="form-control mb-2">

        <label>Geometry Type</label>

        <select
            id="drawingGeometryType"
            class="form-control">

            <option value="Point">
                Point
            </option>

            <option value="LineString">
                Line
            </option>

            <option value="Polygon">
                Polygon
            </option>

        </select>

    </div>

    <div class="card-footer">

        <button
            class="btn btn-primary"
            onclick="GISDrawing.createLayer()">

            Create

        </button>

        <button
            class="btn btn-secondary"
            onclick="GISDrawing.closeCreateLayerModal()">

            Close

        </button>

    </div>

</div>

<div id="attributeModal" class="gis-modal d-none">

    <div class="gis-modal-backdrop"></div>

    <div class="gis-modal-window">

        <div class="gis-modal-header">
            <h5>Feature Attributes</h5>
        </div>

        <div class="gis-modal-body">

            <input
                type="text"
                id="attrName"
                class="form-control mb-2"
                placeholder="Name">

            <input
                type="text"
                id="attrType"
                class="form-control mb-2"
                placeholder="Type">

            <textarea
                id="attrDescription"
                class="form-control"
                placeholder="Description"></textarea>

        </div>

        <div class="gis-modal-footer">

            <button
                class="btn btn-primary"
                onclick="GISDrawing.saveFeatureAttributes()">

                Save Attributes

            </button>

        </div>

    </div>

</div>

<!-- ================= LIBS ================= -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
<script src="https://unpkg.com/@tmcw/togeojson@5.8.1/dist/togeojson.umd.js"></script>

<!-- ================= GIS ENGINE ================= -->
<script src="{{ asset('js/gis/utils.js') }}"></script>
<script src="{{ asset('js/gis/core.js') }}"></script>

<script src="{{ asset('js/gis/analysis.js') }}"></script>
<script src="{{ asset('js/gis/tools.js') }}"></script>
<script src="{{ asset('js/gis/ui.js') }}"></script>
<script src="{{ asset('js/gis/drawing.js') }}"></script>
<script src="{{ asset('js/gis/layers.js') }}"></script>



@endauth
@endsection