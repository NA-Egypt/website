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
const RolesDataTable = defineAsyncComponent(() => import('./components/RolesDataTable.vue'));
const MeetingsDataTable = defineAsyncComponent(() => import('./components/MeetingsDataTable.vue'));
const GroupsDataTable = defineAsyncComponent(() => import('./components/GroupsDataTable.vue'));
const DirectOnlineGroupsDataTable = defineAsyncComponent(() => import('./components/DirectOnlineGroupsDataTable.vue'));
const CitiesDataTable = defineAsyncComponent(() => import('./components/CitiesDataTable.vue'));
const NeighborhoodsDataTable = defineAsyncComponent(() => import('./components/NeighborhoodsDataTable.vue'));
const TopicsDataTable = defineAsyncComponent(() => import('./components/TopicsDataTable.vue'));
const WorkgroupsDataTable = defineAsyncComponent(() => import('./components/WorkgroupsDataTable.vue'));
const ServiceCommitteesDataTable = defineAsyncComponent(() => import('./components/ServiceCommitteesDataTable.vue'));
const ServiceBodiesDataTable = defineAsyncComponent(() => import('./components/ServiceBodiesDataTable.vue'));
const UsersDataTable = defineAsyncComponent(() => import('./components/UsersDataTable.vue'));
const SubscribersDataTable = defineAsyncComponent(() => import('./components/SubscribersDataTable.vue'));
const PermissionsDataTable = defineAsyncComponent(() => import('./components/PermissionsDataTable.vue'));

