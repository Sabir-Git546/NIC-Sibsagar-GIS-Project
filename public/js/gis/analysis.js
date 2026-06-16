console.log("analysis.js loaded");

window.GISAnalysis = {

    // =========================
    // STATE
    // =========================
    activeMode: null,
    selectedProjectId: null,

    // =========================
    // BUFFER STATE
    // =========================
    bufferLayers: [],

    selectedLayerForBuffer: null,

    currentBufferedGeoJSON: null,

    currentBufferDistance: null,

    currentSelectedFeature: null,

    // =========================
    // DISTANCE STATE
    // =========================
    measurePoints: [],

    measureLine: null,

    measureMarkers: [],

    measurePopup: null,

    totalDistance: 0,

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

    GIS.map.getContainer().style.cursor = "pointer";

    alert(
        "Click any feature on map to create buffer"
    );

    // =========================
    // ATTACH BUFFER EVENTS
    // =========================
    Object.values(GIS.layers).forEach(layerGroup => {

        if (!layerGroup || !layerGroup.eachLayer) return;

        layerGroup.eachLayer(featureLayer => {

            // remove old buffer event
            featureLayer.off(
                "click",
                GISAnalysis.handleBufferClick
            );

            // attach buffer event
            featureLayer.on(
                "click",
                GISAnalysis.handleBufferClick
            );

        });

    });

    console.log("Buffer analysis started");
},

    // =========================
    // HANDLE BUFFER CLICK
    // =========================
    handleBufferClick: function (e) {

       if (GISAnalysis.activeMode !== "buffer") return;

    const layer = e.propagatedFrom || e.target;

    // =========================
    //  AUTO PROJECT BINDING (ADD THIS)
    // =========================
    if (!GISAnalysis.selectedProjectId) {

        // Try to extract from layer metadata
        console.log("Feature:", layer.feature);
            GISAnalysis.selectedProjectId =
                layer.feature?.properties?.projectid ??
                null;

        console.log(
            "Auto-bound project:",
            GISAnalysis.selectedProjectId
        );
    }

    //  SAFETY CHECK
    if (!GISAnalysis.selectedProjectId) {
        alert("No project context found for this layer");
        return;
    }

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
            // STORE BUFFER DATA
            // =========================
           const originalFeature = layer.toGeoJSON();

            originalFeature.properties = {
                ...(originalFeature.properties || {}),
                feature_type: "source"
            };

            buffered.properties = {
                ...(buffered.properties || {}),
                feature_type: "buffer",
                buffer_distance: distance
            };

            GISAnalysis.currentBufferedGeoJSON = {
                type: "FeatureCollection",
                features: [
                    originalFeature,
                    buffered
                ]
            };

            GISAnalysis.currentBufferDistance =
                distance;

            GISAnalysis.currentSelectedFeature =
                layer;

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

            // =========================
            // SHOW SAVE BUTTON
            // =========================
            document
                .getElementById(
                    "saveLayerBtn"
                )
                .classList.remove(
                    "d-none"
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
    // OPEN SAVE CARD
    // =========================
    openSaveLayerCard() {

        document
            .getElementById(
                "saveLayerCard"
            )
            .classList.remove(
                "d-none"
            );
    },

    // =========================
    // CLOSE SAVE CARD
    // =========================
    closeSaveLayerCard() {

        document
            .getElementById(
                "saveLayerCard"
            )
            .classList.add(
                "d-none"
            );
    },

    // =========================
    // SAVE BUFFER LAYER (UPDATED)
    // =========================
    async saveLayer() {

        console.log("Saving buffer layer...");

        // =========================
        // COLLECT ALL VISIBLE FEATURES
        // =========================
        const allFeatures = [];

        // =========================
        // ORIGINAL GIS LAYERS
        // =========================
        Object.values(GIS.layers).forEach(layerGroup => {

            if (!layerGroup || !layerGroup.eachLayer) return;

            layerGroup.eachLayer(layer => {

                const geojson = layer.toGeoJSON();

                if (geojson.type === "FeatureCollection") {

                    allFeatures.push(
                        ...geojson.features
                    );

                } else {

                    allFeatures.push(
                        geojson
                    );
                }
            });
        });

        // =========================
        // BUFFER LAYERS
        // =========================
        GISAnalysis.bufferLayers.forEach(bufferLayer => {

            if (!bufferLayer || !bufferLayer.eachLayer) return;

            bufferLayer.eachLayer(layer => {

                const geojson = layer.toGeoJSON();

                if (geojson.type === "FeatureCollection") {

                    allFeatures.push(
                        ...geojson.features
                    );

                } else {

                    allFeatures.push(
                        geojson
                    );
                }
            });
        });

        // =========================
        // VALIDATE
        // =========================
        if (allFeatures.length === 0) {

            alert("No GIS features available");

            return;
        }

        // =========================
        // FINAL FEATURE COLLECTION
        // =========================
        const geojsonToSave = {

            type: "FeatureCollection",

            features: allFeatures
        };

        console.log(
            "Saving Features:",
            geojsonToSave.features.length
        );

        const layerName = document.getElementById("newLayerName").value.trim();
        const description = document.getElementById("layerDescription")?.value.trim();

        if (!layerName) {
            alert("Enter layer name");
            return;
        }

        // =========================
        // VALIDATE PROJECT
        // =========================
        const projectId = this.selectedProjectId;

        if (!projectId) {
            alert("No project selected");
            return;
        }

        try {

            const response = await fetch(`/projects/${projectId}/gis/layers`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({

                    layername: layerName,

                    geojson: geojsonToSave,

                    layer_type: "analysis",

                    bufferdistance: this.currentBufferDistance || 0
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Save failed");
            }

            if (data.success) {

                alert("Buffered layer saved successfully");

                this.closeSaveLayerCard();
            }

        } catch (err) {

            console.error(err);

            alert(err.message || "Unexpected error");
        }
    },

    // =========================
    // OPEN SAVE LAYER CARD
    // =========================
    openSaveLayerCard() {
        document.getElementById("saveLayerCard").classList.remove("d-none");
    },

    closeSaveLayerCard() {
        document.getElementById("saveLayerCard").classList.add("d-none");
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
        // HIDE SAVE BUTTON
        // =========================
        document
            .getElementById(
                "saveLayerBtn"
            )
            .classList.add(
                "d-none"
            );

        // =========================
        // RESET BUFFER STATE
        // =========================
       /* this.currentBufferedGeoJSON =
            null;

        this.currentBufferDistance =
            null;

        this.currentSelectedFeature =
            null;   */

        // =========================
        // RESET DISTANCE STATE
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