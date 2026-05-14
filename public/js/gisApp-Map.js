// ======================================================
// GLOBAL VARIABLES
// ======================================================
let map;
let csvMarkers = [];
let csvInfoWindow;
let selectedBuffer = null;

// Measuring Tool variables
let measureMode = false;
let measurePath = [];
let measurePolyline = null;
let measureMarkers = [];
let measureInfoWindow = null;
let totalDistance = 0;

// Nearby search and buffer
let rightClickLocation = null;
let searchCircle = null;
let selectedFeature = null;
let activeBuffer = null;

// store all buffer shapes
let buffers = [];   //not used till now

// GIS Layers
let activeLayers = {};


// ======================================================
// MAP INITIALIZATION
// ======================================================
window.initMap = function () {

    const center = { lat: 26.9891, lng: 94.6394 };

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: center,
        mapTypeId: "roadmap"
    });

    csvInfoWindow = new google.maps.InfoWindow();

    // secure gis html popups
    function escapeHtml(text) {
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

    // Feature click (attribute popup)
    map.data.addListener('click', function(event) {

        let content = "<div style='max-height:200px;overflow:auto;'>";
        let hasData = false;

        event.feature.forEachProperty(function(value, key) {
            hasData = true;
            content += "<b>" + escapeHtml(key) + ":</b> " + escapeHtnl(value) + "<br>";
        });

        if (!hasData) {
            content += "<i>No attribute data available</i>";
        }

        content += "</div>";

        csvInfoWindow.setPosition(event.latLng);
        csvInfoWindow.setContent(content);
        csvInfoWindow.open(map);
    });

    initContextMenu();

    // ======================================================
    // FIXED LAYER CHECKBOX EVENT (DELEGATION)
    // ======================================================
    document.getElementById("gisLayerList").addEventListener("change", function (e) {

        if (!e.target.classList.contains("layer-checkbox")) return;

        const layer = e.target.value;

        if (e.target.checked) {
            loadLayer(layer);
        } else {
            removeLayer(layer);
        }

        syncSelectAll();
    });

    // Map click for clearing nearby search
    map.addListener("click", function(e) {

        const menu = document.getElementById("contextMenu");
        if (menu) menu.style.display = "none";

        if (measureMode) return;
        if (!searchCircle) return;

        let center = searchCircle.getCenter();
        let radius = searchCircle.getRadius();

        let distance = google.maps.geometry.spherical.computeDistanceBetween(
            center,
            e.latLng
        );

        if (distance > radius) {
            clearNearbySearch();
        }
    });
};

// ======================================================
// MAP BOUNDS
// ======================================================
function updateMapBounds(){

    let bounds = new google.maps.LatLngBounds();

    csvMarkers.forEach(marker => {
        bounds.extend(marker.getPosition());
    });

    map.data.forEach(function(feature){
        processPoints(feature.getGeometry(), bounds.extend, bounds);
    });

    if(!bounds.isEmpty()){
        map.fitBounds(bounds);

        google.maps.event.addListenerOnce(map, 'bounds_changed', function () {
            if (map.getZoom() > 16) {
                map.setZoom(16);
            }
        });
    }
}

// ======================================================
// CSV MODULE
// ======================================================
function loadCSV() {

    const file = document.getElementById("csvFile").files[0];

    if (!file) {
        alert("Please select a CSV file.");
        return;
    }

    Papa.parse(file, {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,

        complete: function(results) {

            clearCSV();
            let validPoints = 0;

            results.data.forEach(row => {

                let lat = row.latitude || row.lat || row.Latitude || row.Lat;
                let lng = row.longitude || row.lng || row.Longitude || row.Lng;

                lat = parseFloat(lat);
                lng = parseFloat(lng);

                if (!isNaN(lat) && !isNaN(lng)) {

                    let marker = new google.maps.Marker({
                        position: { lat, lng },
                        map: map
                    });

                    validPoints++;

                    let content = "<div style='max-height:200px;overflow:auto;'>";
                    for (let key in row) {
                        content += "<b>" + escapeHtml(key) + ":</b> " + escapeHtml(row[key]) + "<br>";
                    }
                    content += "</div>";

                    marker.addListener("click", function() {
                        csvInfoWindow.setContent(content);
                        csvInfoWindow.open(map, marker);
                    });

                    csvMarkers.push(marker);
                }
            });

            if (validPoints > 0) {
                updateMapBounds();
                if (validPoints === 1) map.setZoom(15);
                alert("CSV Loaded Successfully!");
            } else {
                alert("No valid latitude/longitude found.");
            }
        }
    });
}

