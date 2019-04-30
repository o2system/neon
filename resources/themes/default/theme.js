import "o2system-venus-ui/src/main";
import "o2system-venus-form/src/main";
import "o2system-venus-admin/src/main"
import Search from "./assets/js/Search";
import Sidebar from "./assets/js/Sidebar";
import Customizer from "./assets/js/Customizer";
import "./theme.scss"

export default class Theme {
    constructor() {
        this.search = new Search();
        this.sidebar = new Sidebar();
        this.customizer = new Customizer();
    }
}