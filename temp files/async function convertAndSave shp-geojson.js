async function convertAndSave() {

    const fileInput = document.getElementById('zipFile'); // match the HTML id
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
                'X-CSRF-TOKEN': window.Laravel.csrfToken
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
            status.innerText = " ";
            alert("Saved successfully!");
        } else {
            alert("Error: " + data.message);
        }

    } catch (error) {
        console.error("Shapefile conversion error:", error);
        alert("Conversion failed. Check console for details.");
        status.innerHTML = `<span class="text-danger">Conversion failed. See console.</span>`;
    }
}