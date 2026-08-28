<template>
  <div class="service-body-map-container" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Top Stats / Control Header -->
    <div class="map-header-bar mb-3 p-3 rounded-4 shadow-sm">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <!-- Title & Badges -->
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon-box rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
            <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
          </div>
          <div>
            <h4 class="fw-bold mb-0 text-dark">
              {{ isRtl ? 'خريطة مناطق ولجان خدمة الزمالة' : 'Service Bodies & Territories Map' }}
            </h4>
            <p class="text-muted small mb-0">
              {{ isRtl ? 'تحديد وتوزيع النطاقات الجغرافية للمجموعات التابعة لكل منطقة ومنتدى خدمة' : 'Geographic boundary zones and groups belonging to each NA Service Body' }}
            </p>
          </div>
        </div>

        <!-- Summary Metric Pills -->
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="metric-pill">
            <span class="metric-val text-primary">{{ serviceBodies.length }}</span>
            <span class="metric-lbl">{{ isRtl ? 'منطقة ومنتدى' : 'Service Bodies' }}</span>
          </div>
          <div class="metric-pill">
            <span class="metric-val text-success">{{ totalGroupsCount }}</span>
            <span class="metric-lbl">{{ isRtl ? 'مجموعة حضورية' : 'Physical Groups' }}</span>
          </div>
          <div class="metric-pill">
            <span class="metric-val text-info">{{ totalMeetingsCount }}</span>
            <span class="metric-lbl">{{ isRtl ? 'اجتماع أسبوعي' : 'Weekly Meetings' }}</span>
          </div>

          <!-- Refresh live data button -->
          <button 
            class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center gap-1 px-3 py-2"
            @click="refreshLiveMapData"
            :disabled="refreshing"
            :title="isRtl ? 'تحديث البيانات مباشرة من قاعدة البيانات' : 'Refresh live data from database'"
          >
            <i class="bi bi-arrow-clockwise" :class="{ 'spin-animation': refreshing }"></i>
            <span class="d-none d-md-inline">{{ isRtl ? 'تحديث البيانات' : 'Refresh Data' }}</span>
          </button>

          <!-- Mobile Sidebar Toggle Button -->
          <button 
            class="btn btn-outline-primary d-lg-none d-flex align-items-center gap-2 rounded-pill px-3 py-2"
            @click="isSidebarOpen = !isSidebarOpen"
          >
            <i class="bi bi-sliders"></i>
            <span>{{ isRtl ? 'القائمة والتصفية' : 'Legend & Filter' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Map & Legend Grid -->
    <div class="map-layout-wrapper">
      <!-- Sidebar / Interactive Legend -->
      <aside 
        class="map-sidebar rounded-4 shadow-sm p-3"
        :class="{ 'sidebar-open': isSidebarOpen }"
      >
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-layers-fill text-primary"></i>
            {{ isRtl ? 'مناطق الخدمة' : 'Service Bodies' }}
          </h6>
          <button 
            class="btn-close d-lg-none" 
            @click="isSidebarOpen = false"
            aria-label="Close"
          ></button>
        </div>

        <!-- Search Bar with Precise RTL/LTR layout -->
        <div class="search-box mb-3 position-relative">
          <i class="bi bi-search search-icon position-absolute text-muted"></i>
          <input 
            type="text" 
            v-model="searchQuery" 
            class="form-control form-control-sm rounded-pill search-input" 
            :placeholder="isRtl ? 'بحث عن منطقة، مجموعة، مدينة...' : 'Search area, group, city...'"
          />
          <button 
            v-if="searchQuery" 
            class="btn btn-link btn-sm position-absolute clear-btn text-muted p-0" 
            @click="searchQuery = ''"
            :title="isRtl ? 'مسح البحث' : 'Clear search'"
          >
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>

        <!-- Bulk Controls & Layer Options -->
        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
          <div class="d-flex gap-1">
            <button 
              class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 text-nowrap"
              @click="selectAllServiceBodies"
            >
              {{ isRtl ? 'تحديد الكل' : 'Select All' }}
            </button>
            <button 
              class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 text-nowrap"
              @click="deselectAllServiceBodies"
            >
              {{ isRtl ? 'إلغاء الكل' : 'Deselect All' }}
            </button>
          </div>

          <button 
            class="btn btn-xs btn-light rounded-pill py-1 px-2 d-flex align-items-center gap-1 text-nowrap"
            @click="resetMapView"
            :title="isRtl ? 'إعادة ضبط المنظور لكامل مصر' : 'Reset view to all Egypt'"
          >
            <i class="bi bi-arrows-fullscreen"></i>
            <span>{{ isRtl ? 'ضبط الخريطة' : 'Reset View' }}</span>
          </button>
        </div>

        <!-- Layer Display Toggles -->
        <div class="layer-toggles-bar p-2 mb-3 rounded-3 d-flex align-items-center justify-content-between gap-1">
          <label class="layer-toggle-item cursor-pointer" for="togglePolygons">
            <input 
              class="layer-toggle-switch" 
              type="checkbox" 
              id="togglePolygons" 
              v-model="showPolygons" 
              @change="updateLayerVisibility"
            >
            <span class="layer-toggle-label">{{ isRtl ? 'الحدود' : 'Boundaries' }}</span>
          </label>

          <label class="layer-toggle-item cursor-pointer" for="toggleLabels">
            <input 
              class="layer-toggle-switch" 
              type="checkbox" 
              id="toggleLabels" 
              v-model="showTerritoryLabels" 
              @change="updateLayerVisibility"
            >
            <span class="layer-toggle-label">{{ isRtl ? 'الأسماء' : 'Labels' }}</span>
          </label>

          <label class="layer-toggle-item cursor-pointer" for="toggleMarkers">
            <input 
              class="layer-toggle-switch" 
              type="checkbox" 
              id="toggleMarkers" 
              v-model="showMarkers" 
              @change="updateLayerVisibility"
            >
            <span class="layer-toggle-label">{{ isRtl ? 'المجموعات' : 'Pins' }}</span>
          </label>
        </div>

        <!-- Service Bodies List -->
        <div class="service-bodies-list custom-scrollbar">
          <div 
            v-for="sb in filteredServiceBodies" 
            :key="sb.id"
            class="service-body-card mb-2 p-2 rounded-3 transition-all"
            :class="{ 
              'active-sb': isSelected(sb.id),
              'dimmed-sb': !isSelected(sb.id),
              'highlighted-sb': highlightedSbId === sb.id 
            }"
            @mouseenter="highlightServiceBody(sb.id)"
            @mouseleave="highlightServiceBody(null)"
          >
            <div class="d-flex align-items-center justify-content-between gap-2">
              <!-- Checkbox & Color Indicator -->
              <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                <input 
                  type="checkbox" 
                  class="form-check-input sb-checkbox mt-0 cursor-pointer flex-shrink-0"
                  :style="{ accentColor: sb.color, borderColor: sb.color }"
                  :checked="isSelected(sb.id)"
                  @change="toggleServiceBody(sb.id)"
                />
                
                <span 
                  class="color-dot rounded-circle flex-shrink-0"
                  :style="{ backgroundColor: sb.color, boxShadow: `0 0 8px ${sb.color}66` }"
                ></span>

                <div class="sb-info text-truncate cursor-pointer" @click="toggleServiceBody(sb.id)">
                  <div class="sb-name fw-bold text-truncate" :title="sb.name">
                    {{ sb.name }}
                  </div>
                  <div class="sb-sub text-muted extra-small">
                    <span>{{ sb.groups_count }} {{ isRtl ? 'مجموعة' : 'groups' }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ sb.meetings_count }} {{ isRtl ? 'اجتماع' : 'meetings' }}</span>
                  </div>
                </div>
              </div>

              <!-- Zoom to SB Button -->
              <button 
                class="btn btn-sm btn-icon-zoom rounded-circle p-1 text-muted flex-shrink-0"
                :title="isRtl ? 'تكبير وضبط المنظور على هذه المنطقة' : 'Zoom into this service body'"
                @click.stop="zoomToServiceBody(sb)"
              >
                <i class="bi bi-crosshair fs-6"></i>
              </button>
            </div>

            <!-- Groups Accordion inside Service Body (Expandable) -->
            <div 
              v-if="expandedSbId === sb.id" 
              class="groups-sublist mt-2 pt-2 border-top extra-small"
            >
              <div 
                v-for="group in sb.groups" 
                :key="group.id"
                class="group-subitem p-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer hover-bg-light mb-1"
                @click="focusGroupMarker(group)"
              >
                <span class="text-truncate flex-grow-1 me-2" :title="group.name">
                  <i class="bi bi-geo-alt me-1 text-muted"></i>
                  {{ group.name }}
                  <span v-if="group.neighborhood" class="text-muted">({{ group.neighborhood }})</span>
                </span>
                <span class="badge bg-light text-dark rounded-pill flex-shrink-0">{{ group.meetings_count }}</span>
              </div>
            </div>
          </div>

          <!-- Empty state when searching -->
          <div v-if="filteredServiceBodies.length === 0" class="text-center py-4 text-muted">
            <i class="bi bi-search fs-3 d-block mb-1"></i>
            <span class="small">{{ isRtl ? 'لا توجد نتائج مطابقة' : 'No matching results found' }}</span>
          </div>
        </div>
      </aside>

      <!-- Map Canvas Area -->
      <main class="map-canvas-container rounded-4 shadow-sm position-relative overflow-hidden">
        <!-- Google Map Container -->
        <div id="service-body-google-map" class="w-100 h-100"></div>

        <!-- Loading Overlay -->
        <div v-if="loading" class="map-loading-overlay d-flex flex-column align-items-center justify-content-center">
          <div class="spinner-border text-primary mb-2" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span class="fw-bold text-dark">{{ isRtl ? 'جاري تحميل خريطة المناطق ولجان الخدمة...' : 'Loading Service Body Map...' }}</span>
        </div>

        <!-- Floating Quick Map Tools -->
        <div class="map-floating-tools position-absolute top-0 m-3 d-flex flex-column gap-2" :class="isRtl ? 'start-0' : 'end-0'">
          <button 
            class="btn btn-white shadow-sm rounded-circle tool-btn d-flex align-items-center justify-content-center"
            @click="resetMapView"
            :title="isRtl ? 'الرؤية الكاملة لجمهورية مصر' : 'Full Egypt View'"
          >
            <i class="bi bi-globe-americas"></i>
          </button>
          <button 
            class="btn btn-white shadow-sm rounded-circle tool-btn d-flex align-items-center justify-content-center"
            @click="toggleMapType"
            :title="isRtl ? 'تغيير نمط الخريطة (قمر صناعي / عادي)' : 'Toggle Satellite / Roadmap'"
          >
            <i class="bi" :class="mapType === 'roadmap' ? 'bi-image' : 'bi-map'"></i>
          </button>
        </div>

        <!-- Selected Service Body Floating Pill (Bottom Center) -->
        <div 
          v-if="activeServiceBodyDetail" 
          class="active-sb-floating-card position-absolute bottom-0 start-50 translate-middle-x mb-4 p-3 rounded-4 shadow-lg bg-white border d-flex align-items-center justify-content-between gap-3 animate-fade-in"
          :dir="isRtl ? 'rtl' : 'ltr'"
        >
          <div class="d-flex align-items-center gap-3">
            <div 
              class="active-sb-color-box rounded-3 d-flex align-items-center justify-content-center text-white fw-bold px-3 py-2 flex-shrink-0"
              :style="{ backgroundColor: activeServiceBodyDetail.color }"
            >
              {{ activeServiceBodyDetail.groups_count }} {{ isRtl ? 'مجموعة' : 'Groups' }}
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-dark">{{ activeServiceBodyDetail.name }}</h6>
              <p class="text-muted extra-small mb-0">
                <span>{{ activeServiceBodyDetail.meetings_count }} {{ isRtl ? 'اجتماع أسبوعي' : 'Weekly meetings' }}</span>
                <span v-if="activeServiceBodyDetail.location" class="mx-1">• {{ activeServiceBodyDetail.location }}</span>
              </p>
            </div>
          </div>
          <button 
            class="btn-close flex-shrink-0" 
            @click="activeServiceBodyDetail = null"
            aria-label="Close"
          ></button>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({ service_bodies: [], total_service_bodies: 0, total_groups: 0, total_meetings: 0, locale: 'ar' })
  }
});