function clearCSV() {
    csvMarkers.forEach(marker => marker.setMap(null));
    csvMarkers = [];

    if (csvInfoWindow) csvInfoWindow.close();

    updateMapBounds();
}

// ======================================================
// GIS LAYERS
// ======================================================
function loadLayer(layername) {

    if (activeLayers[layername]) return;

    fetch("/gis/layer/" + layername)
        .then(res => res.json())
        .then(data => {

            const features = map.data.addGeoJson(data);

            features.forEach(f => {
                f.setProperty("layername", layername);
            });

            // STORE REAL FEATURES (IMPORTANT FIX)
            activeLayers[layername] = features;

            updateMapBounds();
        })
        .catch(err => console.error(err));
}

function removeLayer(layername) {

    const features = activeLayers[layername];

    if (features && Array.isArray(features)) {

        features.forEach(f => {
            map.data.remove(f);
        });
    }

    delete activeLayers[layername];

    updateMapBounds();
}

// SELECT / UNSELECT ALL
function toggleAllLayers(masterCheckbox) {

    const checkboxes = document.querySelectorAll('.layer-checkbox');

    checkboxes.forEach(cb => {

        const layer = cb.value;

        cb.checked = masterCheckbox.checked;

        if (masterCheckbox.checked) {

            if (!activeLayers[layer]) {
                loadLayer(layer);
            }

        } else {
            removeLayer(layer);
        }
    });

    syncSelectAll();
}

// SYNC MASTER CHECKBOX
function syncSelectAll() {

    const checkboxes = document.querySelectorAll('.layer-checkbox');

    const allChecked =
        Array.from(checkboxes).every(cb => cb.checked);

    document.getElementById('selectAllLayers').checked = allChecked;
}

// Remove Layers
function removeLayer(layername) {

    const toRemove = [];

    map.data.forEach(feature => {

        if (feature.getProperty("layername") === layername) {
            toRemove.push(feature);
        }
    });

    toRemove.forEach(f => map.data.remove(f));

    delete activeLayers[layername];

    updateMapBounds();
}

//load layer safely
function loadLayer(layername) {

    if (activeLayers[layername]) return;

    fetch("/gis/layer/" + layername)
        .then(res => res.json())
        .then(data => {

            let features = map.data.addGeoJson(data);

            features.forEach(f => {
                f.setProperty("layername", layername);
            });

            activeLayers[layername] = true;

            updateMapBounds();
        })
        .catch(err => console.error("Layer load error:", err));
}

// ======================================================
// GEOMETRY UTILITY
// ======================================================
function processPoints(geometry, callback, thisArg) {

    if (!geometry) return;

    // ✅ LatLng (direct)
    if (geometry instanceof google.maps.LatLng) {
        callback.call(thisArg, geometry);
        return;
    }

    // ✅ Point (GeoJSON Point)
    if (geometry instanceof google.maps.Data.Point) {
        callback.call(thisArg, geometry.get());
        return;
    }

    // ✅ Handles Multi geometries (MultiPolygon, MultiLineString)
    if (geometry.getArray) {
        geometry.getArray().forEach(g => {
            processPoints(g, callback, thisArg);
        });
        return;
    }

    // ✅ Handles LineString / Polygon paths
    if (geometry.getAt) {
        for (let i = 0; i < geometry.getLength(); i++) {
            processPoints(geometry.getAt(i), callback, thisArg);
        }
        return;
    }

    console.warn("Unknown geometry type:", geometry);
}



