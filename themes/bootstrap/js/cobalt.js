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
    },

    toggleFullScreen: function() {
        if ((document.fullScreenElement && document.fullScreenElement !== null) ||    // alternative standard method
            (!document.mozFullScreenElement && !document.webkitFullScreenElement)) {  // current working methods
            if (document.documentElement.requestFullScreen) {
                document.documentElement.requestFullScreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullScreen) {
                document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.cancelFullScreen) {
                document.cancelFullScreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            }
        }
    }
};

window.onload = function () {
    Cobalt.init();
};