const serviceBodies = ref(props.initialData.service_bodies || []);
const isRtl = computed(() => (props.initialData.locale || 'ar') === 'ar');
const selectedSbIds = ref(new Set(serviceBodies.value.map(sb => sb.id)));
const showPolygons = ref(true);
const showTerritoryLabels = ref(true);
const showMarkers = ref(true);
const searchQuery = ref('');
const isSidebarOpen = ref(false);
const highlightedSbId = ref(null);
const expandedSbId = ref(null);
const activeServiceBodyDetail = ref(null);
const mapType = ref('roadmap');
const loading = ref(true);
const refreshing = ref(false);

let map = null;
let infoWindow = null;
const mapOverlays = {
  polygons: new Map(), // sbId -> google.maps.Polygon
  markers: new Map(),  // groupId -> google.maps.Marker
  circles: new Map(),  // sbId -> google.maps.Circle
  labels: new Map()    // sbId -> Custom Territory Label Overlay
};

const totalGroupsCount = computed(() => {
  return serviceBodies.value.reduce((acc, sb) => acc + (sb.groups_count || 0), 0);
});

const totalMeetingsCount = computed(() => {
  return serviceBodies.value.reduce((acc, sb) => acc + (sb.meetings_count || 0), 0);
});

const filteredServiceBodies = computed(() => {
  if (!searchQuery.value.trim()) return serviceBodies.value;
  const q = searchQuery.value.toLowerCase().trim();
  return serviceBodies.value.filter(sb => {
    const sbMatch = (sb.name && sb.name.toLowerCase().includes(q)) ||
                    (sb.ar_name && sb.ar_name.toLowerCase().includes(q)) ||
                    (sb.en_name && sb.en_name.toLowerCase().includes(q));
    if (sbMatch) return true;

    // Check if any group within matches
    return sb.groups && sb.groups.some(g => 
      (g.name && g.name.toLowerCase().includes(q)) ||
      (g.city && g.city.toLowerCase().includes(q)) ||
      (g.neighborhood && g.neighborhood.toLowerCase().includes(q)) ||
      (g.address && g.address.toLowerCase().includes(q))
    );
  });
});

