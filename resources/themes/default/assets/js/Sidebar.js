/*
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 *  @copyright      Copyright (c) Steeve Andrian Salim
 */

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