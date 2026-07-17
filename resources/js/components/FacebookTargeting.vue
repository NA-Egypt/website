<template>
  <div class="container-fluid py-4">
    <!-- Notifications / Success Alerts -->
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = ''" aria-label="Close"></button>
    </div>

    <div class="row g-4 mb-4">
      <!-- Map Visualization Card -->
      <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-map-fill text-primary me-2"></i>{{ labels.mapTitle }}
            </h5>
            <div class="text-muted small">{{ labels.mapSubtitle }}</div>
          </div>
          <div class="card-body p-3">
            <div id="map" style="height: 500px; width: 100%; border-radius: 12px; border: 1px solid rgba(0,0,0,0.1); z-index: 1;"></div>
          </div>
        </div>
      </div>

      <!-- Controls and Stats Card -->
      <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-transparent py-3">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-sliders text-primary me-2"></i>{{ labels.metricsTitle }}
            </h5>
          </div>
          <div class="card-body d-flex flex-column justify-content-between">
            <!-- Stats Grid -->
            <div>
              <div class="row g-2 mb-4">
                <div class="col-6">
                  <div class="p-3 border rounded text-center bg-light">
                    <div class="text-muted small mb-1">{{ labels.activeGroups }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ stats.activeCount }}</h3>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 border rounded text-center bg-light">
                    <div class="text-muted small mb-1">{{ labels.accuracyRate }}</div>
                    <h3 class="fw-bold mb-0 text-success">{{ stats.accuracy }}%</h3>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 border rounded text-center bg-light" :class="{ 'bg-warning-subtle': stats.overlapCount > 0 }">
                    <div class="text-muted small mb-1">{{ labels.overlappingAreas }}</div>
                    <h3 class="fw-bold mb-0 text-warning">{{ stats.overlapCount }}</h3>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 border rounded text-center bg-light">
                    <div class="text-muted small mb-1">{{ labels.needsPreciseLink }}</div>
                    <h3 class="fw-bold mb-0 text-danger">{{ stats.impreciseCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="alert alert-warning border-0 small mb-4 py-2.5">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                {{ labels.alertWarn }}
              </div>
              
              <p class="text-muted small">
                {{ labels.helpText }}
              </p>
            </div>

            <!-- Sync Action Button -->
            <div class="mt-4" v-if="isSuperAdmin">
              <form :action="syncRoute" method="POST" @submit="showLoadingState">
                <input type="hidden" name="_token" :value="csrfToken" />
                <button type="submit" :disabled="syncing" class="btn btn-primary w-100 py-2.5 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                  <span v-if="syncing" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  <i v-else class="bi bi-arrow-repeat"></i>
                  <span>{{ syncing ? labels.processing : labels.syncText }}</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card shadow-sm border-0">
      <form :action="downloadRoute" method="POST" id="downloadForm">
        <input type="hidden" name="_token" :value="csrfToken" />
        <!-- Hidden inputs for selected groups data during download submission -->
        <template v-for="g in groups" :key="g.id">
          <input 
            v-if="selectedIds.includes(g.id)" 
            type="hidden" 
            name="selected_groups[]" 
            :value="`${g.id}:${g.lat}:${g.lng}:${g.name}`" 
          />
          <input 
            v-if="selectedIds.includes(g.id)" 
            type="hidden" 
            :name="`radii[${g.id}]`" 
            :value="g.radius" 
          />
        </template>

        <div class="card-header bg-transparent py-3">
          <div class="row g-3 align-items-center force-ltr">
            <div class="col-md-2">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" v-model="search" class="form-control border-start-0" :placeholder="labels.searchPlaceholder">
              </div>
            </div>
            <div class="col-md-2">
              <select v-model="cityFilter" class="form-select form-select-sm">
                <option value="">{{ labels.allCities }}</option>
                <option v-for="c in uniqueCities" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <div class="col-md-2">
              <select v-model="sourceFilter" class="form-select form-select-sm">
                <option value="">{{ labels.allSources }}</option>
                <option value="parsed">{{ labels.parsedCoords }}</option>
                <option value="fallback">{{ labels.anyFallback }}</option>
              </select>
            </div>
            <div class="col-md-2 text-start">
              <div class="form-check form-switch mt-1">
                <input class="form-check-input" type="checkbox" id="toggleImpreciseOnly" v-model="impreciseOnly">
                <label class="form-check-label small" for="toggleImpreciseOnly">{{ labels.impreciseOnly }}</label>
              </div>
            </div>
            <div class="col-md-2 text-end">
              <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-download"></i>
                {{ labels.downloadCsv }}
              </button>
            </div>
            <div class="col-md-2 text-end">
              <button type="button" @click="generateStaticMap" :disabled="staticMapLoading" class="btn btn-info btn-sm w-100 rounded-pill shadow-sm text-white d-flex align-items-center justify-content-center gap-2">
                <span v-if="staticMapLoading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i v-else class="bi bi-link-45deg"></i>
                {{ labels.copyMapUrl }}
              </button>
            </div>
          </div>
        </div>

        <!-- DataTable -->
        <div class="table-responsive">
          <vue3-datatable
            :rows="filteredRows"
            :columns="columns"
            :loading="loading"
            :pageSize="15"
            :pageSizeOptions="[10, 15, 20, 50]"
            :showPageSize="true"
            :showNumbersCount="5"
            :page="page"
            :totalRows="filteredRows.length"
            :sortColumn="sortColumn"
            :sortDirection="sortDirection"
            :sortable="true"
            :pagination="true"
            @change="changeParams"
            @page-change="onPageChange"
            @page-size-change="onPageSizeChange"
            :paginationInfo="labels.paginationInfo"
            :noDataContent="labels.noDataContent"
            class="alt-pagination bh-table-hover align-middle mb-0 text-center"
          >
            <template #firstArrow>{{ isAr ? 'الأول' : 'First' }}</template>
            <template #lastArrow>{{ isAr ? 'الأخير' : 'Last' }}</template>
            <template #previousArrow>{{ isAr ? 'السابق' : 'Prev' }}</template>
            <template #nextArrow>{{ isAr ? 'التالي' : 'Next' }}</template>
            <!-- Checkbox Column -->
            <template #checkbox="data">
              <input
                type="checkbox"
                :value="data.value.id"
                :checked="selectedIds.includes(data.value.id)"
                @change="toggleSelection(data.value.id)"
                class="form-check-input row-checkbox"
              />
            </template>

            <!-- Group Name Column -->
            <template #name="data">
              <div :class="isAr ? 'ar-cell' : 'en-cell'">
                <span class="fw-bold">
                  {{ data.value.name }}
                  <i 
                    v-if="isImprecise(data.value.source)" 
                    class="bi bi-exclamation-triangle-fill text-warning ms-1" 
                    :title="labels.fallbackTooltip"
                  ></i>
                </span>
              </div>
            </template>

            <!-- Location Link Column -->
            <template #location_url="data">
              <div class="en-cell">
                <a v-if="data.value.location_url" :href="data.value.location_url" target="_blank" class="text-truncate d-inline-block text-primary text-decoration-underline" style="max-width: 150px;" :title="data.value.location_url">
                  <i class="bi bi-box-arrow-up-right me-1"></i>Link
                </a>
                <span v-else class="text-danger small"><i class="bi bi-link-45deg"></i>{{ labels.missing }}</span>
              </div>
            </template>

            <!-- Coordinates Column -->
            <template #coordinates="data">
              <div class="en-cell">
                <span class="small font-monospace" v-if="data.value.lat && data.value.lng">
                  {{ round(data.value.lat) }}, {{ round(data.value.lng) }}
                </span>
                <span v-else class="text-danger">{{ labels.none }}</span>
              </div>
            </template>

            <!-- Source Column -->
            <template #source="data">
              <span :class="['badge', getSourceBadgeClass(data.value.source)]">
                {{ getSourceLabel(data.value.source) }}
              </span>
            </template>

            <!-- Radius Column -->
            <template #radius="data">
              <div class="d-flex justify-content-center align-items-center">
                <input 
                  type="number" 
                  v-model.number="data.value.radius" 
                  class="form-control form-control-sm radius-input text-center" 
                  min="1" 
                  max="80" 
                  @input="handleRadiusChange(data.value)"
                  style="width: 80px;"
                />
              </div>
            </template>

          </vue3-datatable>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import axios from 'axios';

const props = defineProps({
  initialGroups: {
    type: Array,
    required: true
  },
  syncRoute: String,
  downloadRoute: String,
  staticMapRoute: String,
  csrfToken: String,
  isSuperAdmin: Boolean
});

const groups = ref(props.initialGroups.map(g => ({
  ...g,
  marker: null,
  circle: null
})));

const search = ref('');
const cityFilter = ref('');
const sourceFilter = ref('');
const impreciseOnly = ref(false);
const page = ref(1);
const sortColumn = ref('name');
const sortDirection = ref('asc');

const changeParams = (newParams) => {
  if (newParams.current_page) page.value = newParams.current_page;
  if (newParams.sort_column) sortColumn.value = newParams.sort_column;
  if (newParams.sort_direction) sortDirection.value = newParams.sort_direction;
};

const onPageChange = (p) => { page.value = p; };
const onPageSizeChange = () => { page.value = 1; };

const selectedIds = ref(props.initialGroups.map(g => g.id));
const successMessage = ref('');
const syncing = ref(false);
const staticMapLoading = ref(false);
const loading = ref(false);

let map = null;
const isAr = computed(() => document.documentElement.lang === 'ar');

const labels = computed(() => {
  if (isAr.value) {
    return {
      mapTitle: 'خريطة المناطق المستهدفة',
      mapSubtitle: 'تمثل الدوائر نطاق استهداف الإعلانات',
      metricsTitle: 'مقاييس الاستهداف',
      activeGroups: 'المجموعات النشطة',
      accuracyRate: 'معدل الدقة',
      overlappingAreas: 'المناطق المتداخلة',
      needsPreciseLink: 'بحاجة لرابط دقيق',
      alertWarn: 'استهداف المناطق المتداخلة يزيد من تكلفة الإعلان والمنافسة ضد مجموعاتك الخاصة.',
      helpText: 'لتحسين دقة الإعلانات وتقليل الهدر في الميزانية، يرجى إضافة روابط خرائط جوجل محددة للمجموعات التي تستخدم حاليًا إحداثيات بديلة للمدينة أو الحي.',
      syncText: 'مزامنة وحل الروابط',
      processing: 'جاري المعالجة...',
      searchPlaceholder: 'بحث المجموعات والعناوين...',
      allCities: 'كل المدن',
      allSources: 'كل المصادر',
      parsedCoords: 'إحداثيات مستخرجة',
      anyFallback: 'أي موقع احتياطي',
      impreciseOnly: 'المواقع غير الدقيقة فقط',
      downloadCsv: 'تحميل CSV',
      copyMapUrl: 'نسخ رابط الخريطة الموقع',
      missing: 'مفقود',
      none: 'لا يوجد',
      fallbackTooltip: 'تم استخدام موقع احتياطي',
      paginationInfo: 'يظهر {0} إلى {1} من {2} المدخلات',
      noDataContent: 'لا توجد بيانات متاحة',
      confirmMapCopy: 'تم نسخ رابط الخريطة بنجاح!',
      errorMapCopy: 'فشل إنشاء رابط الخريطة.'
    };
  }
  return {
    mapTitle: 'Targeting Areas Map',
    mapSubtitle: 'Circles represent the ad targeting radius',
    metricsTitle: 'Targeting Metrics',
    activeGroups: 'Active Groups',
    accuracyRate: 'Accuracy Rate',
    overlappingAreas: 'Overlapping Areas',
    needsPreciseLink: 'Needs Precise Link',
    alertWarn: 'Targeting overlapping regions increases ad spend competition against your own groups.',
    helpText: 'To improve ad accuracy and reduce budget waste, add specific Google Maps links to groups currently utilizing City or Neighborhood fallback coordinates.',
    syncText: 'Sync & Resolve URLs',
    processing: 'Processing...',
    searchPlaceholder: 'Search groups, addresses...',
    allCities: 'All Cities',
    allSources: 'All Sources',
    parsedCoords: 'Parsed Coordinates',
    anyFallback: 'Any Fallback',
    impreciseOnly: 'Imprecise Only',
    downloadCsv: 'Download CSV',
    copyMapUrl: 'Copy Signed Map URL',
    missing: 'Missing',
    none: 'None',
    fallbackTooltip: 'Fallback location used',
    paginationInfo: 'Showing {0} to {1} of {2} entries',
    noDataContent: 'No data available',
    confirmMapCopy: 'Signed Static Map URL copied to clipboard successfully!',
    errorMapCopy: 'Failed to generate Static Map URL.'
  };
});

const columns = computed(() => {
  return [
    { field: 'checkbox', title: '', sort: false, width: '40px' },
    { field: 'name', title: isAr.value ? 'اسم المجموعة' : 'Group Name', sort: true },
    { field: 'city', title: isAr.value ? 'المدينة' : 'City', sort: true },
    { field: 'neighborhood', title: isAr.value ? 'الحي' : 'Neighborhood', sort: true },
    { field: 'location_url', title: isAr.value ? 'رابط خرائط جوجل' : 'Google Maps Link', sort: true },
    { field: 'coordinates', title: isAr.value ? 'الإحداثيات' : 'Coordinates', sort: true },
    { field: 'source', title: isAr.value ? 'المصدر' : 'Source', sort: true },
    { field: 'radius', title: isAr.value ? 'نصف القطر الاستهدافي (كم)' : 'Targeting Radius (km)', sort: true }
  ];
});

const uniqueCities = computed(() => {
  const cities = groups.value.map(g => g.city);
  return [...new Set(cities)];
});

const filteredRows = computed(() => {
  const list = groups.value.filter(g => {
    const matchesSearch = !search.value || 
                          g.name.toLowerCase().includes(search.value.toLowerCase()) ||
                          g.neighborhood.toLowerCase().includes(search.value.toLowerCase());
    
    const matchesCity = !cityFilter.value || g.city === cityFilter.value;
    
    let matchesSource = true;
    if (sourceFilter.value === 'parsed') {
      matchesSource = g.source === 'parsed';
    } else if (sourceFilter.value === 'fallback') {
      matchesSource = g.source !== 'parsed';
    }

    const matchesImprecise = !impreciseOnly.value || isImprecise(g.source);

    return matchesSearch && matchesCity && matchesSource && matchesImprecise;
  });

  return list;
});

const stats = ref({
  activeCount: 0,
  accuracy: 0,
  overlapCount: 0,
  impreciseCount: 0
});

const round = (num) => (num ? Number(num).toFixed(6) : '');

const isImprecise = (source) => ['neighborhood_avg', 'city_preset', 'default'].includes(source);

const getSourceBadgeClass = (source) => {
  if (source === 'parsed') return 'badge-parsed';
  if (source === 'neighborhood_db') return 'badge-neighborhood';
  if (source === 'neighborhood_avg') return 'badge-fallback';
  if (source === 'city_db') return 'badge-neighborhood';
  if (source === 'city_preset') return 'badge-fallback';
  return 'badge-default';
};

const getSourceLabel = (source) => {
  if (isAr.value) {
    if (source === 'parsed') return 'مستخرج';
    if (source === 'neighborhood_db') return 'الحي (قاعدة البيانات)';
    if (source === 'neighborhood_avg') return 'الحي (متوسط)';
    if (source === 'city_db') return 'المدينة (قاعدة البيانات)';
    if (source === 'city_preset') return 'المدينة (افتراضي)';
    return 'افتراضي';
  }
  if (source === 'parsed') return 'Parsed';
  if (source === 'neighborhood_db') return 'Neighborhood (DB)';
  if (source === 'neighborhood_avg') return 'Neighborhood (Avg)';
  if (source === 'city_db') return 'City (DB)';
  if (source === 'city_preset') return 'City (Preset)';
  return 'Default';
};

const toggleSelection = (id) => {
  const index = selectedIds.value.indexOf(id);
  if (index > -1) {
    selectedIds.value.splice(index, 1);
  } else {
    selectedIds.value.push(id);
  }
  calculateMetrics();
};

const handleRadiusChange = (g) => {
  if (g.circle) {
    g.circle.setRadius((parseFloat(g.radius) || 5) * 1000);
  }
  calculateMetrics();
};

const showLoadingState = () => {
  syncing.value = true;
};

// Haversine formula
function getHaversineDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

const calculateMetrics = () => {
  let totalSelected = 0;
  let preciseSelected = 0;
  let impreciseCount = 0;

  const activeGroups = groups.value.filter(g => {
    const isVisible = filteredRows.value.some(fr => fr.id === g.id);
    const isChecked = selectedIds.value.includes(g.id);

    if (isVisible && isChecked) {
      totalSelected++;
      if (!isImprecise(g.source)) preciseSelected++;
      return true;
    }
    return false;
  });

  groups.value.forEach(g => {
    const isVisible = filteredRows.value.some(fr => fr.id === g.id);
    if (isVisible && isImprecise(g.source)) {
      impreciseCount++;
    }
  });

  // Calculate Overlapping Areas among active groups
  const overlappingGroupIds = new Set();
  for (let i = 0; i < activeGroups.length; i++) {
    const gA = activeGroups[i];
    const radA = parseFloat(gA.radius) || 5;

    for (let j = i + 1; j < activeGroups.length; j++) {
      const gB = activeGroups[j];
      const radB = parseFloat(gB.radius) || 5;

      const distance = getHaversineDistance(gA.lat, gA.lng, gB.lat, gB.lng);
      if (distance < radA + radB) {
        overlappingGroupIds.add(gA.id);
        overlappingGroupIds.add(gB.id);
      }
    }
  }

  // Update Google Map circle styles based on overlap state
  groups.value.forEach(g => {
    if (g.circle) {
      if (overlappingGroupIds.has(g.id)) {
        g.circle.setOptions({
          strokeWeight: 2.5
        });
      } else {
        g.circle.setOptions({
          strokeWeight: 1.5
        });
      }
    }
  });

  stats.value.activeCount = totalSelected;
  stats.value.accuracy = totalSelected > 0 ? Math.round((preciseSelected / totalSelected) * 100) : 0;
  stats.value.impreciseCount = impreciseCount;
  stats.value.overlapCount = overlappingGroupIds.size;
};

const generateStaticMap = async () => {
  staticMapLoading.value = true;
  const selectedGroupsPayload = [];
  const radiiPayload = {};

  groups.value.forEach(g => {
    const isVisible = filteredRows.value.some(fr => fr.id === g.id);
    const isChecked = selectedIds.value.includes(g.id);
    if (isVisible && isChecked) {
      selectedGroupsPayload.push(`${g.id}:${g.lat}:${g.lng}:${g.name}`);
      radiiPayload[g.id] = g.radius;
    }
  });

  if (selectedGroupsPayload.length === 0) {
    alert(isAr.value ? 'يرجى اختيار مجموعة واحدة على الأقل لإنشاء الخريطة.' : 'Please select at least one group to generate the static map.');
    staticMapLoading.value = false;
    return;
  }

  try {
    const response = await axios.post(props.staticMapRoute, {
      selected_groups: selectedGroupsPayload,
      radii: radiiPayload
    }, {
      headers: {
        'X-CSRF-TOKEN': props.csrfToken
      }
    });

    if (response.data.url) {
      await navigator.clipboard.writeText(response.data.url);
      alert(labels.value.confirmMapCopy);
    } else {
      alert(labels.value.errorMapCopy);
    }
  } catch (error) {
    console.error('Error copy signed map URL:', error);
    alert(labels.value.errorMapCopy);
  } finally {
    staticMapLoading.value = false;
  }
};

// Sync markers & circles visibility on Map when filteredRows changes
watch(filteredRows, (newRows) => {
  groups.value.forEach(g => {
    const isVisible = newRows.some(nr => nr.id === g.id);
    if (g.marker) g.marker.setMap(isVisible ? map : null);
    if (g.circle) g.circle.setMap(isVisible ? map : null);
  });
  calculateMetrics();
}, { deep: true });

const initGoogleMap = () => {
  if (typeof google === 'undefined') {
    return false;
  }
  if (map) return true; // Already initialized

  // Initialize Map centered on Egypt
  map = new google.maps.Map(document.getElementById('map'), {
    center: { lat: 26.8206, lng: 30.8025 },
    zoom: 6,
    mapTypeControl: true,
    streetViewControl: false,
    fullscreenControl: true
  });

  const bounds = new google.maps.LatLngBounds();
  let hasBounds = false;

  groups.value.forEach(g => {
    if (!isNaN(g.lat) && !isNaN(g.lng)) {
      const position = { lat: parseFloat(g.lat), lng: parseFloat(g.lng) };

      // Add Marker
      const marker = new google.maps.Marker({
        position: position,
        map: map,
        title: g.name
      });
      g.marker = marker;

      // Circle Color
      const circleColor = isImprecise(g.source) ? '#fd7e14' : '#28a745';

      // Add Circle
      const circle = new google.maps.Circle({
        strokeColor: circleColor,
        strokeOpacity: 0.8,
        strokeWeight: 1.5,
        fillColor: circleColor,
        fillOpacity: 0.15,
        map: map,
        center: position,
        radius: (parseFloat(g.radius) || 5) * 1000
      });
      g.circle = circle;

      // Info window content
      const infoWindow = new google.maps.InfoWindow({
        content: `
          <div style="font-family: Cairo, sans-serif; min-width: 150px;">
            <h6 class="fw-bold mb-1">${g.name}</h6>
            <p class="mb-1 text-muted small">${g.city}</p>
            <p class="mb-1 small font-monospace">${parseFloat(g.lat).toFixed(5)}, ${parseFloat(g.lng).toFixed(5)}</p>
            <span class="badge small mb-2" style="background-color: ${circleColor}; color: white;">${g.source.toUpperCase()}</span>
          </div>
        `
      });

      marker.addListener('click', () => {
        infoWindow.open(map, marker);
      });

      bounds.extend(position);
      hasBounds = true;
    }
  });

  if (hasBounds) {
    map.fitBounds(bounds);
  }

  calculateMetrics();
  return true;
};

onMounted(() => {
  if (!initGoogleMap()) {
    // If not loaded yet, wait for window load event
    window.addEventListener('load', () => {
      initGoogleMap();
    });
    // Or poll periodically to detect when google object becomes available
    let attempts = 0;
    const interval = setInterval(() => {
      attempts++;
      if (typeof google !== 'undefined') {
        initGoogleMap();
        clearInterval(interval);
      }
      if (attempts > 30) {
        clearInterval(interval);
      }
    }, 200);
  }
});
</script>

<style scoped>
.badge-parsed { background-color: #28a745; color: white; }
.badge-fallback { background-color: #fd7e14; color: white; }
.badge-neighborhood { background-color: #007bff; color: white; }
.badge-city { background-color: #ffc107; color: #212529; }
.badge-default { background-color: #6c757d; color: white; }
</style>

<style>
.ar-cell {
  text-align: right !important;
  direction: rtl !important;
  display: block;
}
.en-cell {
  text-align: left !important;
  direction: ltr !important;
  display: block;
}
</style>