const isSelected = (sbId) => selectedSbIds.value.has(sbId);

const toggleServiceBody = (sbId) => {
  if (selectedSbIds.value.has(sbId)) {
    selectedSbIds.value.delete(sbId);
  } else {
    selectedSbIds.value.add(sbId);
  }
  selectedSbIds.value = new Set(selectedSbIds.value);
  updateLayerVisibility();
};

const selectAllServiceBodies = () => {
  selectedSbIds.value = new Set(serviceBodies.value.map(sb => sb.id));
  updateLayerVisibility();
};

const deselectAllServiceBodies = () => {
  selectedSbIds.value = new Set();
  updateLayerVisibility();
};

const highlightServiceBody = (sbId) => {
  highlightedSbId.value = sbId;
  if (!map) return;

  serviceBodies.value.forEach(sb => {
    const poly = mapOverlays.polygons.get(sb.id);
    const circle = mapOverlays.circles.get(sb.id);
    const label = mapOverlays.labels.get(sb.id);

    if (poly) {
      poly.setOptions({
        fillOpacity: sbId === sb.id ? 0.38 : (selectedSbIds.value.has(sb.id) ? 0.18 : 0),
        strokeWeight: sbId === sb.id ? 3.5 : 2
      });
    }
    if (circle) {
      circle.setOptions({
        fillOpacity: sbId === sb.id ? 0.38 : (selectedSbIds.value.has(sb.id) ? 0.18 : 0),
        strokeWeight: sbId === sb.id ? 3.5 : 2
      });
    }
    if (label && label.div) {
      if (sbId === sb.id) {
        label.div.classList.add('territory-badge-highlighted');
      } else {
        label.div.classList.remove('territory-badge-highlighted');
      }
    }
  });
};

/* ==========================================================================
   Convex Hull & Territory Calculation
   ========================================================================== */

const crossProduct = (o, a, b) => {
  return (a.lng - o.lng) * (b.lat - o.lat) - (a.lat - o.lat) * (b.lng - o.lng);
};

const computeConvexHull = (points) => {
  if (points.length <= 2) return points;

  const sorted = points.slice().sort((a, b) => {
    return a.lng === b.lng ? a.lat - b.lat : a.lng - b.lng;
  });

  const lower = [];
  for (let i = 0; i < sorted.length; i++) {
    while (lower.length >= 2 && crossProduct(lower[lower.length - 2], lower[lower.length - 1], sorted[i]) <= 0) {
      lower.pop();
    }
    lower.push(sorted[i]);
  }

  const upper = [];
  for (let i = sorted.length - 1; i >= 0; i--) {
    while (upper.length >= 2 && crossProduct(upper[upper.length - 2], upper[upper.length - 1], sorted[i]) <= 0) {
      upper.pop();
    }
    upper.push(sorted[i]);
  }

  lower.pop();
  upper.pop();
  return lower.concat(upper);
};

