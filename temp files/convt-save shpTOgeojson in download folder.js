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

        // Convert geojson to string
        const geojsonStr = JSON.stringify(geojson);

        // Create a Blob
        const blob = new Blob([geojsonStr], { type: "application/json" });

        // Create a download link
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = finalName; // filename for download
        document.body.appendChild(a);
        a.click();

        // Clean up
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        alert("Saved successfully! Check your Downloads folder.");

    } catch (error) {
        console.error("Shapefile conversion error:", error);
        alert("Conversion failed. Check console for details.");
    }
}