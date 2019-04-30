export default class Sidebar {
    /**
     * Sidebar.constructor
     */
    constructor() {
        //Sidebar Right Toggle
        $(".rightside-toggle, .editor-sidebar-toggle").click(function () {
            if ($("body").hasClass("editor-sidebar-showing")) {
                $("body").removeClass("editor-sidebar-showing");
            } else {
                $("body").addClass("editor-sidebar-showing");
            }
        });
    }
}