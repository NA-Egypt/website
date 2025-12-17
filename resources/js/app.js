import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '@popperjs/core';
// 1. Initialize Globals explicitly
window.jQuery = window.$ = $;
import select2 from 'select2'; // Import Select2 JS
select2(); // Register with jQuery (some versions need this, others just import)

// Stub Axios to prevent crash (User requested features don't strictly need it right now)
window.axios = {
    defaults: {
        headers: {
            common: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

import "/node_modules/select2/dist/css/select2.css";

// 3. Load DataTables (extends global jQuery)
import 'datatables.net-bs5';
// import 'datatables.net-buttons-bs5';
// import 'datatables.net-buttons/js/buttons.html5.js';
// import 'datatables.net-buttons/js/buttons.print.js';

// 4. Load Custom Scripts (Sequential loading to handle dependencies)
const loadPlugins = async () => {
    try {
        // Libraries first
        await import('./plugin/jquery-jvectormap-2.0.2.min');
        // await import('./plugin/jquery-jvectormap-world-mill-en'); // If exists
        await import('./plugin/jquery.peity.min');
        await import('./plugin/pace.min');
        await import('./plugin/simplebar.min');

        // Logic that depends on libraries
        await import('./plugin/index');
        await import('./plugin/custom');

        console.log("All plugins loaded sequentially.");
    } catch (err) {
        console.error("Failed to load plugins via async/await", err);
    }
};

loadPlugins();

import.meta.glob([
    '../images/**'
]);