const bufferConvexHull = (hullPoints, bufferFactor = 0.018) => {
  if (hullPoints.length < 3) return hullPoints;

  let sumLat = 0;
  let sumLng = 0;
  hullPoints.forEach(p => {
    sumLat += p.lat;
    sumLng += p.lng;
  });
  const cLat = sumLat / hullPoints.length;
  const cLng = sumLng / hullPoints.length;

  return hullPoints.map(p => {
    const dLat = p.lat - cLat;
    const dLng = p.lng - cLng;
    const dist = Math.sqrt(dLat * dLat + dLng * dLng) || 1;
    const offsetLat = (dLat / dist) * bufferFactor + dLat * 0.08;
    const offsetLng = (dLng / dist) * bufferFactor + dLng * 0.08;
    return {
      lat: p.lat + offsetLat,
      lng: p.lng + offsetLng
    };
  });
};

const createCapsulePolygon = (p1, p2, bufferRadius = 0.025) => {
  const dLat = p2.lat - p1.lat;
  const dLng = p2.lng - p1.lng;
  const len = Math.sqrt(dLat * dLat + dLng * dLng) || 0.001;

  const uLat = -dLng / len;
  const uLng = dLat / len;
  const vLat = dLat / len;
  const vLng = dLng / len;

  return [
    { lat: p1.lat + uLat * bufferRadius - vLat * bufferRadius, lng: p1.lng + uLng * bufferRadius - vLng * bufferRadius },
    { lat: p2.lat + uLat * bufferRadius + vLat * bufferRadius, lng: p2.lng + uLng * bufferRadius + vLng * bufferRadius },
    { lat: p2.lat - uLat * bufferRadius + vLat * bufferRadius, lng: p2.lng - uLng * bufferRadius + vLng * bufferRadius },
    { lat: p1.lat - uLat * bufferRadius - vLat * bufferRadius, lng: p1.lng - uLng * bufferRadius - vLng * bufferRadius }
  ];
};

/* ==========================================================================
   Custom Floating HTML Overlay for Territory Badges
   ========================================================================== */

function createTerritoryBadgeOverlay(latLng, text, color, sb, onClick) {
  if (typeof google === 'undefined' || !google.maps || !google.maps.OverlayView) {
    return null;
  }

  class TerritoryBadge extends google.maps.OverlayView {
    constructor() {
      super();
      this.latLng = latLng;
      this.text = text;
      this.color = color;
      this.sb = sb;
      this.div = null;
      this.visible = true;
    }

    onAdd() {
      const div = document.createElement('div');
      div.className = 'territory-floating-badge shadow-sm';
      div.dir = isRtl.value ? 'rtl' : 'ltr';
      div.style.position = 'absolute';
      div.style.cursor = 'pointer';
      div.style.userSelect = 'none';
      div.style.zIndex = '100';
      div.style.transform = 'translate(-50%, -50%)';
      div.style.whiteSpace = 'nowrap';
      div.style.fontFamily = "'Cairo', 'Segoe UI', sans-serif";
      div.style.fontSize = '12px';
      div.style.fontWeight = '700';
      div.style.padding = '4px 12px';
      div.style.borderRadius = '9999px';
      div.style.color = '#ffffff';
      div.style.backgroundColor = this.color;
      div.style.border = '2px solid #ffffff';
      div.style.boxShadow = `0 4px 14px ${this.color}88`;
      div.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease';

      div.innerHTML = `<span>${this.text}</span>`;

      div.addEventListener('click', (e) => {
        e.stopPropagation();
        if (onClick) onClick(this.sb);
      });

      div.addEventListener('mouseenter', () => {
        div.style.transform = 'translate(-50%, -50%) scale(1.08)';
        div.style.boxShadow = `0 6px 18px ${this.color}`;
      });

      div.addEventListener('mouseleave', () => {
        div.style.transform = 'translate(-50%, -50%) scale(1)';
        div.style.boxShadow = `0 4px 14px ${this.color}88`;
      });

      this.div = div;
      const panes = this.getPanes();
      panes.overlayMouseTarget.appendChild(div);
    }

    draw() {
      const overlayProjection = this.getProjection();
      if (!overlayProjection || !this.div) return;

      const point = overlayProjection.fromLatLngToDivPixel(this.latLng);
      if (point) {
        this.div.style.left = point.x + 'px';
        this.div.style.top = point.y + 'px';
      }
    }

    onRemove() {
      if (this.div && this.div.parentNode) {
        this.div.parentNode.removeChild(this.div);
        this.div = null;
      }
    }

    setVisibility(visible) {
      this.visible = visible;
      if (this.div) {
        this.div.style.display = visible ? 'block' : 'none';
      }
    }
  }

  return new TerritoryBadge();
}

/* ==========================================================================
   Google Maps Initialization & Rendering
   ========================================================================== */

