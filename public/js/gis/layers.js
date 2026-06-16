window.GISLayer = {

    // =============================
    // LOAD LAYER (GET GEOJSON)
    // Route: /gis/layer/{layername}
    // =============================
    searchText: "",
    load(name) {

        if (GIS.layers[name]) return;

        fetch("/gis/layer/" + encodeURIComponent(name))
            .then(async r => {

                if (!r.ok) {
                    throw new Error("Failed to load layer");
                }

                return r.json();
            })
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

                            GIS.selectedFeature = leafletLayer;

                            GISLayer.zoomToFeature(leafletLayer);
                        });
                    }

                }).addTo(GIS.map);

                GIS.layers[name] = layer;

                GISLayer.fitAllLayers();

            })
            .catch(err => {
                console.error("Layer load error:", err);
            });
    },


    // =============================
    // REMOVE FROM MAP ONLY
    // =============================
    remove(name) {

        if (!GIS.layers[name]) return;

        GIS.map.removeLayer(GIS.layers[name]);

        delete GIS.layers[name];

        GIS.selectedFeature = null;

        if (Object.keys(GIS.layers).length === 0) {
            GIS.resetView();
        } else {
            GISLayer.fitAllLayers();
        }
    },


    // =============================
    // TOGGLE SINGLE LAYER
    // =============================
    toggle(cb) {

        cb.checked
            ? this.load(cb.value)
            : this.remove(cb.value);
    },


    // =============================
    // TOGGLE ALL LAYERS
    // =============================
    toggleAll(master) {

        document.querySelectorAll(".layer-item input")
            .forEach(cb => {
                cb.checked = master.checked;
                this.toggle(cb);
            });

        setTimeout(() => {
            GISLayer.fitAllLayers();
        }, 300);
    },


// =============================
// SEARCH LAYERS
// =============================
search(q) {

    const normalized = (q || "")
        .toLowerCase()
        .trim();

    if (GISLayer.searchText === normalized) return;

    GISLayer.searchText = normalized;

    GISLayer.applyFilters();
},


// =============================
// FILTER BY DEPARTMENT
// =============================
filter() {

    GISLayer.applyFilters();
},


// =============================
// APPLY SEARCH + DEPT FILTER
// =============================
applyFilters() {

    const dept = (document.getElementById("deptFilter")?.value || "").trim();
    const search = (GISLayer.searchText || "").toLowerCase().trim();

    document.querySelectorAll(".layer-item").forEach(el => {

        const itemDept = (el.dataset.dept || "").trim();
        const itemName = (el.dataset.name || "").toLowerCase().trim();

        const deptMatch = !dept || itemDept === dept;
        const searchMatch = !search || itemName.includes(search);

        el.classList.toggle("hidden", !(deptMatch && searchMatch));
    });

},


    // =============================
    // POPUP FEATURE INFO
    // =============================
    showFeaturePopup(e, layer, layerName) {

        const props = layer.featureProps || {};

        let html = `
            <div style="
                min-width:220px;
                max-width:300px;
                font-family:Arial;
            ">

                <h4 style="margin:0 0 8px 0;color:#2c3e50;">
                    📍 Feature Info
                </h4>

                <div style="font-size:13px;margin-bottom:6px;">
                    <b>Layer:</b> ${layerName}
                </div>

                <div style="font-size:13px;margin-bottom:6px;">
                    <b>Geometry:</b>
                    ${layer.feature?.geometry?.type || "Unknown"}
                </div>

                <hr>
        `;

        let hasData = false;

        for (let key in props) {
            hasData = true;

            html += `
                <div style="font-size:12px;margin-bottom:4px;">
                    <b>${key}:</b> ${props[key]}
                </div>
            `;
        }

        if (!hasData) {
            html += `<i>No attributes available</i>`;
        }

        html += `</div>`;

        L.popup({
            maxWidth: 320,
            className: "gis-feature-popup"
        })
            .setLatLng(e.latlng)
            .setContent(html)
            .openOn(GIS.map);
    },


    // =============================
    // ZOOM TO FEATURE
    // =============================
    zoomToFeature(layer) {

        if (!layer) return;

        try {

            if (typeof layer.getBounds === "function") {

                const bounds = layer.getBounds();

                if (bounds.isValid()) {
                    GIS.map.fitBounds(bounds, {
                        padding: [40, 40],
                        maxZoom: 17
                    });
                }

            } else if (typeof layer.getLatLng === "function") {

                GIS.map.setView(layer.getLatLng(), 17);
            }

        } catch (err) {
            console.warn("Zoom error:", err);
        }
    },


    // =============================
    // FIT ALL LAYERS
    // =============================
    fitAllLayers() {

        const group = new L.featureGroup();

        Object.values(GIS.layers).forEach(layer => {
            group.addLayer(layer);
        });

        if (group.getLayers().length > 0) {

            GIS.map.fitBounds(group.getBounds(), {
                padding: [40, 40],
                maxZoom: 16
            });
        }
    },


    // =============================
    // DELETE LAYER (DB + MAP)
    // Route: /projects/{projectid}/gis/delete/{layername}
    // =============================
    deleteLayer(projectid, layername) {

        if (!confirm("Delete this layer?")) return;

        fetch(`/projects/${projectid}/gis/delete/${encodeURIComponent(layername)}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            }
        })
        .then(async res => {

            if (!res.ok) {
                throw new Error("Delete request failed");
            }

            return res.json();
        })
        .then(data => {

            if (data.success) {

                // remove from map immediately (no reload needed)
                GISLayer.remove(layername);

            } else {
                alert(data.message || "Delete failed");
            }

        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    }
};