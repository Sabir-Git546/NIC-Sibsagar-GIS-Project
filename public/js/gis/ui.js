window.GISUI = {

    openPanel() {
        document.getElementById("controlPanel").classList.remove("collapsed");
        document.getElementById("openPanelBtn").style.display = "none";
    },

    closePanel() {
        document.getElementById("controlPanel").classList.add("collapsed");
        document.getElementById("openPanelBtn").style.display = "block";
    }
};