const createCustomMarkerIcon = (color) => {
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 42" width="32" height="42">
      <defs>
        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.35"/>
        </filter>
      </defs>
      <path d="M16 0C7.163 0 0 7.163 0 16c0 10.5 16 26 16 26s16-15.5 16-26c0-8.837-7.163-16-16-16z" fill="${color}" filter="url(#shadow)"/>
      <circle cx="16" cy="15" r="6.5" fill="#ffffff"/>
      <circle cx="16" cy="15" r="3.5" fill="${color}"/>
    </svg>
  `;
  return {
    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
    scaledSize: new google.maps.Size(30, 39),
    anchor: new google.maps.Point(15, 39),
    labelOrigin: new google.maps.Point(15, 15)
  };
};

const initMap = async () => {
  if (typeof google === 'undefined' || !google.maps) {
    setTimeout(initMap, 200);
    return;
  }

  // If modern async loader is active, ensure maps and marker libraries are resolved
  if (google.maps.importLibrary) {
    try {
      await Promise.all([
        google.maps.importLibrary("maps"),
        google.maps.importLibrary("marker")
      ]);
    } catch (err) {
      console.warn("Google Maps importLibrary notice:", err);
    }
  }

  if (!google.maps.Map || !google.maps.ControlPosition) {
    setTimeout(initMap, 200);
    return;
  }

  const mapElement = document.getElementById('service-body-google-map');
  if (!mapElement) return;

  const mapStyles = [
    { featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] },
    { featureType: "transit", elementType: "labels", stylers: [{ visibility: "off" }] },
    { featureType: "road", elementType: "geometry", stylers: [{ lightness: 20 }] }
  ];

  const zoomPos = google.maps.ControlPosition ? 
    (isRtl.value ? google.maps.ControlPosition.LEFT_BOTTOM : google.maps.ControlPosition.RIGHT_BOTTOM) : 
    undefined;

  map = new google.maps.Map(mapElement, {
    center: { lat: 27.5, lng: 31.0 },
    zoom: 6.5,
    mapId: 'NA_EGYPT_SERVICE_BODIES_MAP',
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: false,
    zoomControl: true,
    zoomControlOptions: zoomPos ? { position: zoomPos } : {}
  });

  infoWindow = new google.maps.InfoWindow();

  renderAllLayers();
  resetMapView();
  loading.value = false;
};

const createCustomMarker = (lat, lng, title, color, onClick) => {
  const position = { lat: parseFloat(lat), lng: parseFloat(lng) };

  // 1. Use modern AdvancedMarkerElement if available
  if (typeof google !== 'undefined' && google.maps && google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
    const pinEl = document.createElement('div');
    pinEl.className = 'custom-map-pin';
    pinEl.style.cursor = 'pointer';
    pinEl.style.display = 'flex';
    pinEl.style.alignItems = 'center';
    pinEl.style.justifyContent = 'center';
    pinEl.style.transition = 'transform 0.15s ease';

    const safeColorId = color.replace(/[^a-zA-Z0-9]/g, '');
    pinEl.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 42" width="30" height="40">
        <defs>
          <filter id="adv-shadow-${safeColorId}" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.35"/>
          </filter>
        </defs>
        <path d="M16 0C7.163 0 0 7.163 0 16c0 10.5 16 26 16 26s16-15.5 16-26c0-8.837-7.163-16-16-16z" fill="${color}" filter="url(#adv-shadow-${safeColorId})"/>
        <circle cx="16" cy="15" r="6.5" fill="#ffffff"/>
        <circle cx="16" cy="15" r="3.5" fill="${color}"/>
      </svg>
    `;

    pinEl.addEventListener('mouseenter', () => {
      pinEl.style.transform = 'scale(1.15)';
    });
    pinEl.addEventListener('mouseleave', () => {
      pinEl.style.transform = 'scale(1)';
    });

    const marker = new google.maps.marker.AdvancedMarkerElement({
      position: position,
      title: title,
      content: pinEl
    });

    if (onClick) {
      marker.addListener('gmp-click', () => onClick(marker));
    }

    return marker;
  }

  // 2. Legacy Marker fallback
  const legacyMarker = new google.maps.Marker({
    position: position,
    title: title,
    icon: createCustomMarkerIcon(color)
  });

  if (onClick) {
    legacyMarker.addListener('click', () => onClick(legacyMarker));
  }

  return legacyMarker;
};

