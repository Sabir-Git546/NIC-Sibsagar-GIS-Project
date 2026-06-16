window.GISUI = {

    openPanel() {

        document
            .getElementById("controlPanel")
            .classList.remove("collapsed");

        document
            .getElementById("openPanelBtn")
            .style.display = "none";

        setTimeout(() => {

            GIS.map.invalidateSize();

        }, 320);
    },

    closePanel() {

        document
            .getElementById("controlPanel")
            .classList.add("collapsed");

        document
            .getElementById("openPanelBtn")
            .style.display = "block";

        setTimeout(() => {

            GIS.map.invalidateSize();

        }, 320);
    }
};