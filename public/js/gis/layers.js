window.GISLayer = {

    // =============================
    // LOAD LAYER
    // =============================
    load(name) {

        if (GIS.layers[name]) return;

        fetch("/gis/layer/" + name)

            .then(r => r.json())

            .then(data => {

                const layer = L.geoJSON(data, {

                    onEachFeature: (feature, leafletLayer) => {

                        leafletLayer.featureProps =
                            feature.properties || {};

                        leafletLayer.on("click", (e) => {

                            GISLayer.showFeaturePopup(
                                e,
                                leafletLayer,
                                name
                            );

                            GIS.selectedFeature =
                                leafletLayer;

                            GISLayer.zoomToFeature(
                                leafletLayer
                            );
                        });
                    }

                }).addTo(GIS.map);

                GIS.layers[name] = layer;

                GISLayer.fitAllLayers();

            })

            .catch(err =>
                console.error(
                    "Layer load error:",
                    err
                )
            );
    },

    // =============================
    // REMOVE LAYER
    // =============================
    remove(name) {

        if (!GIS.layers[name]) return;

        GIS.map.removeLayer(
            GIS.layers[name]
        );

        delete GIS.layers[name];

        GIS.selectedFeature = null;

        if (
            Object.keys(GIS.layers).length === 0
        ) {

            GIS.resetView();

        } else {

            GISLayer.fitAllLayers();
        }
    },

    // =============================
    // TOGGLE SINGLE
    // =============================
    toggle(cb) {

        cb.checked
            ? this.load(cb.value)
            : this.remove(cb.value);
    },

    // =============================
    // TOGGLE ALL
    // =============================
    toggleAll(master) {

        document
            .querySelectorAll(".layer-item input")

            .forEach(cb => {

                cb.checked = master.checked;

                this.toggle(cb);
            });

        setTimeout(() => {

            GISLayer.fitAllLayers();

        }, 300);
    },

    // =============================
    // SEARCH
    // =============================
    search(q) {

        document
            .querySelectorAll(".layer-item")

            .forEach(el => {

                el.style.display =
                    el.innerText
                        .toLowerCase()
                        .includes(q.toLowerCase())
                        ? ""
                        : "none";
            });
    },

    // =============================
    // FILTER
    // =============================
    filter() {

        let dept =
            document.getElementById(
                "deptFilter"
            ).value;

        document
            .querySelectorAll(".layer-item")

            .forEach(el => {

                let match =
                    !dept ||
                    el.dataset.dept === dept;

                el.style.display =
                    match ? "" : "none";
            });
    },

    // =============================
    // FEATURE POPUP
    // =============================
    showFeaturePopup(e, layer, layerName) {

        const props =
            layer.featureProps || {};

        let html = `

            <div style="
                min-width:220px;
                max-width:300px;
                font-family:Arial;
            ">

                <h4 style="
                    margin:0 0 8px 0;
                    color:#2c3e50;
                ">
                    📍 Feature Info
                </h4>

                <div style="
                    font-size:13px;
                    margin-bottom:6px;
                ">
                    <b>Layer:</b> ${layerName}
                </div>

                <div style="
                    font-size:13px;
                    margin-bottom:6px;
                ">
                    <b>Geometry:</b>
                    ${layer.feature?.geometry?.type || "Unknown"}
                </div>

                <hr>
        `;

        let hasData = false;

        for (let key in props) {

            hasData = true;

            html += `
                <div style="
                    font-size:12px;
                    margin-bottom:4px;
                ">
                    <b>${key}:</b>
                    ${props[key]}
                </div>
            `;
        }

        if (!hasData) {

            html += `<i>No attributes available</i>`;
        }

        html += `</div>`;

        L.popup({

            maxWidth: 320,

            className:
                "gis-feature-popup"

        })

        .setLatLng(e.latlng)

        .setContent(html)

        .openOn(GIS.map);
    },

    // =============================
    // SAFE ZOOM
    // =============================
    zoomToFeature(layer) {

        if (!layer) return;

        try {

            // polygon / line
            if (
                typeof layer.getBounds ===
                "function"
            ) {

                const bounds =
                    layer.getBounds();

                if (bounds.isValid()) {

                    GIS.map.fitBounds(
                        bounds,
                        {
                            padding: [40, 40],
                            maxZoom: 17
                        }
                    );
                }
            }

            // point
            else if (
                typeof layer.getLatLng ===
                "function"
            ) {

                GIS.map.setView(
                    layer.getLatLng(),
                    17
                );
            }

        } catch (err) {

            console.warn(
                "Zoom error:",
                err
            );
        }
    },

    // =============================
    // FIT ALL ACTIVE LAYERS
    // =============================
    fitAllLayers() {

        const group =
            new L.featureGroup();

        Object.values(GIS.layers)

            .forEach(layer => {

                group.addLayer(layer);
            });

        if (
            group.getLayers().length > 0
        ) {

            GIS.map.fitBounds(

                group.getBounds(),

                {
                    padding: [40, 40],
                    maxZoom: 16
                }
            );
        }
    }
};