// ======================================================
// SIDEBAR CONTROLS
// ======================================================
function closePanel() {

    const panel = document.getElementById("controlPanel");

    panel.classList.add("collapsed");
    document.getElementById("openPanelBtn").style.display = "block";

    setTimeout(() => {
        google.maps.event.trigger(map, "resize");
        map.setCenter({ lat: 26.9891, lng: 94.6394 });
    }, 300);
}

function openPanel() {

    const panel = document.getElementById("controlPanel");

    panel.classList.remove("collapsed");
    document.getElementById("openPanelBtn").style.display = "none";

    setTimeout(() => {
        google.maps.event.trigger(map, "resize");
        map.setCenter({ lat: 26.9891, lng: 94.6394 });
    }, 300);
}


// ======================================================
// FILE CONVERTER
// ======================================================
async function convertAndSave() {

    const file = document.getElementById('zipFile').files[0];
    const name = document.getElementById('filename').value.trim();

    if (!file || !name) {
        alert("Select file and enter name");
        return;
    }

    try {
        const buffer = await file.arrayBuffer();
        const geojson = await shp(buffer);

        const blob = new Blob([JSON.stringify(geojson)], { type: "application/json" });

        const a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name + ".geojson";
        a.click();

        alert("Saved successfully!");

    } catch (err) {
        console.error(err);
        alert("Conversion failed");
    }
}


// ======================================================
// FILTER FUNCTIONS
// ======================================================
function filterAdminLayers() {

    let search = document.getElementById("adminLayerSearch").value.toLowerCase();

    document.querySelectorAll("#adminLayerList .layer-item").forEach(item => {
        let text = item.innerText.toLowerCase();
        item.style.display = text.includes(search) ? "" : "none";
    });
}

function filterLayers() {

    let search = document.getElementById("gisLayerSearch").value.toLowerCase();
    let dept = document.getElementById("deptFilter").value;

    document.querySelectorAll("#gisLayerList .layer-item").forEach(item => {

        let text = item.innerText.toLowerCase();
        let itemDept = item.getAttribute("data-dept");

        let matchSearch = text.includes(search);
        let matchDept = !dept || dept === itemDept;

        item.style.display = (matchSearch && matchDept) ? "" : "none";
    });
}


// ======================================================
// MEASUREMENT TOOL (UNCHANGED LOGIC)
// ======================================================
function toggleAllLayers(masterCheckbox) {

    const checkboxes = document.querySelectorAll('.layer-checkbox');

    const shouldLoad = masterCheckbox.checked;

    checkboxes.forEach(cb => {

        const layer = cb.value;

        // avoid unnecessary re-trigger loops
        cb.checked = shouldLoad;

        if (shouldLoad) {

            if (!activeLayers[layer]) {
                loadLayer(layer);
            }

        } else {
            removeLayerSafe(layer);
        }
    });

    syncSelectAll();
}
function addMeasurePoint(event) {

    if (!measureMode || !measurePolyline) return;

    const position = event.latLng;

    measurePath.push(position);

    const marker = new google.maps.Marker({
        position: position,
        map: map,
        label: String(measurePath.length)
    });

    measureMarkers.push(marker);

    measurePolyline.setPath(measurePath);

    updateMeasurement();
}

function updateMeasurement() {

    if (!measurePolyline || measurePath.length < 2) return;

    totalDistance = google.maps.geometry.spherical.computeLength(measurePath);

    let display = totalDistance < 1000
        ? totalDistance.toFixed(2) + " meters"
        : (totalDistance / 1000).toFixed(2) + " km";

    let lastPoint = measurePath[measurePath.length - 1];

    measureInfoWindow.setContent("<b>Distance:</b> " + display);
    measureInfoWindow.setPosition(lastPoint);
    measureInfoWindow.open(map);
}

