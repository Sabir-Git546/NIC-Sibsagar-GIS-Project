window.GISTools = {

    // =========================
    // LOAD CSV
    // =========================
    loadCSV() {

        const file =
            document.getElementById(
                "csvFile"
            ).files[0];

        if (!file) {

            alert(
                "Please select CSV file"
            );

            return;
        }

        Papa.parse(file, {

            header: true,

            skipEmptyLines: true,

            complete: function (results) {

                console.log(
                    "CSV Parsed:",
                    results.data
                );

                GISTools.renderCSV(
                    results.data
                );
            }

        });
    },

    // =========================
    // RENDER CSV
    // =========================
    renderCSV(data) {

        // =========================
        // CLEAR OLD CSV
        // =========================
        this.clearCSV();

        const bounds = [];

        data.forEach(row => {

            // =========================
            // AUTO DETECT LAT LNG
            // =========================
            const lat =
                parseFloat(
                    row.lat ||
                    row.latitude ||
                    row.Latitude
                );

            const lng =
                parseFloat(
                    row.lng ||
                    row.lon ||
                    row.longitude ||
                    row.Longitude
                );

            // INVALID COORDS
            if (
                isNaN(lat) ||
                isNaN(lng)
            ) return;

           // =========================
            // RED LOCATION ICON
            // =========================
            const redIcon = L.icon({

                iconUrl:
                    "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",

                shadowUrl:
                    "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",

                iconSize: [25, 41],

                iconAnchor: [12, 41],

                popupAnchor: [1, -34],

                shadowSize: [41, 41]
            });

            // =========================
            // CREATE MARKER
            // =========================
            const marker =
                L.marker(
                    [lat, lng],
                    {
                        icon: redIcon
                    }
                );

            // =========================
            // CREATE POPUP
            // =========================
            let popup =
                `
                <div style="
                    min-width:200px;
                    font-family:Arial;
                ">
                    <h4 style="
                        margin:0 0 8px 0;
                        color:#2c3e50;
                    ">
                        📄 CSV Feature
                    </h4>
                `;

            for (let key in row) {

                popup +=
                    `
                    <div style="
                        font-size:12px;
                        margin-bottom:4px;
                    ">
                        <b>${key}:</b>
                        ${row[key]}
                    </div>
                    `;
            }

            popup += `</div>`;

            marker.bindPopup(popup);

            // =========================
            // ADD TO MAP
            // =========================
            marker.addTo(GIS.map);

            // =========================
            // STORE
            // =========================
            GIS.csvLayers.push(marker);

            bounds.push([lat, lng]);
        });

        // =========================
        // AUTO FIT
        // =========================
        if (bounds.length > 0) {

            GIS.map.fitBounds(bounds, {
                padding: [40, 40]
            });
        }

        alert(
            "CSV loaded successfully"
        );
    },

    // =========================
    // CLEAR CSV
    // =========================
    clearCSV() {

        GIS.csvLayers.forEach(layer => {

            GIS.map.removeLayer(layer);

        });

        GIS.csvLayers = [];

        console.log(
            "CSV cleared"
        );
    },

    // =========================
    // CONVERT SHAPEFILE
    // =========================
    convertShp() {

        const file =
            document.getElementById(
                "zipFile"
            ).files[0];

        if (!file) {

            alert(
                "Please select ZIP shapefile"
            );

            return;
        }

        // =========================
        // OUTPUT FILE NAME
        // =========================
        const outputName =
            document.getElementById(
                "geojsonName"
            ).value || "converted";

        // =========================
        // READ FILE
        // =========================
        const reader =
            new FileReader();

        reader.onload =
            async function (e) {

                try {

                    // =========================
                    // PARSE SHP ZIP
                    // =========================
                    const geojson =
                        await shp(
                            e.target.result
                        );

                    console.log(
                        "Converted GeoJSON:",
                        geojson
                    );

                    // =========================
                    // SHOW ON MAP
                    // =========================
                    const layer =
                        L.geoJSON(
                            geojson,
                            {

                                style: {

                                    color: "#28a745",

                                    weight: 2,

                                    fillOpacity: 0.3
                                },

                                onEachFeature:
                                    function (
                                        feature,
                                        layer
                                    ) {

                                        let popup =
                                            `
                                            <div style="
                                                min-width:200px;
                                                font-family:Arial;
                                            ">
                                                <h4 style="
                                                    margin:0 0 8px 0;
                                                    color:#2c3e50;
                                                ">
                                                    🗂 Converted Feature
                                                </h4>
                                            `;

                                        const props =
                                            feature.properties || {};

                                        for (
                                            let key in props
                                        ) {

                                            popup +=
                                                `
                                                <div style="
                                                    font-size:12px;
                                                    margin-bottom:4px;
                                                ">
                                                    <b>${key}:</b>
                                                    ${props[key]}
                                                </div>
                                                `;
                                        }

                                        popup +=
                                            `</div>`;

                                        layer.bindPopup(
                                            popup
                                        );
                                    }
                            }

                        ).addTo(GIS.map);

                    // =========================
                    // FIT BOUNDS
                    // =========================
                    GIS.map.fitBounds(
                        layer.getBounds(),
                        {
                            padding: [40, 40]
                        }
                    );

                    // =========================
                    // DOWNLOAD GEOJSON
                    // =========================
                    const blob =
                        new Blob(
                            [
                                JSON.stringify(
                                    geojson,
                                    null,
                                    2
                                )
                            ],
                            {
                                type:
                                    "application/json"
                            }
                        );

                    const url =
                        URL.createObjectURL(
                            blob
                        );

                    const a =
                        document.createElement(
                            "a"
                        );

                    a.href = url;

                    a.download =
                        outputName + ".geojson";

                    document.body.appendChild(
                        a
                    );

                    a.click();

                    document.body.removeChild(
                        a
                    );

                    URL.revokeObjectURL(
                        url
                    );

                    alert(
                        "Shapefile converted successfully"
                    );

                } catch (err) {

                    console.error(
                        "SHP conversion error:",
                        err
                    );

                    alert(
                        "Invalid shapefile ZIP"
                    );
                }
            };

        reader.readAsArrayBuffer(file);
    },
};