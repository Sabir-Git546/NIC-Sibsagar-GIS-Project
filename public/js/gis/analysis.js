console.log("analysis.js loaded");

window.GISAnalysis = {

    // =========================
    // STATE
    // =========================
    activeMode: null,

    // =========================
    // DISTANCE STATE
    // =========================
    measurePoints: [],

    measureLine: null,

    measureMarkers: [],

    measurePopup: null,

    totalDistance: 0,

    // =========================
    // BUFFER STATE
    // =========================
    bufferLayers: [],

    selectedLayerForBuffer: null,

    // =========================
    // START ANALYSIS
    // =========================
    startSelectedAnalysis() {

        console.log(
            "Run Analysis clicked"
        );

        const mode =
            document.getElementById(
                "analysisType"
            ).value;

        console.log("Mode:", mode);

        if (!mode) {

            alert(
                "Select analysis type"
            );

            return;
        }

        this.start(mode);
    },

    // =========================
    // START MODE
    // =========================
    start(mode) {

        this.clearAnalysis();

        GIS.clearInteractions();

        this.activeMode = mode;

        GIS.interaction.activeTool =
            mode;

        switch (mode) {

            case "distance":

                this.startDistanceAnalysis();

                break;

            case "buffer":

                this.startBufferAnalysis();

                break;

            case "overlap":

                this.startOverlapAnalysis();

                break;

            case "area":

                this.startAreaAnalysis();

                break;

            case "perimeter":

                this.startPerimeterAnalysis();

                break;
        }
    },

    // =========================
    // DISTANCE ANALYSIS
    // =========================
    startDistanceAnalysis() {

        GIS.map.getContainer().style.cursor =
            "crosshair";

        GIS.map.on(
            "click",
            GISAnalysis.addMeasurePoint
        );

        GIS.map.on(
            "dblclick",
            GISAnalysis.stopDistanceAnalysis
        );

        console.log(
            "Distance analysis started"
        );
    },

    // =========================
    // ADD MEASURE POINT
    // =========================
    addMeasurePoint: function (e) {

        if (
            GISAnalysis.activeMode !==
            "distance"
        ) return;

        const latlng = e.latlng;

        GISAnalysis.measurePoints.push(
            latlng
        );

        // =========================
        // ADD MARKER
        // =========================
        const marker =
            L.circleMarker(latlng, {

                radius: 5,

                color: "blue",

                fillColor: "white",

                fillOpacity: 1,

                weight: 2

            }).addTo(GIS.map);

        GISAnalysis.measureMarkers.push(
            marker
        );

        // =========================
        // CREATE / UPDATE LINE
        // =========================
        if (GISAnalysis.measureLine) {

            GISAnalysis.measureLine.setLatLngs(
                GISAnalysis.measurePoints
            );

        } else {

            GISAnalysis.measureLine =
                L.polyline(
                    GISAnalysis.measurePoints,
                    {
                        color: "blue",
                        weight: 4
                    }
                ).addTo(GIS.map);

            // =========================
            // CLICKABLE LINE
            // =========================
            GISAnalysis.measureLine.on(
                "click",
                function (e) {

                    let text =
                        GISAnalysis.totalDistance < 1000
                            ? GISAnalysis.totalDistance.toFixed(2) + " m"
                            : (GISAnalysis.totalDistance / 1000).toFixed(2) + " km";

                    L.popup()

                        .setLatLng(e.latlng)

                        .setContent(
                            `
                            <div style="
                                min-width:180px;
                                font-family:Arial;
                            ">
                                <h4 style="
                                    margin:0 0 8px 0;
                                    color:#2c3e50;
                                ">
                                    📏 Distance Analysis
                                </h4>

                                <b>Total Distance:</b><br>
                                ${text}
                            </div>
                            `
                        )

                        .openOn(GIS.map);
                }
            );
        }

        GISAnalysis.updateDistance();
    },

    // =========================
    // UPDATE DISTANCE
    // =========================
    updateDistance() {

        if (
            this.measurePoints.length < 2
        ) return;

        let distance = 0;

        for (
            let i = 1;
            i < this.measurePoints.length;
            i++
        ) {

            distance +=
                this.measurePoints[i - 1]
                .distanceTo(
                    this.measurePoints[i]
                );
        }

        this.totalDistance = distance;

        let text =
            distance < 1000
                ? `${distance.toFixed(2)} m`
                : `${(distance / 1000).toFixed(2)} km`;

        // =========================
        // LIVE POPUP
        // =========================
        if (!this.measurePopup) {

            this.measurePopup = L.popup()

                .setLatLng(
                    this.measurePoints[
                        this.measurePoints.length - 1
                    ]
                )

                .setContent(
                    `
                    <div style="
                        min-width:180px;
                        font-family:Arial;
                    ">
                        <h4 style="
                            margin:0 0 8px 0;
                            color:#2c3e50;
                        ">
                            📏 Measuring
                        </h4>

                        <b>Current Distance:</b><br>
                        ${text}
                    </div>
                    `
                )

                .openOn(GIS.map);

        } else {

            this.measurePopup

                .setLatLng(
                    this.measurePoints[
                        this.measurePoints.length - 1
                    ]
                )

                .setContent(
                    `
                    <div style="
                        min-width:180px;
                        font-family:Arial;
                    ">
                        <h4 style="
                            margin:0 0 8px 0;
                            color:#2c3e50;
                        ">
                            📏 Measuring
                        </h4>

                        <b>Current Distance:</b><br>
                        ${text}
                    </div>
                    `
                )

                .openOn(GIS.map);
        }
    },

    // =========================
    // STOP DISTANCE
    // =========================
    stopDistanceAnalysis: function () {

        GIS.clearInteractions();

        GISAnalysis.activeMode = null;

        GIS.map.getContainer().style.cursor = "";

        let finalText =
            GISAnalysis.totalDistance < 1000
                ? GISAnalysis.totalDistance.toFixed(2) + " m"
                : (GISAnalysis.totalDistance / 1000).toFixed(2) + " km";

        if (GISAnalysis.measurePopup) {

            GISAnalysis.measurePopup

                .setContent(
                    `
                    <div style="
                        min-width:180px;
                        font-family:Arial;
                    ">
                        <h4 style="
                            margin:0 0 8px 0;
                            color:#2c3e50;
                        ">
                            ✅ Final Distance
                        </h4>

                        <b>Total Distance:</b><br>
                        ${finalText}
                    </div>
                    `
                )

                .openOn(GIS.map);
        }

        console.log(
            "Distance analysis completed"
        );
    },

    // =========================
    // BUFFER ANALYSIS
    // =========================
    startBufferAnalysis() {

        GIS.map.getContainer().style.cursor =
            "pointer";

        alert(
            "Click any feature on map to create buffer"
        );

        // =========================
        // ATTACH CLICK EVENTS
        // =========================
        Object.values(GIS.layers).forEach(layerGroup => {

            layerGroup.eachLayer(layer => {

                layer.off(
                    "click",
                    GISAnalysis.handleBufferClick
                );

                layer.on(
                    "click",
                    GISAnalysis.handleBufferClick
                );
            });
        });

        console.log(
            "Buffer analysis started"
        );
    },

    // =========================
    // HANDLE BUFFER CLICK
    // =========================
    handleBufferClick: function (e) {

        if (
            GISAnalysis.activeMode !==
            "buffer"
        ) return;

        const layer = e.target;

        // =========================
        // ASK DISTANCE
        // =========================
        const distance = prompt(
            "Enter buffer distance in meters",
            "100"
        );

        if (!distance) return;

        try {

            // =========================
            // GET GEOJSON
            // =========================
            const geojson =
                layer.toGeoJSON();

            // =========================
            // CREATE BUFFER
            // =========================
            const buffered =
                turf.buffer(
                    geojson,
                    parseFloat(distance) / 1000,
                    {
                        units: "kilometers"
                    }
                );

            // =========================
            // RENDER BUFFER
            // =========================
            const bufferLayer =
                L.geoJSON(buffered, {

                    style: {

                        color: "#0066ff",

                        weight: 2,

                        fillColor: "#4da3ff",

                        fillOpacity: 0.4
                    }

                }).addTo(GIS.map);

            // =========================
            // STORE BUFFER
            // =========================
            GISAnalysis.bufferLayers.push(
                bufferLayer
            );

            GIS.analysis.buffers.push(
                bufferLayer
            );

            // =========================
            // POPUP
            // =========================
            bufferLayer.bindPopup(
                `
                <div style="
                    min-width:180px;
                    font-family:Arial;
                ">

                    <h4 style="
                        margin:0 0 8px 0;
                        color:#2c3e50;
                    ">
                        🔵 Buffer Analysis
                    </h4>

                    <b>Buffer Distance:</b><br>
                    ${distance} meters

                </div>
                `
            );

            console.log(
                "Buffer created"
            );

        } catch (err) {

            console.error(
                "Buffer error:",
                err
            );

            alert(
                "Buffer creation failed"
            );
        }
    },

    // =========================
    // OVERLAP
    // =========================
    startOverlapAnalysis() {

        alert(
            "Overlap analysis mode started"
        );

        console.log(
            "Overlap analysis started"
        );
    },

    // =========================
    // AREA
    // =========================
    startAreaAnalysis() {

        alert(
            "Area analysis mode started"
        );

        console.log(
            "Area analysis started"
        );
    },

    // =========================
    // PERIMETER
    // =========================
    startPerimeterAnalysis() {

        alert(
            "Perimeter analysis mode started"
        );

        console.log(
            "Perimeter analysis started"
        );
    },

    // =========================
    // CLEAR ANALYSIS
    // =========================
    clearAnalysis() {

        GIS.clearInteractions();

        // =========================
        // REMOVE MEASURE LINE
        // =========================
        if (this.measureLine) {

            GIS.map.removeLayer(
                this.measureLine
            );

            this.measureLine = null;
        }

        // =========================
        // REMOVE MEASURE MARKERS
        // =========================
        this.measureMarkers.forEach(marker => {

            GIS.map.removeLayer(marker);

        });

        this.measureMarkers = [];

        // =========================
        // REMOVE POPUP
        // =========================
        if (this.measurePopup) {

            GIS.map.closePopup(
                this.measurePopup
            );

            this.measurePopup = null;
        }

        // =========================
        // REMOVE BUFFERS
        // =========================
        this.bufferLayers.forEach(layer => {

            GIS.map.removeLayer(layer);

        });

        this.bufferLayers = [];

        // =========================
        // RESET STATE
        // =========================
        this.measurePoints = [];

        this.totalDistance = 0;

        this.activeMode = null;

        GIS.interaction.activeTool =
            null;

        GIS.map.getContainer().style.cursor =
            "";

        console.log(
            "Analysis cleared"
        );
    }
};