function finishMeasurement() {

    google.maps.event.clearListeners(map, "click");
    google.maps.event.clearListeners(map, "dblclick");

    map.setOptions({ draggableCursor: null });

    measureMode = false;

    if (!measurePolyline) return;

    measurePolyline.addListener("click", function(event) {

        let display = totalDistance < 1000
            ? totalDistance.toFixed(2) + " meters"
            : (totalDistance / 1000).toFixed(2) + " km";

        measureInfoWindow.setContent("<b>Total Distance:</b> " + display);
        measureInfoWindow.setPosition(event.latLng);
        measureInfoWindow.open(map);
    });
}

function undoLastPoint() {

    if (!measureMode) return;

    if (measurePath.length === 0) return;

    // remove last coordinate
    measurePath.pop();

    // remove last marker safely
    const marker = measureMarkers.pop();
    if (marker) marker.setMap(null);

    // update polyline safely
    if (measurePolyline) {
        measurePolyline.setPath(measurePath);
    }

    // recalculate distance
    if (measurePath.length >= 2) {
        totalDistance = google.maps.geometry.spherical.computeLength(measurePath);
        updateMeasurement();
    } else {
        totalDistance = 0;

        if (measureInfoWindow) {
            measureInfoWindow.close();
        }
    }
}

function clearMeasurement() {

    // remove markers
    measureMarkers.forEach(m => m.setMap(null));
    measureMarkers = [];

    // reset polyline (DON'T DESTROY OBJECT)
    if (measurePolyline) {
        measurePolyline.setPath([]);
    }

    // reset state
    measurePath = [];
    totalDistance = 0;

    // close info window
    if (measureInfoWindow) {
        measureInfoWindow.close();
    }
}
// --------------xxx-------------------------xxxx---------------


// This Part Works For Nearby Projects and Buffer
// ======================================================
// CONTEXT MENU INITIALIZATION
// ======================================================
function initContextMenu() {

    const menu = document.getElementById("contextMenu");

    // RIGHT CLICK ON FEATURE
    map.data.addListener("rightclick", function (e) {

        selectedFeature = e.feature;
        rightClickLocation = e.latLng;

        showContextMenu(e.domEvent.pageX, e.domEvent.pageY);
    });

    // RIGHT CLICK ON MAP
    map.addListener("rightclick", function (e) {

        selectedFeature = null;
        rightClickLocation = e.latLng;

        showContextMenu(e.domEvent.pageX, e.domEvent.pageY);
    });

    // HIDE MENU
    map.addListener("click", () => menu.style.display = "none");

    document.addEventListener("click", () => menu.style.display = "none");

    menu.addEventListener("click", (e) => e.stopPropagation());
}

// ======================================================
// SHOW CONTEXT MENU
// ======================================================
// 

function showContextMenu(x, y) {

    const menu = document.getElementById("contextMenu");

    menu.innerHTML = `
        <button class="btn btn-sm btn-primary w-100 mb-1"
            onclick="openNearbySearch()">
            Find Nearby Projects
        </button>

        <button class="btn btn-sm btn-success w-100 mb-1"
            onclick="createBufferArea()">
            Create Buffer
        </button>

        <button class="btn btn-sm btn-danger w-100"
            onclick="clearAllBuffers()">
            Clear All Buffers
        </button>
    `;

    menu.style.left = x + "px";
    menu.style.top = y + "px";
    menu.style.display = "block";
}

// ======================================================
// UPDATE BUFFER BUTTON UI
// ======================================================
function updateBufferButtonText() {

    const btn = document.getElementById("bufferBtn");

    if (!btn) return;

    if (activeBuffer) {
        btn.innerText = "Remove Buffer";
        btn.classList.remove("btn-success");
        btn.classList.add("btn-danger");
    } else {
        btn.innerText = "Create Buffer";
        btn.classList.remove("btn-danger");
        btn.classList.add("btn-success");
    }
}

