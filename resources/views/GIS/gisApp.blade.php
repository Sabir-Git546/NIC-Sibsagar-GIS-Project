@php
$hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
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
                                        <div class="layer-item" data-dept="{{ $layer->deptid }}">
                                            <input type="checkbox"
                                                   value="{{ $layer->layername }}"
                                                   onchange="GISLayer.toggle(this)">
                                            <label>{{ ucfirst($layer->layername) }}</label>
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

                                        <option value="overlap">
                                            Overlap Analysis
                                        </option>

                                        <option value="area">
                                            Area Measurement
                                        </option>

                                        <option value="perimeter">
                                            Perimeter Measurement
                                        </option>

                                    </select>

                                    <button class="btn btn-primary w-100 mb-2"
                                            onclick="GISAnalysis.startSelectedAnalysis()">
                                        Run Analysis
                                    </button>

                                    <button class="btn btn-danger w-100"
                                            onclick="GISAnalysis.clearAnalysis()">
                                        Clear Analysis
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

                                <button class="btn btn-danger w-100"
                                        onclick="GISTools.clearCSV()">
                                    Clear CSV
                                </button>

                            </div>
                        </div>
                    </div>

                    <!-- ================= FILE CONVERTER ================= -->
                    <div class="accordion-item">
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
                                    Convert SHP
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ================= MAP ================= -->
        <div class="main-content">
            <div id="map"></div>
        </div>

    </div>
</div>
<!-- ================= LIBS ================= -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>

<!-- ================= GIS ENGINE ================= -->
<script src="{{ asset('js/gis/utils.js') }}"></script>
<script src="{{ asset('js/gis/core.js') }}"></script>
<script src="{{ asset('js/gis/layers.js') }}"></script>
<script src="{{ asset('js/gis/analysis.js') }}"></script>
<script src="{{ asset('js/gis/tools.js') }}"></script>
<script src="{{ asset('js/gis/ui.js') }}"></script>

@endauth
@endsection