window.GIS = {

    // =========================
    // MAP
    // =========================
    map: null,

    // =========================
    // GIS LAYERS
    // =========================
    layers: {},

    csvGeoJson: null,

    csvLayers: [],

    kmlLayers: [],

    kmlGeoJson: null,

    kmlPreviousView: null,

    // =========================
    // FEATURE STATE
    // =========================
    selectedFeature: null,

    previousFeature: null,

    mode: null,

    // =========================
    // ANALYSIS STATE
    // =========================
    analysis: {

        buffers: [],

        overlaps: [],

        measurements: []
    },

    // =========================
    // INTERACTION STATE
    // =========================
    interaction: {

        activeTool: null,

        selectedFeatures: [],

        selectedBuffers: [],

        analysisLayer: null
    },

    // =========================
    // HOME VIEW
    // =========================
    home: {

        center: [26.9891, 94.6394],

        zoom: 13
    }
};

// =========================
// INIT GIS
// =========================
window.initGIS = function () {

    GIS.map = L.map("map", {

        doubleClickZoom: false

    }).setView(

        GIS.home.center,
        GIS.home.zoom
    );

    // =========================
    // BASEMAP
    // =========================
    L.tileLayer(

        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",

        {
            attribution: "© OpenStreetMap"
        }

    ).addTo(GIS.map);

    // =========================
    // SAVE INITIAL VIEW
    // =========================
    GIS.home.center = GIS.map.getCenter();

    GIS.home.zoom = GIS.map.getZoom();

    // =========================
    // RESET FEATURE SELECTION
    // =========================
    GIS.map.on("click", () => {

        GIS.selectedFeature = null;
    });

    console.log("GIS initialized");
};

// =========================
// RESET VIEW
// =========================
GIS.resetView = function () {

    GIS.map.setView(

        GIS.home.center,
        GIS.home.zoom
    );

    GIS.selectedFeature = null;

    GIS.previousFeature = null;
};

// =========================
// CLEAR MAP INTERACTIONS
// =========================
GIS.clearInteractions = function () {

    if (!GIS.map) return;

    GIS.map.off("click");

    GIS.map.off("dblclick");

    GIS.map.off("contextmenu");

    GIS.map.getContainer().style.cursor = "";

    GIS.map.on("click", () => {

        GIS.selectedFeature = null;
    });
};

// =========================
// INIT
// =========================
document.addEventListener(

    "DOMContentLoaded",

    initGIS
);