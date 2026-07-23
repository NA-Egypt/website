import $ from 'jquery';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import multiMonthPlugin from '@fullcalendar/multimonth';

window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    multiMonthPlugin
};

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import '@popperjs/core';
import Swal from 'sweetalert2';
window.Swal = Swal;
import Splide from '@splidejs/splide';
window.Splide = Splide;
// 1. Initialize Globals explicitly
window.jQuery = window.$ = $;
import select2 from 'select2'; // Import Select2 JS
select2(); // Register with jQuery (some versions need this, others just import)
import './form-select2.js';

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
        await import('./plugin/jquery-jvectormap-world-mill-en'); // If exists
        await import('./plugin/jquery.peity.min');
        await import('./plugin/pace.min');
        await import('./plugin/simplebar.min');

        // Logic that depends on libraries
        await import('./plugin/index');
        await import('./plugin/custom');


    } catch (err) {
        console.error("Failed to load plugins via async/await", err);
    }
};

loadPlugins();

import.meta.glob([
    '../images/**'
]);

import { createApp, h } from 'vue';
import TransactionsTable from './components/TransactionsTable.vue';
import GenericDataTable from './components/GenericDataTable.vue';
import FacebookTargeting from './components/FacebookTargeting.vue';
import CtkDateTimePickerWrapper from './components/CtkDateTimePickerWrapper.vue';

document.addEventListener("DOMContentLoaded", () => {
    // Mount FacebookTargeting
    const fbEl = document.querySelector('[data-vue-app="FacebookTargeting"]');
    if (fbEl) {
        const initialGroups = JSON.parse(fbEl.getAttribute('data-initial-groups') || '[]');
        const syncRoute = fbEl.getAttribute('data-sync-route') || '';
        const downloadRoute = fbEl.getAttribute('data-download-route') || '';
        const staticMapRoute = fbEl.getAttribute('data-static-map-route') || '';
        const csrfToken = fbEl.getAttribute('data-csrf-token') || '';
        const isSuperAdmin = fbEl.hasAttribute('data-is-super-admin');

        const app = createApp({
            render: () => h(FacebookTargeting, {
                initialGroups,
                syncRoute,
                downloadRoute,
                staticMapRoute,
                csrfToken,
                isSuperAdmin
            })
        });
        app.mount(fbEl);
    }

    // Mount TransactionsTable
    const transactionsEl = document.querySelector('[data-vue-app="TransactionsTable"]');
    if (transactionsEl) {
        const fetchUrl = transactionsEl.getAttribute('data-fetch-url');
        const availableModels = JSON.parse(transactionsEl.getAttribute('data-available-models') || '[]');
        const availableOperations = JSON.parse(transactionsEl.getAttribute('data-available-operations') || '[]');

        const app = createApp({
            render: () => h(TransactionsTable, {
                fetchUrl,
                availableModels,
                availableOperations
            })
        });
        app.mount(transactionsEl);
    }

    // Mount GenericDataTable
    const genericEls = document.querySelectorAll('[data-vue-app="GenericDataTable"]');
    genericEls.forEach(el => {
        const fetchUrl = el.getAttribute('data-fetch-url');
        const columns = JSON.parse(el.getAttribute('data-columns') || '[]');
        const createRoute = el.getAttribute('data-create-route') || '';
        const createLabel = el.getAttribute('data-create-label') || '';
        const bulkActionRoute = el.getAttribute('data-bulk-action-route') || '';
        const bulkActions = JSON.parse(el.getAttribute('data-bulk-actions') || '[]');
        const bulkIdsName = el.getAttribute('data-bulk-ids-name') || 'ids[]';
        const editRouteTemplate = el.getAttribute('data-edit-route-template') || '';
        const showRouteTemplate = el.getAttribute('data-show-route-template') || '';
        const deleteRouteTemplate = el.getAttribute('data-delete-route-template') || '';
        const hasAgendasButton = el.hasAttribute('data-has-agendas-button');
        const hasToggleVerificationButton = el.hasAttribute('data-has-toggle-verification-button');
        const deleteRouteName = el.getAttribute('data-delete-route-name') || '';

        const app = createApp({
            render: () => h(GenericDataTable, {
                fetchUrl,
                columns,
                createRoute,
                createLabel,
                bulkActionRoute,
                bulkActions,
                bulkIdsName,
                editRouteTemplate,
                showRouteTemplate,
                deleteRouteTemplate,
                hasAgendasButton,
                hasToggleVerificationButton,
                deleteRouteName
            })
        });
        app.mount(el);
    });

    // Mount VueCtkDateTimePicker instances globally
    const datePickerEls = document.querySelectorAll('[data-vue-app="VueCtkDateTimePicker"]');
    datePickerEls.forEach(el => {
        const name = el.getAttribute('data-name') || '';
        const id = el.getAttribute('data-id') || '';
        const value = el.getAttribute('data-value') || null;
        const enableTime = el.getAttribute('data-enable-time') === 'true';
        const timeOnly = el.getAttribute('data-time-only') === 'true';
        const placeholder = el.getAttribute('data-placeholder') || '';
        const locale = el.getAttribute('data-locale') || 'ar';

        const app = createApp({
            render: () => h(CtkDateTimePickerWrapper, {
                name,
                id,
                modelValue: value,
                enableTime,
                timeOnly,
                placeholder,
                locale,
                onChange: (val) => {
                    // Dispatch custom change event to native DOM element for external listeners like setDate / findTime
                    const hiddenInput = el.querySelector('input[type="hidden"]');
                    if (hiddenInput) {
                        hiddenInput.value = typeof val === 'object' && val.hours !== undefined ? `${String(val.hours).padStart(2, '0')}:${String(val.minutes).padStart(2, '0')}` : (val instanceof Date ? val.toISOString() : val);
                        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    el.dispatchEvent(new CustomEvent('picker-change', { detail: val, bubbles: true }));
                }
            })
        });
        app.mount(el);
    });
});


