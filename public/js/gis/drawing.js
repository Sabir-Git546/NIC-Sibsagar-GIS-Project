window.GISDrawing = {

    activeLayer: null,

    drawnFeatures: [],

    pendingFeature: null,

    drawnLayers: [],

    pendingLatLng: null,

    linePoints: [],

    tempLine: null,

    polygonPoints: [],

    tempPolygon: null,

    completedLineCount: 0,

    drawingMode: false,

    currentProjectId: null,

    currentLayerName: null,

    currentGeometryType: null,


    openCreateLayerModal() {

        document
            .getElementById(
                "createLayerModal"
            )
            .classList.remove("d-none");

        this.loadProjects();
    },

    closeCreateLayerModal() {

        document
            .getElementById(
                "createLayerModal"
            )
            .classList.add("d-none");
    },

    async loadProjects() {

        try {

            const response =
                await fetch('/gis/projects');

            console.log(
                "Response:",
                response.status
            );

            const projects =
                await response.json();

            console.log(
                "Projects:",
                projects
            );

            const select =
                document.getElementById(
                    "drawingProject"
                );

            select.innerHTML = "";

            projects.forEach(project => {

                select.innerHTML += `
                    <option value="${project.projectid}">
                        ${project.projectname}
                    </option>
                `;
            });

        } catch (err) {

            console.error(
                "Project load error:",
                err
            );
        }
    },

    createLayer() {

        const projectId =
            document.getElementById(
                "drawingProject"
            ).value;

        const layerName =
            document.getElementById(
                "drawingLayerName"
            ).value;

        const geometryType =
            document.getElementById(
                "drawingGeometryType"
            ).value;

        if (!projectId || !layerName) {

            alert(
                "Project and Layer Name are required"
            );

            return;
        }

        this.currentProjectId =
            projectId;

        this.currentLayerName =
            layerName;

        this.currentGeometryType =
            geometryType;

        this.drawingMode = true;

        this.activeLayer = {

            projectId,
            layerName,
            geometryType
        };

        const btn =
            document.getElementById(
                "createLayerBtn"
            );

        btn.innerText =
            "Cancel Drawing";

        btn.classList.remove(
            "btn-success"
        );

        btn.classList.add(
            "btn-danger"
        );

        btn.onclick = () =>
            GISDrawing.cancelDrawing();

        this.startDrawing();

        document
            .getElementById(
                "saveDrawLayerBtn"
            )
            .classList.remove(
                "d-none"
            );

        if (geometryType === "LineString" || geometryType === "Polygon") {

            document
                .getElementById(
                    "finishLineBtn"
                )
                .classList.remove(
                    "d-none"
                );
        }

        this.closeCreateLayerModal();

        if (geometryType === "Point") {

            alert(
                "Click map to add points"
            );
        }
        else if (geometryType === "LineString") {

            alert(
                "Click map to add vertices. Press Finish Drawing when completed."
            );
        }
        else if (geometryType === "Polygon") {

            alert(
                "Click map to add vertices. Press Finish Drawing when completed."
            );
        }
    },

    startDrawing() {

        GIS.clearInteractions();

        GIS.interaction.activeTool =
            "drawing";

        GIS.map.getContainer().style.cursor =
            "crosshair";

        const type =
            this.activeLayer.geometryType;

        if (type === "Point") {

            GIS.map.on(
                "click",
                this.handlePointClick
            );

        }
        else if (type === "LineString") {

            GIS.map.doubleClickZoom.disable();

            GIS.map.on(
                "click",
                this.handleLineClick
            );

        }
        else if (type === "Polygon") {

            GIS.map.on(
                "click",
                this.handlePolygonClick
            );

            document
                .getElementById(
                    "finishLineBtn"
                )
                .classList.remove(
                    "d-none"
                );
        }
        else {

            alert(
                `${type} drawing coming next`
            );
        }
    },

    cancelDrawing() {

        if (
            !confirm(
                "Discard all unsaved features?"
            )
        ) {
            return;
        }

        GIS.clearInteractions();

        this.drawnLayers.forEach(layer => {

            GIS.map.removeLayer(
                layer
            );

        });

        this.drawnLayers = [];

        this.drawnFeatures = [];

        this.pendingLatLng = null;

        this.activeLayer = null;

        this.currentProjectId = null;

        this.currentLayerName = null;

        this.currentGeometryType = null;

        this.drawingMode = false;

        if (this.tempLine) {

            GIS.map.removeLayer(
                this.tempLine
            );
        }

        this.linePoints = [];

        this.tempLine = null;

        if (this.tempPolygon) {

            GIS.map.removeLayer(
                this.tempPolygon
            );
        }

        this.polygonPoints = [];

        this.tempPolygon = null;

        this.completedLineCount = 0;

        document
            .getElementById(
                "saveDrawLayerBtn"
            )
            .classList.add(
                "d-none"
            );
        document
            .getElementById(
                "finishLineBtn"
            )
            .classList.add(
                "d-none"
            );

        const btn =
            document.getElementById(
                "createLayerBtn"
            );

        btn.innerText =
            "+ Create Layer";

        btn.classList.remove(
            "btn-danger"
        );

        btn.classList.add(
            "btn-success"
        );

        btn.onclick = () =>
            GISDrawing.openCreateLayerModal();
        
        GIS.map.doubleClickZoom.enable();

        alert(
            "Drawing cancelled"
        );
    },

    handleLineClick(e) {

        const latlng = e.latlng;

        GISDrawing.linePoints.push([
            latlng.lat,
            latlng.lng
        ]);

        // remove old preview
        if (GISDrawing.tempLine) {

            GIS.map.removeLayer(
                GISDrawing.tempLine
            );
        }

        // create updated preview
        GISDrawing.tempLine = L.polyline(

            GISDrawing.linePoints,

            {
                color: "red",
                weight: 4
            }

        ).addTo(GIS.map);
    },

    handlePolygonClick(e) {

        const latlng = e.latlng;

        GISDrawing.polygonPoints.push([
            latlng.lat,
            latlng.lng
        ]);

        if (GISDrawing.tempPolygon) {

            GIS.map.removeLayer(
                GISDrawing.tempPolygon
            );
        }

        GISDrawing.tempPolygon = L.polygon(

            GISDrawing.polygonPoints,

            {
                color: "blue",
                weight: 3,
                fillOpacity: 0.3
            }

        ).addTo(GIS.map);
    },

    handlePointClick(e) {

        if (
            !GISDrawing.drawingMode ||
            GISDrawing.currentGeometryType !== "Point"
        ) {
            return;
        }

        const latlng = e.latlng;

        GISDrawing.pendingFeature = {

            type: "Feature",

            geometry: {

                type: "Point",

                coordinates: [
                    latlng.lng,
                    latlng.lat
                ]
            },

            properties: {}
        };

        document
            .getElementById(
                "attributeModal"
            )
            .classList.remove(
                "d-none"
            );
    },

   
    finishCurrentLine(e) {

        console.log(
            "DOUBLE CLICK DETECTED",
            GISDrawing.linePoints.length
        );

        if (e && e.originalEvent) {

            L.DomEvent.stop(
                e.originalEvent
            );
        }

        if (
            GISDrawing.linePoints.length < 2
        ) {
            return;
        }

        const feature = {

            type: "Feature",

            geometry: {

                type: "LineString",

                coordinates:

                    GISDrawing.linePoints.map(
                        point => [

                            point[1],
                            point[0]
                        ]
                    )
            },

            properties: {

                name:
                    GISDrawing.currentLayerName
            }
        };

        GISDrawing.pendingFeature =
            feature;

        document
            .getElementById(
                "attributeModal"
            )
            .classList.remove(
                "d-none"
            );

        GISDrawing.drawnLayers.push(
            GISDrawing.tempLine
        );

        GISDrawing.completedLineCount++;

        console.log(
            "Line completed",
            feature
        );

        GISDrawing.linePoints = [];

        GISDrawing.tempLine = null;

        alert(
            "Line completed. Click to start another line or Save Drawn Layer."
        );
    },

    finishCurrentPolygon() {

        if (
            GISDrawing.polygonPoints.length < 3
        ) {

            alert(
                "Polygon requires at least 3 vertices"
            );

            return;
        }

        const coordinates =
            GISDrawing.polygonPoints.map(
                point => [
                    point[1],
                    point[0]
                ]
            );

        coordinates.push(
            coordinates[0]
        );

        const feature = {

            type: "Feature",

            geometry: {

                type: "Polygon",

                coordinates: [
                    coordinates
                ]
            },

            properties: {

                name:
                    GISDrawing.currentLayerName
            }
        };

        GISDrawing.pendingFeature =
            feature;

        document
            .getElementById(
                "attributeModal"
            )
            .classList.remove(
                "d-none"
            );

        GISDrawing.drawnLayers.push(
            GISDrawing.tempPolygon
        );

        GISDrawing.polygonPoints = [];

        GISDrawing.tempPolygon = null;

        alert(
            "Polygon completed. Click Save Drawn Layer."
        );
    },


    finishDrawing() {

        if (
            this.currentGeometryType ===
            "LineString"
        ) {

            this.finishCurrentLine();
        }
        else if (
            this.currentGeometryType ===
            "Polygon"
        ) {

            this.finishCurrentPolygon();
        }
    },

   saveFeatureAttributes() {

        if (!this.pendingFeature) {

            alert(
                "No feature available"
            );

            return;
        }

        const name =
            document
                .getElementById(
                    "attrName"
                )
                .value;

        const type =
            document
                .getElementById(
                    "attrType"
                )
                .value;

        const description =
            document
                .getElementById(
                    "attrDescription"
                )
                .value;

        this.pendingFeature.properties = {

            name,

            type,

            description
        };

        // POINT MARKER DISPLAY
        if (
            this.pendingFeature.geometry.type
            === "Point"
        ) {

            const coordinates =
                this.pendingFeature.geometry.coordinates;

            const marker = L.marker([

                coordinates[1],
                coordinates[0]

            ])

            .addTo(GIS.map)

            .bindPopup(`

                <b>${name}</b><br>

                ${type}

            `);

            this.drawnLayers.push(
                marker
            );
        }

        this.drawnFeatures.push(
            this.pendingFeature
        );

        this.pendingFeature = null;

        document
            .getElementById(
                "attrName"
            )
            .value = "";

        document
            .getElementById(
                "attrType"
            )
            .value = "";

        document
            .getElementById(
                "attrDescription"
            )
            .value = "";

        document
            .getElementById(
                "attributeModal"
            )
            .classList.add(
                "d-none"
            );

        console.log(
            "Feature Added",
            this.drawnFeatures
        );
    },

    async saveDrawnLayer() {

        if (
            this.drawnFeatures.length === 0
        ) {

            alert(
                "No features drawn"
            );

            return;
        }

        const geojson = {

            type: "FeatureCollection",

            features:
                this.drawnFeatures
        };

        try {

            const response =
                await fetch(

                    `/projects/${this.currentProjectId}/gis/layers`,

                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/json",

                            "X-CSRF-TOKEN":
                                document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .content
                        },

                        body: JSON.stringify({

                            layername:
                                this.currentLayerName,

                            geojson,

                            layer_type:
                                "drawing"
                        })
                    }
                );

            const data =
                await response.json();

            if (data.success) {

                alert(
                    "Layer saved successfully"
                );

                GIS.clearInteractions();

                this.drawnFeatures = [];

                this.drawnLayers = [];

                this.pendingLatLng = null;

                this.activeLayer = null;

                this.currentProjectId = null;

                this.currentLayerName = null;

                this.currentGeometryType = null;

                this.drawingMode = false;

                if (this.tempLine) {

                    GIS.map.removeLayer(
                        this.tempLine
                    );
                }

                this.linePoints = [];

                this.tempLine = null;

                if (this.tempPolygon) {

                    GIS.map.removeLayer(
                        this.tempPolygon
                    );
                }

                this.polygonPoints = [];

                this.tempPolygon = null;

                this.completedLineCount = 0;

                GIS.map.doubleClickZoom.enable();

                document
                    .getElementById(
                        "saveDrawLayerBtn"
                    )
                    .classList.add(
                        "d-none"
                    );

                document
                    .getElementById(
                        "finishLineBtn"
                    )
                    .classList.add(
                        "d-none"
                    );

                const btn =
                    document.getElementById(
                        "createLayerBtn"
                    );

                btn.innerText =
                    "+ Create Layer";

                btn.classList.remove(
                    "btn-danger"
                );

                btn.classList.add(
                    "btn-success"
                );

                btn.onclick = () =>
                    GISDrawing.openCreateLayerModal();
            }

        } catch (err) {

            console.error(err);

            alert(
                "Save failed"
            );
        }
    }
};