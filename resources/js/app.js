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
import '@splidejs/splide/css';
window.Splide = Splide;

// Global jQuery
if (typeof window !== 'undefined') {
    window.jQuery = window.$ = $;
}
import select2 from 'select2';
select2(window, $);
import './form-select2.js';
import './frontend.js';
import "select2/dist/css/select2.css";

import { initWebVitals } from './web-vitals.js';
initWebVitals();

// Select2 CSS & Vue3 Select styles
import 'vue3-select-component/styles';

// Axios setup
window.axios = {
    defaults: {
        headers: {
            common: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

import 'datatables.net-bs5';

// Load legacy plugins (Admin pages only)
const loadPlugins = async () => {
    if (!document.querySelector('.wrapper, .has-sidebar, .sidebar, #sidebar-wrapper, .minimal-theme')) {
        return;
    }
    try {
        await import('./plugin/jquery-jvectormap-2.0.2.min');
        await import('./plugin/jquery-jvectormap-world-mill-en');
        await import('./plugin/jquery.peity.min');
        await import('./plugin/pace.min');
        await import('./plugin/simplebar.min');
        await import('./plugin/index');
        await import('./plugin/custom');
    } catch (err) {
        console.error("Failed to load plugins", err);
    }
};

loadPlugins();

import.meta.glob([
    '../images/**'
]);

import { createApp, h, defineAsyncComponent } from 'vue';
import { createPinia } from 'pinia';
import '@kodeglot/vue-calendar/style.css';

const TransactionsTable = defineAsyncComponent(() => import('./components/TransactionsTable.vue'));
const GenericDataTable = defineAsyncComponent(() => import('./components/GenericDataTable.vue'));
const FacebookTargeting = defineAsyncComponent(() => import('./components/FacebookTargeting.vue'));
const ServiceBodyMap = defineAsyncComponent(() => import('./components/ServiceBodyMap.vue'));
const CtkDateTimePickerWrapper = defineAsyncComponent(() => import('./components/CtkDateTimePickerWrapper.vue'));
const EventsCalendar = defineAsyncComponent(() => import('./components/EventsCalendar.vue'));
const AnimatedStatCard = defineAsyncComponent(() => import('./components/AnimatedStatCard.vue'));
const VueSelectWrapper = defineAsyncComponent(() => import('./components/VueSelectWrapper.vue'));

const mountVueApps = () => {
    const calendarEls = document.querySelectorAll('[data-vue-app="EventsCalendar"]');
    calendarEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
        const fetchUrl = el.getAttribute('data-fetch-url') || '/web-calendar-events';
        const storeUrl = el.getAttribute('data-store-url') || '/web-calendar-events';
        const canManage = el.hasAttribute('data-can-manage');
        const locale = el.getAttribute('data-locale') || document.documentElement.lang || 'ar';
        const csrfToken = el.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const initialEvents = JSON.parse(el.getAttribute('data-initial-events') || '[]');

        const app = createApp({
            render: () => h(EventsCalendar, {
                initialEvents,
                fetchUrl,
                storeUrl,
                canManage,
                locale,
                csrfToken
            })
        });
        app.use(createPinia());
        app.mount(el);
    });

    const fbEl = document.querySelector('[data-vue-app="FacebookTargeting"]');
    if (fbEl && !fbEl.dataset.vueMounted) {
        fbEl.dataset.vueMounted = 'true';
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

    const sbMapEl = document.querySelector('[data-vue-app="ServiceBodyMap"]');
    if (sbMapEl && !sbMapEl.dataset.vueMounted) {
        sbMapEl.dataset.vueMounted = 'true';
        const initialData = JSON.parse(sbMapEl.getAttribute('data-initial-data') || '{}');

        const app = createApp({
            render: () => h(ServiceBodyMap, {
                initialData
            })
        });
        app.mount(sbMapEl);
    }

    const transactionsEl = document.querySelector('[data-vue-app="TransactionsTable"]');
    if (transactionsEl && !transactionsEl.dataset.vueMounted) {
        transactionsEl.dataset.vueMounted = 'true';
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

    const genericEls = document.querySelectorAll('[data-vue-app="GenericDataTable"]');
    genericEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
        const fetchUrl = el.getAttribute('data-fetch-url');
        const columns = JSON.parse(el.getAttribute('data-columns') || '[]');
        const createRoute = el.getAttribute('data-create-route') || '';
        const createLabel = el.getAttribute('data-create-label') || '';
        const bulkActionRoute = el.getAttribute('data-bulk-action-route') || '';
        const bulkActions = JSON.parse(el.getAttribute('data-bulk-actions') || '[]');
        const bulkIdsName = el.getAttribute('data-bulk-ids-name') || 'ids[]';
        const editRouteTemplate = el.getAttribute('data-edit-route-template') || '';
        const showRouteTemplate = el.getAttribute('data-show-route-template') || '';
        const impersonateRouteTemplate = el.getAttribute('data-impersonate-route-template') || '';
        const deleteRouteTemplate = el.getAttribute('data-delete-route-template') || '';
        const hasAgendasButton = el.hasAttribute('data-has-agendas-button');
        const hasToggleVerificationButton = el.hasAttribute('data-has-toggle-verification-button');
        const hasImpersonateButton = el.hasAttribute('data-has-impersonate-button');
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
                impersonateRouteTemplate,
                deleteRouteTemplate,
                hasAgendasButton,
                hasToggleVerificationButton,
                hasImpersonateButton,
                deleteRouteName
            })
        });
        app.mount(el);
    });

    const datePickerEls = document.querySelectorAll('[data-vue-app="VueCtkDateTimePicker"]');
    datePickerEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
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

    const animatedStatEls = document.querySelectorAll('[data-vue-app="AnimatedStatCard"]');
    animatedStatEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
        const weeklyMeetings = el.getAttribute('data-weekly-meetings') || '0';
        const groupsCount = el.getAttribute('data-groups-count') || '0';
        const weeklyMeetingsLabel = el.getAttribute('data-weekly-meetings-label') || 'اجتماعات أسبوعية';
        const groupsCountLabel = el.getAttribute('data-groups-count-label') || 'مجموعات';

        const app = createApp({
            render: () => h(AnimatedStatCard, {
                weeklyMeetings,
                groupsCount,
                weeklyMeetingsLabel,
                groupsCountLabel
            })
        });
        app.mount(el);
    });

    const vueSelectEls = document.querySelectorAll('[data-vue-app="VueSelectWrapper"]');
    vueSelectEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
        const options = JSON.parse(el.getAttribute('data-options') || '[]');
        const placeholder = el.getAttribute('data-placeholder') || 'Select...';
        const value = el.getAttribute('data-value') || null;
        const name = el.getAttribute('data-name') || '';
        
        const app = createApp({
            render: () => h(VueSelectWrapper, {
                options,
                placeholder,
                modelValue: value,
                name,
                onPickerChange: (val) => {
                    el.dispatchEvent(new CustomEvent('picker-change', { detail: val, bubbles: true }));
                }
            })
        });
        app.mount(el);
    });
};

document.addEventListener("DOMContentLoaded", mountVueApps);
document.addEventListener("livewire:navigated", mountVueApps);
