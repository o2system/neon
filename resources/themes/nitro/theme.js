/*
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author         Teguh Rianto
 *  @copyright      Copyright (c) Teguh Rianto
 */

// Main Dependencies
let $ = require('jquery');
window.$ = window.jQuery = $;

// Bootstrap
import Popper from 'popper.js';
window.Popper = Popper.default;
import 'bootstrap';

// Chartlist
import Chartist from 'chartist';
window.Chartist = Chartist;

require('chartist-plugin-tooltips');
window.c3 = require('c3');

// jQuery Plugins
require('jquery-sparkline');
require('jquery-slinky');
require('jvectormap-next')($);
$.fn.vectorMap('addMap', 'world_mill', require('jvectormap-content/world-mill'));

// Anchor
require('./assets/js/anchor');

// Aside
require('./assets/js/sidebar');

// PerfectScrollbar
require('./assets/js/perfect-scrollbar');