const mountVueApps = () => {
    const calendarEls = document.querySelectorAll('[data-vue-app="EventsCalendar"]');
    calendarEls.forEach(el => {
        if (el.dataset.vueMounted) return;
        el.dataset.vueMounted = 'true';
        const fetchUrl = el.getAttribute('data-fetch-url') || '/web-calendar-events';
        const storeUrl = el.getAttribute('data-store-url') || '/web-calendar-events';
        const canManage = el.hasAttribute('data-can-manage');
        const canCreate = el.hasAttribute('data-can-create') || canManage;
        const currentUserId = el.getAttribute('data-user-id') ? parseInt(el.getAttribute('data-user-id'), 10) : null;
        const locale = el.getAttribute('data-locale') || document.documentElement.lang || 'ar';
        const csrfToken = el.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const initialEvents = JSON.parse(el.getAttribute('data-initial-events') || '[]');

        const app = createApp({
            render: () => h(EventsCalendar, {
                initialEvents,
                fetchUrl,
                storeUrl,
                canManage,
                canCreate,
                currentUserId,
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

    const rolesEl = document.querySelector('[data-vue-app="RolesDataTable"]');
    if (rolesEl && !rolesEl.dataset.vueMounted) {
        rolesEl.dataset.vueMounted = 'true';
        const fetchUrl = rolesEl.getAttribute('data-fetch-url') || '';
        const createRoute = rolesEl.getAttribute('data-create-route') || '';
        const assignPermissionsTemplate = rolesEl.getAttribute('data-assign-permissions-template') || '';
        const detailsRouteTemplate = rolesEl.getAttribute('data-details-route-template') || '';
        const updateRouteTemplate = rolesEl.getAttribute('data-update-route-template') || '';
        const deleteRouteTemplate = rolesEl.getAttribute('data-delete-route-template') || '';
        const initialKpiStats = JSON.parse(rolesEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(rolesEl.getAttribute('data-labels') || '{}');
        const csrfToken = rolesEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(RolesDataTable, {
                fetchUrl,
                createRoute,
                assignPermissionsTemplate,
                detailsRouteTemplate,
                updateRouteTemplate,
                deleteRouteTemplate,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(rolesEl);
    }

    const meetingsEl = document.querySelector('[data-vue-app="MeetingsDataTable"]');
    if (meetingsEl && !meetingsEl.dataset.vueMounted) {
        meetingsEl.dataset.vueMounted = 'true';
        const fetchUrl = meetingsEl.getAttribute('data-fetch-url') || '';
        const createRoute = meetingsEl.getAttribute('data-create-route') || '';
        const createLabel = meetingsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = meetingsEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = meetingsEl.getAttribute('data-delete-route-template') || '';
        const days = JSON.parse(meetingsEl.getAttribute('data-days') || '[]');
        const initialKpiStats = JSON.parse(meetingsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(meetingsEl.getAttribute('data-labels') || '{}');
        const csrfToken = meetingsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(MeetingsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                days,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(meetingsEl);
    }

    const groupsEl = document.querySelector('[data-vue-app="GroupsDataTable"]');
    if (groupsEl && !groupsEl.dataset.vueMounted) {
        groupsEl.dataset.vueMounted = 'true';
        const fetchUrl = groupsEl.getAttribute('data-fetch-url') || '';
        const createRoute = groupsEl.getAttribute('data-create-route') || '';
        const createLabel = groupsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = groupsEl.getAttribute('data-edit-route-template') || '';
        const showRouteTemplate = groupsEl.getAttribute('data-show-route-template') || '';
        const meetingsRouteTemplate = groupsEl.getAttribute('data-meetings-route-template') || '';
        const deleteRouteTemplate = groupsEl.getAttribute('data-delete-route-template') || '';
        const serviceBodies = JSON.parse(groupsEl.getAttribute('data-service-bodies') || '[]');
        const initialKpiStats = JSON.parse(groupsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(groupsEl.getAttribute('data-labels') || '{}');
        const csrfToken = groupsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(GroupsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                showRouteTemplate,
                meetingsRouteTemplate,
                deleteRouteTemplate,
                serviceBodies,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(groupsEl);
    }

    const directOnlineGroupsEl = document.querySelector('[data-vue-app="DirectOnlineGroupsDataTable"]');
    if (directOnlineGroupsEl && !directOnlineGroupsEl.dataset.vueMounted) {
        directOnlineGroupsEl.dataset.vueMounted = 'true';
        const fetchUrl = directOnlineGroupsEl.getAttribute('data-fetch-url') || '';
        const createRoute = directOnlineGroupsEl.getAttribute('data-create-route') || '';
        const createLabel = directOnlineGroupsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = directOnlineGroupsEl.getAttribute('data-edit-route-template') || '';
        const showRouteTemplate = directOnlineGroupsEl.getAttribute('data-show-route-template') || '';
        const deleteRouteTemplate = directOnlineGroupsEl.getAttribute('data-delete-route-template') || '';
        const initialKpiStats = JSON.parse(directOnlineGroupsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(directOnlineGroupsEl.getAttribute('data-labels') || '{}');
        const csrfToken = directOnlineGroupsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(DirectOnlineGroupsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                showRouteTemplate,
                deleteRouteTemplate,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(directOnlineGroupsEl);
    }

    const citiesEl = document.querySelector('[data-vue-app="CitiesDataTable"]');
    if (citiesEl && !citiesEl.dataset.vueMounted) {
        citiesEl.dataset.vueMounted = 'true';
        const fetchUrl = citiesEl.getAttribute('data-fetch-url') || '';
        const createRoute = citiesEl.getAttribute('data-create-route') || '';
        const createLabel = citiesEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = citiesEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = citiesEl.getAttribute('data-delete-route-template') || '';
        const initialKpiStats = JSON.parse(citiesEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(citiesEl.getAttribute('data-labels') || '{}');
        const csrfToken = citiesEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(CitiesDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(citiesEl);
    }

    const neighborhoodsEl = document.querySelector('[data-vue-app="NeighborhoodsDataTable"]');
    if (neighborhoodsEl && !neighborhoodsEl.dataset.vueMounted) {
        neighborhoodsEl.dataset.vueMounted = 'true';
        const fetchUrl = neighborhoodsEl.getAttribute('data-fetch-url') || '';
        const createRoute = neighborhoodsEl.getAttribute('data-create-route') || '';
        const createLabel = neighborhoodsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = neighborhoodsEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = neighborhoodsEl.getAttribute('data-delete-route-template') || '';
        const cities = JSON.parse(neighborhoodsEl.getAttribute('data-cities') || '[]');
        const initialKpiStats = JSON.parse(neighborhoodsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(neighborhoodsEl.getAttribute('data-labels') || '{}');
        const csrfToken = neighborhoodsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(NeighborhoodsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                cities,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(neighborhoodsEl);
    }

    const topicsEl = document.querySelector('[data-vue-app="TopicsDataTable"]');
    if (topicsEl && !topicsEl.dataset.vueMounted) {
        topicsEl.dataset.vueMounted = 'true';
        const fetchUrl = topicsEl.getAttribute('data-fetch-url') || '';
        const createRoute = topicsEl.getAttribute('data-create-route') || '';
        const createLabel = topicsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = topicsEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = topicsEl.getAttribute('data-delete-route-template') || '';
        const initialKpiStats = JSON.parse(topicsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(topicsEl.getAttribute('data-labels') || '{}');
        const csrfToken = topicsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(TopicsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(topicsEl);
    }

    const workgroupsEl = document.querySelector('[data-vue-app="WorkgroupsDataTable"]');
    if (workgroupsEl && !workgroupsEl.dataset.vueMounted) {
        workgroupsEl.dataset.vueMounted = 'true';
        const fetchUrl = workgroupsEl.getAttribute('data-fetch-url') || '';
        const createRoute = workgroupsEl.getAttribute('data-create-route') || '';
        const createLabel = workgroupsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = workgroupsEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = workgroupsEl.getAttribute('data-delete-route-template') || '';
        const committees = JSON.parse(workgroupsEl.getAttribute('data-committees') || '[]');
        const initialKpiStats = JSON.parse(workgroupsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(workgroupsEl.getAttribute('data-labels') || '{}');
        const csrfToken = workgroupsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(WorkgroupsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                committees,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(workgroupsEl);
    }

    const serviceCommitteesEl = document.querySelector('[data-vue-app="ServiceCommitteesDataTable"]');
    if (serviceCommitteesEl && !serviceCommitteesEl.dataset.vueMounted) {
        serviceCommitteesEl.dataset.vueMounted = 'true';
        const fetchUrl = serviceCommitteesEl.getAttribute('data-fetch-url') || '';
        const createRoute = serviceCommitteesEl.getAttribute('data-create-route') || '';
        const createLabel = serviceCommitteesEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = serviceCommitteesEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = serviceCommitteesEl.getAttribute('data-delete-route-template') || '';
        const initialKpiStats = JSON.parse(serviceCommitteesEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(serviceCommitteesEl.getAttribute('data-labels') || '{}');
        const csrfToken = serviceCommitteesEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(ServiceCommitteesDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(serviceCommitteesEl);
    }

    const serviceBodiesEl = document.querySelector('[data-vue-app="ServiceBodiesDataTable"]');
    if (serviceBodiesEl && !serviceBodiesEl.dataset.vueMounted) {
        serviceBodiesEl.dataset.vueMounted = 'true';
        const fetchUrl = serviceBodiesEl.getAttribute('data-fetch-url') || '';
        const createRoute = serviceBodiesEl.getAttribute('data-create-route') || '';
        const createLabel = serviceBodiesEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = serviceBodiesEl.getAttribute('data-edit-route-template') || '';
        const showRouteTemplate = serviceBodiesEl.getAttribute('data-show-route-template') || '';
        const deleteRouteTemplate = serviceBodiesEl.getAttribute('data-delete-route-template') || '';
        const days = JSON.parse(serviceBodiesEl.getAttribute('data-days') || '[]');
        const initialKpiStats = JSON.parse(serviceBodiesEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(serviceBodiesEl.getAttribute('data-labels') || '{}');
        const csrfToken = serviceBodiesEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(ServiceBodiesDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                showRouteTemplate,
                deleteRouteTemplate,
                days,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(serviceBodiesEl);
    }

    const usersEl = document.querySelector('[data-vue-app="UsersDataTable"]');
    if (usersEl && !usersEl.dataset.vueMounted) {
        usersEl.dataset.vueMounted = 'true';
        const fetchUrl = usersEl.getAttribute('data-fetch-url') || '';
        const createRoute = usersEl.getAttribute('data-create-route') || '';
        const createLabel = usersEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = usersEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = usersEl.getAttribute('data-delete-route-template') || '';
        const impersonateRouteTemplate = usersEl.getAttribute('data-impersonate-route-template') || '';
        const canImpersonate = usersEl.getAttribute('data-can-impersonate') === 'true';
        const roles = JSON.parse(usersEl.getAttribute('data-roles') || '[]');
        const initialKpiStats = JSON.parse(usersEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(usersEl.getAttribute('data-labels') || '{}');
        const csrfToken = usersEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(UsersDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                impersonateRouteTemplate,
                canImpersonate,
                roles,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(usersEl);
    }

    const subscribersEl = document.querySelector('[data-vue-app="SubscribersDataTable"]');
    if (subscribersEl && !subscribersEl.dataset.vueMounted) {
        subscribersEl.dataset.vueMounted = 'true';
        const fetchUrl = subscribersEl.getAttribute('data-fetch-url') || '';
        const createRoute = subscribersEl.getAttribute('data-create-route') || '';
        const createLabel = subscribersEl.getAttribute('data-create-label') || '';
        const exportRoute = subscribersEl.getAttribute('data-export-route') || '';
        const toggleRouteTemplate = subscribersEl.getAttribute('data-toggle-route-template') || '';
        const deleteRouteTemplate = subscribersEl.getAttribute('data-delete-route-template') || '';
        const getIdsUrl = subscribersEl.getAttribute('data-get-ids-url') || '';
        const verifyBatchUrl = subscribersEl.getAttribute('data-verify-batch-url') || '';
        const initialKpiStats = JSON.parse(subscribersEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(subscribersEl.getAttribute('data-labels') || '{}');
        const csrfToken = subscribersEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(SubscribersDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                exportRoute,
                toggleRouteTemplate,
                deleteRouteTemplate,
                getIdsUrl,
                verifyBatchUrl,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(subscribersEl);
    }

    const permissionsEl = document.querySelector('[data-vue-app="PermissionsDataTable"]');
    if (permissionsEl && !permissionsEl.dataset.vueMounted) {
        permissionsEl.dataset.vueMounted = 'true';
        const fetchUrl = permissionsEl.getAttribute('data-fetch-url') || '';
        const createRoute = permissionsEl.getAttribute('data-create-route') || '';
        const createLabel = permissionsEl.getAttribute('data-create-label') || '';
        const editRouteTemplate = permissionsEl.getAttribute('data-edit-route-template') || '';
        const deleteRouteTemplate = permissionsEl.getAttribute('data-delete-route-template') || '';
        const categories = JSON.parse(permissionsEl.getAttribute('data-categories') || '[]');
        const initialKpiStats = JSON.parse(permissionsEl.getAttribute('data-kpi-stats') || '{}');
        const labels = JSON.parse(permissionsEl.getAttribute('data-labels') || '{}');
        const csrfToken = permissionsEl.getAttribute('data-csrf-token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const app = createApp({
            render: () => h(PermissionsDataTable, {
                fetchUrl,
                createRoute,
                createLabel,
                editRouteTemplate,
                deleteRouteTemplate,
                categories,
                initialKpiStats,
                labels,
                csrfToken
            })
        });
        app.mount(permissionsEl);
    }

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
