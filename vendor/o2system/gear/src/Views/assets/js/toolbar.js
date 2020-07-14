/*
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------
var gearToolbar = {

    tabActive: null,

    init: function () {
        var buttons = document.querySelectorAll('.gear-toolbar-tab');

        for (var i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener('click', gearToolbar.showTab, false);
        }
    },

    //--------------------------------------------------------------------

    showTab: function (tabId) {
        var tabs = document.querySelectorAll('.tab');
        var tabButtons = document.querySelectorAll('.tab-button');
        var tabContainer = document.getElementById('gear-toolbar-tabs');
        var tabActive = document.getElementById('gear-toolbar-tab-' + tabId);
        var tabButtonActive = document.getElementById('tab-button-' + tabId);

        if (gearToolbar.tabActive === tabId) {
            if (tabContainer.classList.contains('show')) {
                tabActive.classList.remove('show');
                tabContainer.classList.remove('show');
                tabButtonActive.classList.remove('active');
            }
            gearToolbar.tabActive = null;
        } else {
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('show');
                tabButtons[i].classList.remove('active');
            }
            gearToolbar.tabActive = tabId;
            tabContainer.classList.add('show');
            tabActive.classList.add('show');
            tabButtonActive.classList.add('active');
        }
    },

    //--------------------------------------------------------------------

    addClass: function (el, className) {
        if (el.classList) {
            el.classList.add(className);
        } else {
            el.className += ' ' + className;
        }
    },

    //--------------------------------------------------------------------

    removeClass: function (el, className) {
        if (el.classList) {
            el.classList.remove(className);
        } else {
            el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
        }

    },

    //--------------------------------------------------------------------

    /**
     * Toggle display of a data table
     * @param obj
     */
    toggleDataTable: function (obj) {
        if (typeof obj == 'string') {
            obj = document.getElementById(obj + '_table');
        }

        if (obj) {
            obj.style.display = obj.style.display == 'none' ? 'block' : 'none';
        }
    },

    //--------------------------------------------------------------------

    /**
     *   Toggle tool bar from full to icon and icon to full
     */
    toggleToolbar: function () {
        var tabs = document.querySelectorAll('.tab');
        var tabContainer = document.getElementById('gear-toolbar-tabs');

        if (tabContainer.classList.contains('show')) {
            tabContainer.classList.remove('show');
        } else {
            tabContainer.classList.add('show');
            if (gearToolbar.tabActive === null) {
                gearToolbar.showTab('metrics');
            }
        }
    },

    //--------------------------------------------------------------------
};