const renderAllLayers = () => {
  if (!map) return;

  // Clear previous overlays
  mapOverlays.polygons.forEach(p => p.setMap(null));
  mapOverlays.polygons.clear();

  mapOverlays.circles.forEach(c => c.setMap(null));
  mapOverlays.circles.clear();

  mapOverlays.labels.forEach(l => l.setMap(null));
  mapOverlays.labels.clear();

  mapOverlays.markers.forEach(m => {
    if (m.setMap) m.setMap(null);
    else m.map = null;
  });
  mapOverlays.markers.clear();

  serviceBodies.value.forEach(sb => {
    const validGroupPoints = (sb.groups || [])
      .filter(g => g.lat && g.lng && !isNaN(g.lat) && !isNaN(g.lng))
      .map(g => ({ lat: parseFloat(g.lat), lng: parseFloat(g.lng), group: g }));

    if (validGroupPoints.length === 0) return;

    let centerLatLng = null;
    if (sb.center && sb.center.lat && sb.center.lng) {
      centerLatLng = new google.maps.LatLng(parseFloat(sb.center.lat), parseFloat(sb.center.lng));
    } else {
      const avgLat = validGroupPoints.reduce((sum, p) => sum + p.lat, 0) / validGroupPoints.length;
      const avgLng = validGroupPoints.reduce((sum, p) => sum + p.lng, 0) / validGroupPoints.length;
      centerLatLng = new google.maps.LatLng(avgLat, avgLng);
    }

    /* -------------------------------------------------------------
       1. Generate Territory Boundary Polygon / Circle
       ------------------------------------------------------------- */
    if (validGroupPoints.length >= 3) {
      const rawHull = computeConvexHull(validGroupPoints);
      const bufferedHull = bufferConvexHull(rawHull, 0.025);

      const polygon = new google.maps.Polygon({
        paths: bufferedHull,
        strokeColor: sb.stroke_color || sb.color,
        strokeOpacity: 0.9,
        strokeWeight: 2.2,
        fillColor: sb.fill_color || sb.color,
        fillOpacity: 0.18,
        clickable: true,
        map: showPolygons.value && isSelected(sb.id) ? map : null
      });

      polygon.addListener('click', (e) => {
        activeServiceBodyDetail.value = sb;
        infoWindow.setContent(`
          <div style="font-family: Cairo, 'Segoe UI', sans-serif; min-width: 220px; padding: 6px; text-align: ${isRtl.value ? 'right' : 'left'};" dir="${isRtl.value ? 'rtl' : 'ltr'}">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
              <span style="width: 12px; height: 12px; border-radius: 50%; background-color: ${sb.color}; display: inline-block; flex-shrink: 0;"></span>
              <strong style="font-size: 15px; color: #1e293b;">${sb.name}</strong>
            </div>
            <div style="font-size: 13px; color: #64748b; line-height: 1.6;">
              <div><strong>${isRtl.value ? 'عدد المجموعات:' : 'Groups:'}</strong> ${sb.groups_count}</div>
              <div><strong>${isRtl.value ? 'عدد الاجتماعات:' : 'Meetings:'}</strong> ${sb.meetings_count}</div>
              ${sb.location ? `<div><strong>${isRtl.value ? 'المقر / النطاق:' : 'Location:'}</strong> ${sb.location}</div>` : ''}
            </div>
          </div>
        `);
        infoWindow.setPosition(e.latLng);
        infoWindow.open(map);
      });

      polygon.addListener('mouseover', () => {
        polygon.setOptions({ fillOpacity: 0.32, strokeWeight: 3.2 });
        highlightServiceBody(sb.id);
      });
      polygon.addListener('mouseout', () => {
        polygon.setOptions({ fillOpacity: 0.18, strokeWeight: 2.2 });
        highlightServiceBody(null);
      });

      mapOverlays.polygons.set(sb.id, polygon);

    } else if (validGroupPoints.length === 2) {
      const capsulePaths = createCapsulePolygon(validGroupPoints[0], validGroupPoints[1], 0.035);
      const polygon = new google.maps.Polygon({
        paths: capsulePaths,
        strokeColor: sb.stroke_color || sb.color,
        strokeOpacity: 0.9,
        strokeWeight: 2.2,
        fillColor: sb.fill_color || sb.color,
        fillOpacity: 0.18,
        clickable: true,
        map: showPolygons.value && isSelected(sb.id) ? map : null
      });

      polygon.addListener('click', () => {
        activeServiceBodyDetail.value = sb;
      });
      mapOverlays.polygons.set(sb.id, polygon);

    } else if (validGroupPoints.length === 1) {
      const circle = new google.maps.Circle({
        strokeColor: sb.stroke_color || sb.color,
        strokeOpacity: 0.85,
        strokeWeight: 2,
        fillColor: sb.fill_color || sb.color,
        fillOpacity: 0.18,
        map: showPolygons.value && isSelected(sb.id) ? map : null,
        center: { lat: validGroupPoints[0].lat, lng: validGroupPoints[0].lng },
        radius: 12000
      });
      mapOverlays.circles.set(sb.id, circle);
    }

    /* -------------------------------------------------------------
       2. Generate Floating Territory Label Badge on Map
       ------------------------------------------------------------- */
    if (centerLatLng) {
      const badgeOverlay = createTerritoryBadgeOverlay(
        centerLatLng,
        sb.name,
        sb.color,
        sb,
        (selectedSb) => {
          zoomToServiceBody(selectedSb);
        }
      );

      if (badgeOverlay) {
        badgeOverlay.setMap(showTerritoryLabels.value && isSelected(sb.id) ? map : null);
        mapOverlays.labels.set(sb.id, badgeOverlay);
      }
    }

    /* -------------------------------------------------------------
       3. Render Individual Group Markers (AdvancedMarkerElement)
       ------------------------------------------------------------- */
    validGroupPoints.forEach(p => {
      const g = p.group;
      const marker = createCustomMarker(
        p.lat,
        p.lng,
        g.name,
        sb.color,
        (activeMarker) => {
          openGroupInfoWindow(g, sb, activeMarker);
        }
      );

      const isVisible = showMarkers.value && isSelected(sb.id);
      if (marker.setMap) {
        marker.setMap(isVisible ? map : null);
      } else {
        marker.map = isVisible ? map : null;
      }

      mapOverlays.markers.set(g.id, marker);
    });
  });
};

