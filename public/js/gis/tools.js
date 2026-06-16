window.GISTools = {


    // LOAD CSV

    loadCSV() {

        const file =
            document.getElementById(
                "csvFile"
            ).files[0];

            GIS.csvFileName =
                file.name.replace(
                    /\.csv$/i,
                    ''
    );

        if (!file) {

            alert(
                "Please select CSV file"
            );

            return;
        }

        GIS.csvPreviousView = {

            center: GIS.map.getCenter(),

            zoom: GIS.map.getZoom()

        };

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

    // CLEAR CSV
    clearCSV() {

        GIS.csvLayers.forEach(layer => {

            GIS.map.removeLayer(layer);

        });

        GIS.csvLayers = [];

        GIS.csvGeoJson = null;

        // Restore previous map view
        if (GIS.csvPreviousView) {

            GIS.map.setView(

                GIS.csvPreviousView.center,

                GIS.csvPreviousView.zoom

            );

        }

        console.log(
            "CSV cleared"
        );
    },

    // RENDER CSV
    renderCSV(data) {

        
        // CLEAR OLD CSV
        
        this.clearCSV();

        const bounds = [];

        const geojson = {

            type: "FeatureCollection",

            features: []

        };

        data.forEach(row => {


            // AUTO DETECT LAT LNG

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


            // RED LOCATION ICON
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

            // CREATE MARKER
            const marker =
                L.marker(
                    [lat, lng],
                    {
                        icon: redIcon
                    }
                );


            // CREATE POPUP
            let popup =
                `
                <div style="
                    min-width:200px;
                    font-family:Arial;
                ">
                    <h5 style="
                        margin:0 0 8px 0;
                        color:#2c3e50;
                    ">
                        📍 ${GIS.csvFileName || 'CSV Feature'}
                    </h5>
                `;

            for (let key in row) {

                popup +=
                    `
                    <div style="
                        font-size:18px;
                        margin-bottom:6px;
                    ">
                        <b>${key}:</b>
                        ${row[key]}
                    </div>
                    `;
            }

            popup += `</div>`;

            marker.bindPopup(popup);

            // ADD TO MAP
            marker.addTo(GIS.map);

            // STORE
            GIS.csvLayers.push(marker);

            bounds.push([lat, lng]);

            geojson.features.push({

                type: "Feature",

                geometry: {

                    type: "Point",

                    coordinates: [
                        lng,
                        lat
                    ]

                },

                properties: row

            });
        });


        // AUTO FIT
        if (bounds.length > 0) {

            GIS.map.fitBounds(bounds, {
                padding: [40, 40]
            });
        }

        GIS.csvGeoJson = geojson;

        console.log(
            "CSV GeoJSON:",
            GIS.csvGeoJson
        );

        alert(
            "CSV loaded successfully"
        );
    },


    // OPEN CSV SAVE MODAL
    openCSVLayerModal() {

        if (!GIS.csvGeoJson) {

            alert(
                "Load a CSV first"
            );

            return;
        }

        const modal =
            new bootstrap.Modal(
                document.getElementById(
                    "csvLayerModal"
                )
            );

        modal.show();
    },


    // SAVE CSV AS GIS LAYER
    async saveCSVLayer() {

        if (!GIS.csvGeoJson) {

            alert(
                "Load a CSV first"
            );

            return;
        }

        const layerName =
            document.getElementById(
                "csvLayerName"
            ).value.trim();

        const projectId =
            document.getElementById(
                "csvProjectId"
            ).value;

        if (!projectId) {

            alert(
                "Select a project"
            );

            return;
        }

        if (!layerName) {

            alert(
                "Enter layer name"
            );

            return;
        }

        try {

            const response =
                await fetch(

                    `/projects/${projectId}/gis/layers`,

                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/json",

                            "X-CSRF-TOKEN":
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content

                        },

                        body: JSON.stringify({

                            layername:
                                layerName,

                            geojson:
                                GIS.csvGeoJson

                        })

                    }

                );

            const result =
                await response.json();

            if (result.success) {

                alert(
                    "CSV layer saved successfully."
                );

                bootstrap.Modal
                    .getInstance(
                        document.getElementById(
                            "csvLayerModal"
                        )
                    )
                    ?.hide();

            } else {

                alert(
                    result.message ||
                    "Save failed."
                );
            }

        } catch (err) {

            console.error(err);

            alert(
                "Failed to save layer."
            );
        }
    },


    // CONVERT SHAPEFILE
    async convertShp() {

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


        // LAYER NAME
        const outputName =
            document.getElementById(
                "geojsonName"
            ).value.trim();

        if (!outputName) {

            alert(
                "Please enter a layer name"
            );

            return;
        }


        // READ FILE

        const reader =
            new FileReader();

        reader.onload =
            async function (e) {

                try {

            
                    // CONVERT SHAPEFILE
            
                    const geojson =
                        await shp(
                            e.target.result
                        );

                    console.log(
                        "Converted GeoJSON:",
                        geojson
                    );

                    // SAVE TO PROJECT GIS
                    const response =
                        await fetch(

                            `/projects/${window.projectId}/gis/layers`,

                            {

                                method: "POST",

                                headers: {

                                    "Content-Type":
                                        "application/json",

                                    "X-CSRF-TOKEN":
                                        document.querySelector(
                                            'meta[name="csrf-token"]'
                                        ).content

                                },

                                body: JSON.stringify({

                                    layername:
                                        outputName,

                                    geojson:
                                        geojson

                                })

                            }

                        );

                    if (!response.ok) {

                        throw new Error(
                            "Server error"
                        );
                    }

                    const result =
                        await response.json();

                    if (result.success) {

                        alert(
                            "Layer uploaded successfully."
                        );

                        window.location.href =
                            `/projects/${window.projectId}/gis`;

                    } else {

                        alert(
                            result.message ||
                            "Upload failed."
                        );
                    }

                } catch (err) {

                    console.error(
                        "SHP conversion/upload error:",
                        err
                    );

                    alert(
                        "Failed to convert or upload shapefile."
                    );
                }

            };

        reader.readAsArrayBuffer(file);
    },
