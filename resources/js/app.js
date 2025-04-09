import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '@popperjs/core';
import $ from 'jquery';
window.jQuery = $; 
window.$ = $;


import "/node_modules/select2/dist/css/select2.css";

import 'datatables.net';
import 'datatables.net-dt';
import 'datatables.net-buttons';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.print.js'; 

import './plugin/custom';
import './plugin/jquery-jvectormap-2.0.2.min';
import './plugin/jquery-jvectormap-world-mill-en';
import './plugin/index';
import './plugin/pace.min';
import './plugin/simplebar.min';
import './plugin/jquery.peity.min';
// import './plugin/metisMenu.min';
import 'metismenu';
// import './form-select2';


import.meta.glob([
    '../images/**'
]);


// $('.select2').select2();  // Initialize Select2 if it's defined
// $(document).ready(function() {
//     console.log($.fn.select2);  // Check if Select2 is defined
//     if ($.fn.select2) {
//     } else {
//         console.error("Select2 is not loaded");
//     }
// });