const openGroupInfoWindow = (group, sb, marker) => {
  activeServiceBodyDetail.value = sb;

  const meetingsListHtml = (group.meetings || []).map(m => `
    <div style="background: #f8fafc; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; border-inline-start: 4px solid ${sb.color}; font-size: 12px; text-align: ${isRtl.value ? 'right' : 'left'};">
      <div style="font-weight: 700; color: #334155;">
        ${m.day} • ${m.start_time} - ${m.end_time}
      </div>
      ${m.topic ? `<div style="color: #64748b; font-size: 11px; margin-top: 2px;">${m.topic}</div>` : ''}
    </div>
  `).join('');

  const directionsUrl = group.location_url || `https://www.google.com/maps/search/?api=1&query=${group.lat},${group.lng}`;

  const infoContent = `
    <div style="font-family: Cairo, 'Segoe UI', sans-serif; max-width: 290px; padding: 4px; text-align: ${isRtl.value ? 'right' : 'left'};" dir="${isRtl.value ? 'rtl' : 'ltr'}">
      <!-- Service Body Badge -->
      <div style="display: inline-block; background-color: ${sb.color}18; color: ${sb.color}; font-weight: 700; font-size: 11px; padding: 3px 10px; border-radius: 12px; margin-bottom: 6px;">
        ${sb.name}
      </div>

      <!-- Group Name -->
      <h6 style="font-weight: 700; font-size: 16px; margin: 0 0 6px 0; color: #0f172a;">${group.name}</h6>

      <!-- Location / Neighborhood / City -->
      <div style="font-size: 13px; color: #475569; margin-bottom: 10px; line-height: 1.5;">
        ${group.neighborhood ? `<div><i class="bi bi-geo-alt" style="color: #94a3b8; margin-inline-end: 4px;"></i> <strong>${isRtl.value ? 'المنطقة:' : 'Area:'}</strong> ${group.neighborhood}</div>` : ''}
        ${group.city ? `<div><i class="bi bi-building" style="color: #94a3b8; margin-inline-end: 4px;"></i> <strong>${isRtl.value ? 'المحافظة:' : 'City:'}</strong> ${group.city}</div>` : ''}
        ${group.address ? `<div style="color: #64748b; font-size: 12px; margin-top: 4px;"><i class="bi bi-pin-map" style="color: #94a3b8; margin-inline-end: 4px;"></i> ${group.address}</div>` : ''}
      </div>

      <!-- Meetings Header -->
      <div style="font-weight: 700; font-size: 12px; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
        <i class="bi bi-clock-history text-primary"></i> 
        <span>${isRtl.value ? 'مواعيد الاجتماعات' : 'Meeting Schedule'} (${(group.meetings || []).length})</span>
      </div>

      <!-- Meetings Container -->
      <div style="max-height: 140px; overflow-y: auto; margin-bottom: 10px;">
        ${meetingsListHtml || `<div style="font-size: 12px; color: #94a3b8;">${isRtl.value ? 'لا توجد مواعيد مسجلة' : 'No schedules available'}</div>`}
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; gap: 6px; margin-top: 8px;">
        <a href="${directionsUrl}" target="_blank" rel="noopener noreferrer" 
           style="flex: 1; text-align: center; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 7px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
          <i class="bi bi-cursor-fill"></i> 
          <span>${isRtl.value ? 'الاتجاهات والخريطة' : 'Get Directions'}</span>
        </a>
      </div>
    </div>
  `;

  infoWindow.setContent(infoContent);
  if (marker) {
    infoWindow.open({
      map: map,
      anchor: marker
    });
  } else {
    infoWindow.open(map);
  }
};

const updateLayerVisibility = () => {
  if (!map) return;

  serviceBodies.value.forEach(sb => {
    const isSbActive = selectedSbIds.value.has(sb.id);

    const poly = mapOverlays.polygons.get(sb.id);
    if (poly) {
      poly.setMap(isSbActive && showPolygons.value ? map : null);
    }

    const circle = mapOverlays.circles.get(sb.id);
    if (circle) {
      circle.setMap(isSbActive && showPolygons.value ? map : null);
    }

    const label = mapOverlays.labels.get(sb.id);
    if (label) {
      label.setMap(isSbActive && showTerritoryLabels.value ? map : null);
    }

    (sb.groups || []).forEach(g => {
      const marker = mapOverlays.markers.get(g.id);
      if (marker) {
        const isVisible = isSbActive && showMarkers.value;
        if (marker.setMap) {
          marker.setMap(isVisible ? map : null);
        } else {
          marker.map = isVisible ? map : null;
        }
      }
    });
  });
};

const zoomToServiceBody = (sb) => {
  if (!map) return;

  if (!selectedSbIds.value.has(sb.id)) {
    selectedSbIds.value.add(sb.id);
    selectedSbIds.value = new Set(selectedSbIds.value);
    updateLayerVisibility();
  }

  activeServiceBodyDetail.value = sb;
  expandedSbId.value = expandedSbId.value === sb.id ? null : sb.id;

  const validGroups = (sb.groups || []).filter(g => g.lat && g.lng);
  if (validGroups.length === 0) return;

  if (validGroups.length === 1) {
    map.setCenter({ lat: parseFloat(validGroups[0].lat), lng: parseFloat(validGroups[0].lng) });
    map.setZoom(12);
    return;
  }

  const bounds = new google.maps.LatLngBounds();
  validGroups.forEach(g => {
    bounds.extend(new google.maps.LatLng(parseFloat(g.lat), parseFloat(g.lng)));
  });

  map.fitBounds(bounds);

  const listener = google.maps.event.addListener(map, "idle", () => {
    if (map.getZoom() > 14) map.setZoom(14);
    google.maps.event.removeListener(listener);
  });
};

const focusGroupMarker = (group) => {
  if (!map || !group.lat || !group.lng) return;

  const marker = mapOverlays.markers.get(group.id);
  const sb = serviceBodies.value.find(s => s.id === group.service_body_id);

  map.setCenter({ lat: parseFloat(group.lat), lng: parseFloat(group.lng) });
  map.setZoom(14);

  if (marker && sb) {
    openGroupInfoWindow(group, sb, marker);
  }
};

const resetMapView = () => {
  if (!map) return;

  const allBounds = new google.maps.LatLngBounds();
  let hasAnyPoints = false;

  serviceBodies.value.forEach(sb => {
    if (selectedSbIds.value.has(sb.id)) {
      (sb.groups || []).forEach(g => {
        if (g.lat && g.lng) {
          allBounds.extend(new google.maps.LatLng(parseFloat(g.lat), parseFloat(g.lng)));
          hasAnyPoints = true;
        }
      });
    }
  });

  if (hasAnyPoints) {
    map.fitBounds(allBounds);
  } else {
    map.setCenter({ lat: 27.5, lng: 31.0 });
    map.setZoom(6.5);
  }
};

const toggleMapType = () => {
  if (!map) return;
  mapType.value = mapType.value === 'roadmap' ? 'hybrid' : 'roadmap';
  map.setMapTypeId(mapType.value);
};

const refreshLiveMapData = async () => {
  refreshing.value = true;
  try {
    const response = await fetch('/api/service-bodies/map-data', {
      headers: { 'Accept': 'application/json' }
    });
    if (response.ok) {
      const data = await response.json();
      if (data && data.service_bodies) {
        serviceBodies.value = data.service_bodies;
        // Keep selected set in sync
        selectedSbIds.value = new Set(serviceBodies.value.map(sb => sb.id));
        renderAllLayers();
      }
    }
  } catch (err) {
    console.error('Failed to refresh map data:', err);
  } finally {
    refreshing.value = false;
  }
};