// CSV PART ENDS HERE ----------------------------------------------------------------------------------
// KML PART STARTS HERE --------------------------------------------------------------------------------
    //LOAD KML

    async loadKML() {

        const file =
            document.getElementById(
                "kmlFile"
            ).files[0];

            GIS.kmlFileName =
                file.name.replace(
                    /\.kml$/i,
                    ""
                );

        if (!file) {

            alert(
                "Select KML file"
            );

            return;
        }

        GIS.kmlPreviousView = {

            center:
                GIS.map.getCenter(),

            zoom:
                GIS.map.getZoom()

        };

        const text =
            await file.text();

        const parser =
            new DOMParser();

        const kml =
            parser.parseFromString(
                text,
                "text/xml"
            );

        const geojson =
            toGeoJSON.kml(kml);

        GIS.kmlGeoJson =
            geojson;

        GISTools.renderKML(
            geojson
        );

        //test
        console.log(
    geojson.features
);
    },
    

    // RENDER KML
    renderKML(geojson) {

        this.clearKML();

        const layer = L.geoJSON(

            geojson,

            {

                pointToLayer(feature, latlng) {

                    const redIcon = L.icon({

                        iconUrl:
                            "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",

                        shadowUrl:
                            "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",

                        iconSize: [25, 41],

                        iconAnchor: [12, 41],

                        popupAnchor: [1, -34],

                        shadowSize: [41, 41]

                    });

                    return L.marker(
                        latlng,
                        {
                            icon: redIcon
                        }
                    );
                },

                onEachFeature(
                    feature,
                    layer
                ) {

                    let popup = `

                        <div style="
                            min-width:200px;
                            font-family:Arial;
                        ">

                            <h5 style="
                                margin:0 0 8px 0;
                                color:#2c3e50;
                            ">
                                📍 ${GIS.kmlFileName || 'KML Feature'}
                            </h5>
                    `;

                    if (
                        feature.properties
                    ) {

                        for (
                            let key in feature.properties
                        ) {

                            popup += `

                                <div style="
                                    font-size:18px;
                                    margin-bottom:6px;
                                ">
                                    <b>${key}:</b>
                                    ${feature.properties[key]}
                                </div>
                            `;
                        }
                    }

                    popup += `</div>`;

                    layer.bindPopup(
                        popup
                    );
                }
            }

        ).addTo(
            GIS.map
        );

        GIS.kmlLayers.push(
            layer
        );

        GIS.map.fitBounds(
            layer.getBounds()
        );

        alert(
            "KML loaded successfully"
        );
    },

    // SAVE KML AS LAYER

    async saveKMLLayer() {

        if (!GIS.kmlGeoJson) {

            alert(
                "Load a KML first"
            );

            return;
        }

        const layerName =
            document.getElementById(
                "kmlLayerName"
            ).value.trim();

        const projectId =
            document.getElementById(
                "kmlProjectId"
            ).value;

        if (!projectId) {

            alert(
                "Select a project"
            );

            return;
        }

        if (!layerName) {

            alert(
                "Enter layer name"
            );

            return;
        }

        try {

            const response =
                await fetch(

                    `/projects/${projectId}/gis/layers`,

                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/json",

                            "X-CSRF-TOKEN":
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content

                        },

                        body: JSON.stringify({

                            layername:
                                layerName,

                            geojson:
                                GIS.kmlGeoJson

                        })

                    }

                );

            const result =
                await response.json();

            if (result.success) {

                alert(
                    "KML layer saved successfully."
                );

                bootstrap.Modal
                    .getInstance(
                        document.getElementById(
                            "kmlLayerModal"
                        )
                    )
                    ?.hide();

            } else {

                alert(
                    result.message ||
                    "Save failed."
                );
            }

        } catch (err) {

            console.error(err);

            alert(
                "Failed to save layer."
            );
        }
    },

    clearKML() {

        if (!GIS.kmlLayers) {

            GIS.kmlLayers = [];

            return;
        }

        GIS.kmlLayers.forEach(layer => {

            GIS.map.removeLayer(
                layer
            );

        });

        GIS.kmlLayers = [];

        GIS.kmlGeoJson = null;
    },
};