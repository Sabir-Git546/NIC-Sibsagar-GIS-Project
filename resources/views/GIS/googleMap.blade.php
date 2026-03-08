<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Project Demo</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        #map {
            height: 600px;
            width: 95%;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<!-- Heading -->
<div class="container-fluid text-center mt-4">
    <div class="container-md bg-primary p-4 rounded shadow-sm">
        <h3 class="text-white fw-bold mb-0">
            Google Map View
        </h3>
    </div>
</div>

<!-- Description -->
<div class="container-md text-left p-4 rounded shadow-sm">
    <p class="text-black" style="font-size:14px">
        This is a page dedicated Google map view.
    </p>
</div>

<!-- Back Button -->
<div class="container-fluid text-center mt-4">
    <a href="{{ route('dashboard') }}" class="btn btn-info">
        Back to Dashboard
    </a>
</div>

<!--upload GeoJSON and .CSV file here and view-->


<!--Select the desired file-->
<div class="container text-center" style="margin-top:15px;">
    <label><b>Select GeoJSON Layer</b></label>
    <select id="geojsonSelect" class="form-control" style="max-width:300px; margin:auto;">
        <option value="">-- Select GeoJSON file --</option>
        @foreach ($files as $file)
            <option value="{{ $file }}">
                {{ pathinfo($file, PATHINFO_FILENAME) }}
            </option>
        @endforeach

    </select>
</div>



<!-- Google Map -->
<div id="map"></div>

<script>
let map;   // make map global

function initMap() {

    // Default center location
    const center = { lat: 26.9891, lng: 94.6394 };

    // Create map
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: center
    });

    // Load GeoJSON file from public/geojson folder throgh the select option
    document.getElementById('geojsonSelect').addEventListener('change', function () {

        const fileName = this.value;
        if (!fileName) return;

        // Remove existing GeoJSON
        map.data.forEach(function (feature) {
            map.data.remove(feature);
        });

        // Creating bounds for map reload
        const bounds = new google.maps.LatLngBounds();

        // Load selected GeoJSON WITH callback
        map.data.loadGeoJson(`/geojson/${fileName}`, null, function (features) {

            features.forEach(function (feature) {
                processGeometry(feature.getGeometry(), bounds);
            });

            // Auto zoom & center to GeoJSON
            map.fitBounds(bounds);
        });
        // Style GeoJSON
        map.data.setStyle({
            fillColor: '#00FF00',
            strokeColor: '#006400',
            strokeWeight: 2,
            fillOpacity: 0.35
        });
    });

   
    // plotting the actual location on the map
    map.data.addListener('click', function (event) {

        let content = '<b>Feature Info</b><br>';
        event.feature.forEachProperty(function (value, key) {
            content += `<b>${key}</b>: ${value}<br>`;
        });

        const infoWindow = new google.maps.InfoWindow({
            content: content,
            position: event.latLng
        });

        infoWindow.open(map);
    });
}

    // ---- Geometry processor (handles Point / Line / Polygon / MultiPolygon) ----
    function processGeometry(geometry, bounds) {

        if (geometry instanceof google.maps.Data.Point) {
            bounds.extend(geometry.get());

        } else if (geometry instanceof google.maps.Data.MultiPoint ||
                geometry instanceof google.maps.Data.LineString) {

            geometry.getArray().forEach(coord => bounds.extend(coord));

        } else if (geometry instanceof google.maps.Data.MultiLineString ||
                geometry instanceof google.maps.Data.Polygon) {

            geometry.getArray().forEach(path => {
                path.getArray().forEach(coord => bounds.extend(coord));
            });

        } else if (geometry instanceof google.maps.Data.MultiPolygon) {

            geometry.getArray().forEach(polygon => {
                polygon.getArray().forEach(path => {
                    path.getArray().forEach(coord => bounds.extend(coord));
                });
            });
        }
    }
</script>

<!-- Google Maps API -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9mTWnANz3Mm-Km933gvoxOv5Wp57P3NM&libraries=geometry&callback=initMap"
    async defer>
</script>

</body>
</html>