// ======================================================
// OPEN NEARBY SEARCH
// ======================================================
window.openNearbySearch = function () {

    let radiusKm = prompt("Enter radius in kilometers (km):", "1");

    if (!radiusKm) return;

    radiusKm = parseFloat(radiusKm);

    if (isNaN(radiusKm) || radiusKm <= 0) {
        alert("Please enter a valid number.");
        return;
    }

    findNearbyProjects(radiusKm * 1000);

    document.getElementById("contextMenu").style.display = "none";
};

// ======================================================
// FIND NEARBY FEATURES
// ======================================================
function findNearbyProjects(radius) {

    if (!rightClickLocation) {
        alert("No location selected.");
        return;
    }

    let nearbyFeatures = [];

    map.data.forEach(function (feature) {

        const geometry = feature.getGeometry();

        processPoints(geometry, function (latlng) {

            let distance = google.maps.geometry.spherical.computeDistanceBetween(
                rightClickLocation,
                latlng
            );

            if (distance <= radius) {
                nearbyFeatures.push(feature);
            }
        });
    });

    highlightNearby(nearbyFeatures, radius);
}

// ======================================================
// HIGHLIGHT NEARBY + CIRCLE + POPUP
// ======================================================
function highlightNearby(features, radius) {

    map.data.revertStyle();

    features.forEach(f => {
        map.data.overrideStyle(f, {
            fillColor: "yellow",
            strokeColor: "red",
            strokeWeight: 3
        });
    });

    if (searchCircle) searchCircle.setMap(null);

    searchCircle = new google.maps.Circle({
        map: map,
        center: rightClickLocation,
        radius: radius,
        fillColor: "blue",
        fillOpacity: 0.1,
        strokeColor: "blue",
        strokeWeight: 2
    });

    let projectNames = features.map(f =>
        f.getProperty("id") ||
        f.getProperty("id") ||
        "Unnamed Project"

    );

    showNearbyProjectsPopup(rightClickLocation, projectNames);

    alert(`${features.length} project(s) found`);
}

// ======================================================
// POPUP
// ======================================================
function showNearbyProjectsPopup(latLng, projects) {

    const content = projects.length
        ? "<b>Projects:</b><br><br>" + projects.join("<br>")
        : "<b>No projects found</b>";

    new google.maps.InfoWindow({
        content,
        position: latLng
    }).open(map);
}

// ======================================================
// CLEAR SEARCH
// ======================================================
function clearNearbySearch() {

    if (searchCircle) {
        searchCircle.setMap(null);
        searchCircle = null;
    }

    map.data.revertStyle();
}

// ======================================================
// SAFE FEATURE → GEOJSON CONVERTER (FIXED)
// ======================================================
function featureToGeoJSON(feature) {

    const geometry = feature.getGeometry();
    const type = geometry.getType();

    // ✅ FIX: Handle Point directly
    if (type === "Point") {
        const latLng = geometry.get();
        return turf.point([latLng.lng(), latLng.lat()]);
    }

    const coords = [];

    function extract(g) {

        if (!g) return;

        if (g instanceof google.maps.LatLng) {
            coords.push([g.lng(), g.lat()]);
            return;
        }

        if (g.getArray) {
            g.getArray().forEach(extract);
            return;
        }

        if (g.getAt) {
            for (let i = 0; i < g.getLength(); i++) {
                extract(g.getAt(i));
            }
            return;
        }
    }

    extract(geometry);

    // ✅ LineString
    if (type === "LineString") {
        return turf.lineString(coords);
    }

    // ✅ Polygon
    if (type === "Polygon") {

        if (coords.length > 0) {
            const first = coords[0];
            const last = coords[coords.length - 1];

            if (first[0] !== last[0] || first[1] !== last[1]) {
                coords.push(first);
            }
        }

        return turf.polygon([coords]);
    }

    console.warn("Unsupported geometry:", type);
    return null;
}

