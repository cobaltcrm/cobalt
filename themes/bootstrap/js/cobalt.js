var Cobalt = {
    init: function() {
        this.bindPopovers();
        this.bindTooltips();
    },

    bindPopovers: function() {
        $('[rel="tooltip"]').tooltip({
            container: "body"
        });
    },

    bindTooltips: function() {
        $('[rel="popover"]').popover();
    }
};

window.onload = function () {
    Cobalt.init();
};
