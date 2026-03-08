<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Converting Shape File to GeoJSON File</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- shpjs -->
    <script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
</head>
<body>

<div class="container" style="margin-top:40px; max-width:600px;">

    <h3 class="text-center">Shapefile → GeoJSON Converter</h3>
    <hr>

    <!-- Shapefile ZIP -->
    <div class="form-group">
        <label>Select Shapefile (.zip)</label>
        <input type="file" id="shapefile" class="form-control" accept=".zip">
    </div>

    <!-- New file name -->
    <div class="form-group">
        <label>GeoJSON File Name</label>
        <input type="text" id="filename" class="form-control"
               placeholder="e.g. Maidam_Area">
        <small class="text-muted">.geojson will be added automatically</small>
    </div>

    <!-- Convert button -->
    <button class="btn btn-primary btn-block" onclick="convertAndSave()">
        Convert & Save
    </button>
    <button type="button" class="btn btn-success btn-block" onclick="window.location.href='{{ url('/') }}'">
        Home
    </button>

    <br>

    <!-- Status -->
    <p id="status" class="text-info text-center"></p>

</div>

<script>
async function convertAndSave() {

    const fileInput = document.getElementById('shapefile');
    const nameInput = document.getElementById('filename').value.trim();
    const status = document.getElementById('status');

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

    status.innerText = "Reading ZIP file...";

    try {
        // ✅ Read file as ArrayBuffer
        const buffer = await file.arrayBuffer();

        status.innerText = "Converting shapefile to GeoJSON...";

        // ✅ Convert using shpjs
        const geojson = await shp(buffer);

        status.innerText = "Saving GeoJSON file...";

        // Send to Laravel
        const response = await fetch('/save-geojson', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                filename: finalName,
                geojson: geojson
            })
        });

        const text = await response.text();
        console.log("Server response:", text);

        const data = JSON.parse(text);

        if (data.success) {
            status.innerHTML = `<b>Saved successfully:</b> ${data.path}`;
        } else {
            status.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }

    } catch (error) {
        console.error("Shapefile conversion error:", error);
        alert("Conversion failed. Check console for details.");
    }
}
</script>


</body>
</html>