// ======================================================
// BUFFER PANEL OPEN
// ======================================================
// 
window.createBufferArea = function () {

    if (!selectedFeature) {
        alert("Right-click on a feature first.");
        return;
    }

    const panel = document.getElementById("bufferPanel");

    panel.style.display = "block";
    panel.style.left = "50%";
    panel.style.top = "50%";
    panel.style.transform = "translate(-50%, -50%)";

    document.getElementById("contextMenu").style.display = "none";
};

// ======================================================
// APPLY BUFFER (FIXED TURF + GEOMETRY)
// ======================================================
window.applyBuffer = function () {

    let meters = parseFloat(document.getElementById("bufferDistance").value);

    if (!meters || meters <= 0) {
        alert("Enter valid distance in meters.");
        return;
    }

    if (!selectedFeature) {
        alert("No feature selected.");
        return;
    }

    const geojson = featureToGeoJSON(selectedFeature);

    if (!geojson) {
        alert("Unsupported geometry.");
        return;
    }

    const buffered = turf.buffer(geojson, meters / 1000, {
        units: "kilometers"
    });

    const coords = buffered.geometry.coordinates[0].map(c => ({
        lat: c[1],
        lng: c[0]
    }));

    let newBuffer = new google.maps.Polygon({
        paths: coords,
        map: map,
        fillColor: "#00FF00",
        fillOpacity: 0.25,
        strokeColor: "#008000",
        strokeWeight: 2
    });

    // RIGHT CLICK ON THIS BUFFER
    newBuffer.addListener("rightclick", function (e) {

        selectedBuffer = newBuffer;
        selectedFeature = null;
        rightClickLocation = e.latLng;

        showBufferMenu(e.domEvent.pageX, e.domEvent.pageY);
    });

    buffers.push(newBuffer);

    map.data.overrideStyle(selectedFeature, {
        fillColor: "yellow",
        strokeColor: "red",
        strokeWeight: 3
    });

    closeBufferPanel();
};

// ======================================================
// Clear All Buffer
// ======================================================
window.clearAllBuffers = function () {

    buffers.forEach(buffer => buffer.setMap(null));

    buffers = [];

    map.data.revertStyle();
};

// ======================================================
// CLOSE BUFFER PANEL
// ======================================================
window.closeBufferPanel = function () {

    document.getElementById("bufferPanel").style.display = "none";
    document.getElementById("bufferDistance").value = "";
};

// ======================================================
// RECURSIVE GEOMETRY TRAVERSAL (FIXED)
// ======================================================
function processPoints(geometry, callback) {

    if (!geometry) return;

    if (geometry instanceof google.maps.LatLng) {
        callback(geometry);
        return;
    }

    if (geometry.getArray) {
        geometry.getArray().forEach(g => processPoints(g, callback));
        return;
    }

    if (geometry.getAt) {
        for (let i = 0; i < geometry.getLength(); i++) {
            processPoints(geometry.getAt(i), callback);
        }
    }
}

function showBufferMenu(x, y) {

    const menu = document.getElementById("contextMenu");

    menu.innerHTML = `
        <button class="btn btn-sm btn-danger w-100 mb-1"
            onclick="removeSelectedBuffer()">
            Clear Buffer
        </button>

        <button class="btn btn-sm btn-danger w-100"
            onclick="clearAllBuffers()">
            Clear All Buffers
        </button>
    `;

    menu.style.left = x + "px";
    menu.style.top = y + "px";
    menu.style.display = "block";
}

window.removeSelectedBuffer = function () {

    if (!selectedBuffer) return;

    selectedBuffer.setMap(null);

    buffers = buffers.filter(b => b !== selectedBuffer);

    selectedBuffer = null;

    document.getElementById("contextMenu").style.display = "none";
};