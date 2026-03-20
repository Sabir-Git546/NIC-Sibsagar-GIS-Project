let map;
let csvMarkers = [];
let csvInfoWindow;

// GIS Layers
let activeLayers = {};

// ===============================
// INITIALIZE MAP
// ===============================

window.initMap = function () {

    const center = { lat: 26.9891, lng: 94.6394 };

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: center,
        mapTypeId: "roadmap"
    });

    csvInfoWindow = new google.maps.InfoWindow();

    //
    map.data.addListener('click', function(event) {

        let content = "<div style='max-height:200px;overflow:auto;'>";

        event.feature.forEachProperty(function(value, key) {

            content += "<b>" + key + ":</b> " + value + "<br>";

        });

        content += "</div>";

        csvInfoWindow.setPosition(event.latLng);
        csvInfoWindow.setContent(content);
        csvInfoWindow.open(map);

    });

    // Layer checkbox listener
    document.querySelectorAll(".layer-checkbox").forEach(cb => {

        cb.addEventListener("change", function () {

            let layer = this.value;

            if (this.checked) {
                loadLayer(layer);
            } else {
                removeLayer(layer);
            }

        });

    });

};


// ===============================
// UPDATE MAP BOUNDS (CSV + LAYERS)
// ===============================

function updateMapBounds(){

    let bounds = new google.maps.LatLngBounds();

    // Include CSV markers
    csvMarkers.forEach(marker => {
        bounds.extend(marker.getPosition());
    });

    // Include GIS layers
    map.data.forEach(function(feature){
        processPoints(feature.getGeometry(), bounds.extend, bounds);
    });

    if(!bounds.isEmpty()){
        map.fitBounds(bounds);

        // Prevent too much zoom
        google.maps.event.addListenerOnce(map, 'bounds_changed', function () {
            if (map.getZoom() > 16) {
                map.setZoom(16);
            }
        });
    }
}


// ===============================
// CSV FILE VIEWER
// ===============================

function loadCSV() {

    const fileInput = document.getElementById("csvFile");
    const file = fileInput.files[0];

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

                // Auto detect lat/lng column names
                let lat = row.latitude || row.lat || row.Latitude || row.Lat;
                let lng = row.longitude || row.lng || row.Longitude || row.Lng;

                lat = parseFloat(lat);
                lng = parseFloat(lng);

                if (!isNaN(lat) && !isNaN(lng)) {

                    let position = { lat: lat, lng: lng };

                    let marker = new google.maps.Marker({
                        position: position,
                        map: map
                    });

                    validPoints++;

                    let content = "<div style='max-height:200px;overflow:auto;'>";

                    for (let key in row) {
                        content += "<b>" + key + ":</b> " + row[key] + "<br>";
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

                if (validPoints === 1) {
                    map.setZoom(15);
                }

                alert("CSV Loaded Successfully!");

            } else {

                alert("No valid latitude/longitude columns found.");

            }

        }
    });
}


function clearCSV() {

    csvMarkers.forEach(marker => marker.setMap(null));
    csvMarkers = [];

    if (csvInfoWindow) {
        csvInfoWindow.close();
    }

    updateMapBounds();
}


// ===============================
// LOAD GIS LAYER FROM DATABASE
// ===============================

function loadLayer(layername) {

    fetch("/gis/layer/" + layername)

    .then(res => res.json())

    .then(data => {

        let features = map.data.addGeoJson(data);

        activeLayers[layername] = features;

        updateMapBounds();

    })

    .catch(err => console.error("Layer load error:", err));

}


// ===============================
// REMOVE GIS LAYER
// ===============================

function removeLayer(layername) {

    map.data.forEach(function(feature){

        if(feature.getProperty("layername") === layername){
            map.data.remove(feature);
        }

    });

    updateMapBounds();

}


// ===============================
// PROCESS GEOMETRY POINTS
// ===============================

function processPoints(geometry, callback, thisArg) {

    if (geometry instanceof google.maps.LatLng) {

        callback.call(thisArg, geometry);

    }

    else if (geometry instanceof google.maps.Data.Point) {

        callback.call(thisArg, geometry.get());

    }

    else {

        geometry.getArray().forEach(g => {
            processPoints(g, callback, thisArg);
        });

    }

}


// ===============================
// SIDEBAR CONTROL PANEL
// ===============================

function closePanel() {

    const panel = document.getElementById("controlPanel");

    panel.classList.add("collapsed");

    document.getElementById("openPanelBtn").style.display = "block";

    setTimeout(() => {

        if (map) {

            google.maps.event.trigger(map, "resize");

            map.setCenter({ lat: 26.9891, lng: 94.6394 });

        }

    }, 310);

}

function openPanel() {

    const panel = document.getElementById("controlPanel");

    panel.classList.remove("collapsed");

    document.getElementById("openPanelBtn").style.display = "none";

    setTimeout(() => {

        if (map) {

            google.maps.event.trigger(map, "resize");

            map.setCenter({ lat: 26.9891, lng: 94.6394 });

        }

    }, 310);

}


// ===============================
// SHAPEFILE → GEOJSON CONVERTER
// ===============================

async function convertAndSave() {

    const fileInput = document.getElementById('zipFile');
    const nameInput = document.getElementById('filename').value.trim();

    if (!fileInput.files.length) {

        alert("Please select a shapefile ZIP");

        return;

    }

    if (!nameInput) {

        alert("Please enter a file name");

        return;

    }

    const file = fileInput.files[0];
    const finalName = nameInput + '.geojson';

    try {

        const buffer = await file.arrayBuffer();

        const geojson = await shp(buffer);

        const geojsonStr = JSON.stringify(geojson);

        const blob = new Blob([geojsonStr], { type: "application/json" });

        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");

        a.href = url;
        a.download = finalName;

        document.body.appendChild(a);

        a.click();

        document.body.removeChild(a);

        URL.revokeObjectURL(url);

        alert("Saved successfully! Check your Downloads folder.");

    }

    catch (error) {

        console.error("Shapefile conversion error:", error);

        alert("Conversion failed. Check console.");

    }

}


// ===============================
// LAYER SEARCH + DEPARTMENT FILTER
// ===============================

function filterLayers() {

    let searchText = document
        .getElementById("layerSearch")
        .value
        .toLowerCase();

    let deptSelected = document
        .getElementById("deptFilter")
        .value;

    let items = document.querySelectorAll(".layer-item");

    items.forEach(item => {

        let label = item.innerText.toLowerCase();

        let dept = item.getAttribute("data-dept");

        let matchSearch = label.includes(searchText);

        let matchDept = !deptSelected || dept === deptSelected;

        if (matchSearch && matchDept) {

            item.style.display = "";

        } else {

            item.style.display = "none";

        }

    });

}