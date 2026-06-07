window.GISDrawing = {

    activeLayer: null,

    drawnFeatures: [],

    drawnLayers: [],

    pendingLatLng: null,

    linePoints: [],

    tempLine: null,

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

        if (geometryType === "LineString") {

            document
                .getElementById(
                    "finishLineBtn"
                )
                .classList.remove(
                    "d-none"
                );
        }

        this.closeCreateLayerModal();

        alert(
            "Click on map to add points"
        );
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

            GIS.map.on(
                "dblclick",
                function(e) {

                    console.log(
                        "MAP DBLCLICK EVENT"
                    );

                    GISDrawing.finishCurrentLine(e);
                }
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

    handlePointClick(e) {

        if (
            !GISDrawing.drawingMode ||
            GISDrawing.currentGeometryType !== "Point"
        ) {
            return;
        }

        GISDrawing.pendingLatLng = e.latlng;

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

        GISDrawing.drawnFeatures.push(
            feature
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

    savePointAttributes() {

        const latlng =
            this.pendingLatLng;

        if (!latlng) return;

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

        const marker = L.marker(latlng)

            .addTo(GIS.map)

            .bindPopup(`
                <b>${name}</b><br>
                ${type}
            `);

        this.drawnLayers.push(
            marker
        );

        this.drawnFeatures.push({

            type: "Feature",

            geometry: {

                type: "Point",

                coordinates: [
                    latlng.lng,
                    latlng.lat
                ]
            },

            properties: {

                name,

                type,

                description
            }
        });

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

        this.pendingLatLng = null;

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