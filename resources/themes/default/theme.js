/*
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 *  @copyright      Copyright (c) Steeve Andrian Salim
 */

import "o2system-venus-admin";
import Chart from 'chart.js';
window.Chart = Chart;
//Toastr
import toastr from "toastr";
window.toastr = toastr;
//Swal
import Swal from 'sweetalert2';
window.Swal = Swal;
//Quill
import Quill from 'quill';
window.Quill = Quill;
//Dropzone
import Dropzone from 'dropzone';
window.Dropzone = Dropzone;
//Sortable
import Sortable from 'sortablejs';
window.Sortable = Sortable;

import "./assets/js/sidebar";
import "./assets/js/anchor";
import "./assets/js/search";
import "./assets/js/customizer";

$(".stop-propagation").click(function (e) {
    e.stopPropagation();
});