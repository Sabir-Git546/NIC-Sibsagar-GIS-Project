@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">

<style>

/* ================= WRAPPER (Prevents Header Overlap) ================= */
.dashboard-wrapper {
    position: relative;
    margin-top: 10px;
}

/* ================= MAIN DASHBOARD LAYOUT ================= */
.dashboard-container {
    display: flex;
    height: 750px;
    background: #f4f6f9;
}

/* ================= MAP AREA ================= */
.main-content {
    flex: 1;
    position: relative;
}

#map {
    height: 100%;
    width: 100%;
}

/* ================= SIDEBAR ================= */
.side-control-panel {
    width: 320px;
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

        <!-- ================= SIDEBAR ================= -->
        <div class="side-control-panel" id="controlPanel">

            <div class="panel-content">

                <div class="panel-title">
                    <span>GIS Control Panel</span>
                    <button class="close-btn" onclick="closePanel()">✖</button>
                </div>

                <!-- ================= LAYER FILE SELECTOR ================= --> 
                <div class="control-card"> <label class="mb-2">Layer File Selector</label> 
                    <!-- Search Filter --> 
                    <input type="text" id="layerSearch" class="form-control mb-2" 
                        placeholder="Search layer files..." onkeyup="filterLayers()"> 

                    <!-- Multi-file checkbox dropdown style list --> 
                    <div id="layerList" style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:6px; background:#fff;"> 
                        <div class="form-check"> 
                            <input class="form-check-input layer-checkbox" type="checkbox" value="roads.geojson" id="layer1"> 
                            <label class="form-check-label" for="layer1">Road Network</label> </div> <div class="form-check"> 
                            <input class="form-check-input layer-checkbox" type="checkbox" value="schools.geojson" id="layer2"> 
                            <label class="form-check-label" for="layer2">Schools</label> </div> <div class="form-check"> 
                            <input class="form-check-input layer-checkbox" type="checkbox" value="hospitals.geojson" id="layer3"> 
                            <label class="form-check-label" for="layer3">Hospitals</label> </div> <div class="form-check"> 
                            <input class="form-check-input layer-checkbox" type="checkbox" value="water.geojson" id="layer4"> 
                            <label class="form-check-label" for="layer4">Water Bodies</label>
                        </div> 
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
                    <button class="btn btn-success btn-custom">GeoJSON Convert </button> 
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

<script src="{{ asset('js/gisApp-Map.js') }}"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9mTWnANz3Mm-Km933gvoxOv5Wp57P3NM&libraries=geometry&callback=initMap" async defer></script>

@endsection