onMounted(() => {
  initMap();
});

onUnmounted(() => {
  if (infoWindow) infoWindow.close();
});
</script>

<style scoped>
.service-body-map-container {
  font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  width: 100%;
}

.map-header-bar {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.header-icon-box {
  width: 48px;
  height: 48px;
  background: rgba(37, 99, 235, 0.1);
}

.metric-pill {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 9999px;
  padding: 6px 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.metric-val {
  font-weight: 800;
  font-size: 15px;
}

.metric-lbl {
  font-size: 13px;
  color: #64748b;
}

.map-layout-wrapper {
  display: flex;
  gap: 16px;
  height: calc(100vh - 230px);
  min-height: 580px;
  position: relative;
}

.map-sidebar {
  width: 350px;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  z-index: 10;
  transition: transform 0.3s ease;
}

.map-canvas-container {
  flex-grow: 1;
  background: #e2e8f0;
  min-height: 100%;
}

/* Precise RTL & LTR Search Input styling */
.search-box {
  position: relative;
  display: flex;
  align-items: center;
}

.search-box .search-icon {
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  pointer-events: none;
  z-index: 5;
}

.search-box .clear-btn {
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
}

[dir="rtl"] .search-box .search-icon {
  right: 14px;
  left: auto;
}

[dir="rtl"] .search-box .clear-btn {
  left: 12px;
  right: auto;
}

[dir="rtl"] .search-input {
  padding-right: 38px !important;
  padding-left: 32px !important;
  text-align: right;
}

[dir="ltr"] .search-box .search-icon {
  left: 14px;
  right: auto;
}

[dir="ltr"] .search-box .clear-btn {
  right: 12px;
  left: auto;
}

[dir="ltr"] .search-input {
  padding-left: 38px !important;
  padding-right: 32px !important;
  text-align: left;
}

/* Layer Display Toggles (Zero-overlap in RTL & LTR) */
.layer-toggles-bar {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
}

.layer-toggle-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  margin: 0;
  padding: 4px 6px;
  border-radius: 6px;
  transition: background 0.15s ease;
  user-select: none;
}

.layer-toggle-item:hover {
  background: rgba(0, 0, 0, 0.04);
}

.layer-toggle-switch {
  appearance: none;
  -webkit-appearance: none;
  width: 30px;
  height: 17px;
  background-color: #cbd5e1;
  border-radius: 9999px;
  position: relative;
  cursor: pointer;
  outline: none;
  margin: 0 !important;
  float: none !important;
  flex-shrink: 0;
  transition: background-color 0.2s ease;
}

.layer-toggle-switch::before {
  content: "";
  position: absolute;
  top: 2px;
  left: 2px;
  width: 13px;
  height: 13px;
  background-color: #ffffff;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
  transition: transform 0.2s ease;
}

[dir="rtl"] .layer-toggle-switch::before {
  left: auto;
  right: 2px;
}

.layer-toggle-switch:checked {
  background-color: #2563eb;
}

[dir="ltr"] .layer-toggle-switch:checked::before {
  transform: translateX(13px);
}

[dir="rtl"] .layer-toggle-switch:checked::before {
  transform: translateX(-13px);
}

.layer-toggle-label {
  font-size: 12px;
  font-weight: 700;
  color: #334155;
  white-space: nowrap;
  margin: 0 !important;
  padding: 0 !important;
}

.sb-checkbox {
  float: none !important;
  margin: 0 !important;
}

.service-bodies-list {
  overflow-y: auto;
  flex-grow: 1;
  padding-right: 4px;
}

[dir="rtl"] .service-bodies-list {
  padding-right: 0;
  padding-left: 4px;
}

.service-body-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.service-body-card:hover {
  background: #f1f5f9;
}

.service-body-card.active-sb {
  border-color: #cbd5e1;
  background: #ffffff;
}

.service-body-card.dimmed-sb {
  opacity: 0.55;
}

.service-body-card.highlighted-sb {
  border-color: #2563eb;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
}

.color-dot {
  width: 14px;
  height: 14px;
}

.sb-name {
  font-size: 13.5px;
  color: #1e293b;
}

[dir="rtl"] .sb-name,
[dir="rtl"] .sb-sub {
  text-align: right;
}

.btn-icon-zoom {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-icon-zoom:hover {
  background: #e2e8f0;
  color: #2563eb !important;
}

.tool-btn {
  width: 40px;
  height: 40px;
  background: #ffffff;
  color: #1e293b;
  border: none;
  font-size: 16px;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.tool-btn:hover {
  background: #f8fafc;
  transform: scale(1.05);
  color: #2563eb;
}

.active-sb-floating-card {
  z-index: 20;
  max-width: 90%;
  width: 450px;
}

.active-sb-color-box {
  font-size: 12px;
  min-width: 80px;
}

.btn-xs {
  font-size: 11px;
}

.extra-small {
  font-size: 11px;
}

.cursor-pointer {
  cursor: pointer;
}

.spin-animation {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.map-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.85);
  z-index: 50;
  backdrop-filter: blur(4px);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, 15px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}

.animate-fade-in {
  animation: fadeIn 0.25s ease forwards;
}

/* Mobile responsive drawer */
@media (max-width: 991.98px) {
  .map-sidebar {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 300px;
    z-index: 1000;
    transform: translateX(-105%);
    background: #ffffff;
  }

  [dir="rtl"] .map-sidebar {
    left: auto;
    right: 0;
    transform: translateX(105%);
  }

  .map-sidebar.sidebar-open {
    transform: translateX(0);
  }
}
</style>
