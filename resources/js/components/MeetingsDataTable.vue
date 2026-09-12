<template>
  <div class="meetings-datatable-container" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Top Summary KPI Metric Cards -->
    <div class="row g-3 mb-4">
      <!-- Total Meetings -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper bg-primary-subtle text-primary rounded-3 p-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-calendar-event-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.totalMeetings }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.total_meetings }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- In-Person Meetings -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.12); color: #0d6efd;">
              <i class="bi bi-geo-alt-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.inPersonMeetings }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.in_person_meetings }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Online Meetings -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(13, 202, 240, 0.12); color: #0dcaf0;">
              <i class="bi bi-camera-video-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.onlineMeetings }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.online_meetings }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Meetings -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper bg-success-subtle text-success rounded-3 p-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-check-circle-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.activeMeetings }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.active_meetings }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controls & Quick-Filter Bar -->
    <div class="card border-0 shadow-sm mb-4 glass-card">
      <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
          <!-- Live Debounced Search -->
          <div class="search-box-wrapper">
            <div class="search-box">
              <span class="search-icon"><i class="bi bi-search"></i></span>
              <input
                type="text"
                v-model="searchQuery"
                class="search-input"
                :placeholder="labels.searchPlaceholder"
                @input="onSearchInput"
              />
              <button
                v-if="searchQuery"
                class="search-clear-btn"
                type="button"
                @click="clearSearch"
                :title="labels.clearFilters"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>

          <!-- Day Filter -->
          <div class="filter-wrapper">
            <select v-model="selectedDay" @change="fetchData" class="form-select form-select-sm filter-select">
              <option value="">{{ labels.allDays }}</option>
              <option v-for="d in days" :key="d.id" :value="d.id">
                {{ isRtl ? (d.ar_name || d.en_name) : (d.en_name || d.ar_name) }}
              </option>
            </select>
          </div>

          <!-- Type Filter -->
          <div class="filter-wrapper">
            <select v-model="selectedType" @change="fetchData" class="form-select form-select-sm filter-select">
              <option value="">{{ labels.allTypes }}</option>
              <option value="in_person">{{ labels.inPersonGroup }}</option>
              <option value="online">{{ labels.directOnlineGroup }}</option>
            </select>
          </div>

          <!-- Status Filter -->
          <div class="filter-wrapper">
            <select v-model="selectedStatus" @change="fetchData" class="form-select form-select-sm filter-select">
              <option value="">{{ labels.allStatuses }}</option>
              <option value="available">{{ labels.available }}</option>
              <option value="suspended">{{ labels.suspended }}</option>
            </select>
          </div>

          <!-- Reset Filter Button -->
          <button
            v-if="hasActiveFilters"
            type="button"
            class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1"
            @click="resetAllFilters"
          >
            <i class="bi bi-arrow-counterclockwise"></i>
            {{ labels.clearFilters }}
          </button>
        </div>

        <!-- Create Button -->
        <div class="d-flex align-items-center gap-2">
          <a v-if="createRoute" :href="createRoute" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm">
            <i class="bi bi-plus-lg"></i>
            {{ labels.createLabel }}
          </a>
        </div>
      </div>
    </div>

    <!-- Main Table / Mobile Card View -->
    <div class="card border-0 shadow-lg glass-card main-table-card">
      <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-hover align-middle mb-0 custom-datatable">
            <thead>
              <tr class="table-header-row">
                <th scope="col" class="ps-4 py-3" style="min-width: 240px;" @click="setSort('group.en_name')">
                  <div class="d-flex align-items-center gap-1 cursor-pointer">
                    <span>{{ labels.groupName }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('group.en_name')"></i>
                  </div>
                </th>
                <th scope="col" class="py-3" style="min-width: 200px;" @click="setSort('topic.en_name')">
                  <div class="d-flex align-items-center gap-1 cursor-pointer">
                    <span>{{ labels.meetingTopic }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('topic.en_name')"></i>
                  </div>
                </th>
                <th scope="col" class="py-3" style="min-width: 170px;" @click="setSort('day_id')">
                  <div class="d-flex align-items-center gap-1 cursor-pointer">
                    <span>{{ labels.dayAndTime }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('day_id')"></i>
                  </div>
                </th>
                <th scope="col" class="py-3 text-center" style="min-width: 110px;" @click="setSort('status')">
                  <div class="d-flex align-items-center justify-content-center gap-1 cursor-pointer">
                    <span>{{ labels.status }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('status')"></i>
                  </div>
                </th>
                <th scope="col" class="pe-4 py-3 text-end" style="min-width: 140px;">
                  {{ labels.control }}
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading Skeleton -->
              <tr v-if="loading" v-for="n in 5" :key="'skeleton-' + n">
                <td colspan="5" class="py-3 px-4">
                  <div class="placeholder-glow d-flex align-items-center justify-content-between gap-3">
                    <span class="placeholder col-4 rounded py-2"></span>
                    <span class="placeholder col-3 rounded py-2"></span>
                    <span class="placeholder col-2 rounded py-2"></span>
                    <span class="placeholder col-1 rounded py-2"></span>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="rows.length === 0">
                <td colspan="5" class="text-center py-5">
                  <div class="empty-state-wrapper py-4">
                    <i class="bi bi-calendar-x fs-1 text-muted opacity-50 mb-2"></i>
                    <h6 class="fw-semibold text-muted">{{ labels.noData }}</h6>
                    <p class="small text-muted mb-3">{{ labels.adjustFilters }}</p>
                    <button v-if="hasActiveFilters" type="button" class="btn btn-outline-primary btn-sm" @click="resetAllFilters">
                      {{ labels.clearFilters }}
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Rows -->
              <tr v-else v-for="item in rows" :key="item.id" class="table-data-row">
                <!-- Group Info -->
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <div
                      class="badge-avatar rounded-3 p-2 d-flex align-items-center justify-content-center"
                      :class="item.group_type === 'online' ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary'"
                      style="width: 40px; height: 40px;"
                    >
                      <i :class="item.group_type === 'online' ? 'bi bi-camera-video-fill fs-5' : 'bi bi-building fs-5'"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="fw-bold fs-6 text-truncate" style="color: var(--text-primary);">
                        {{ item.group_name }}
                      </div>
                      <div class="d-flex align-items-center gap-2 mt-1">
                        <span
                          class="badge rounded-pill px-2 py-0-5 small"
                          :class="item.group_type === 'online' ? 'badge-type-online' : 'badge-type-inperson'"
                        >
                          <i :class="item.group_type === 'online' ? 'bi bi-broadcast me-1' : 'bi bi-geo-alt me-1'"></i>
                          {{ item.group_type === 'online' ? labels.directOnlineGroup : labels.inPersonGroup }}
                        </span>
                        <span v-if="item.group_subname" class="text-muted small text-truncate">
                          {{ item.group_subname }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Topic & Options -->
                <td class="py-3">
                  <div class="fw-semibold" style="color: var(--text-primary);">
                    {{ item.topic_name }}
                  </div>
                  <div v-if="item.options_labels && item.options_labels.length" class="d-flex flex-wrap gap-1 mt-1">
                    <span
                      v-for="(opt, oIdx) in item.options_labels.slice(0, 3)"
                      :key="oIdx"
                      class="badge bg-secondary-subtle text-secondary small py-0 px-2 rounded"
                    >
                      {{ opt }}
                    </span>
                    <span v-if="item.options_labels.length > 3" class="text-muted small">
                      +{{ item.options_labels.length - 3 }}
                    </span>
                  </div>
                </td>

                <!-- Day & Time -->
                <td class="py-3">
                  <div class="d-flex flex-column gap-1">
                    <span class="badge bg-light text-dark border align-self-start py-1 px-2 fw-medium">
                      <i class="bi bi-calendar3 me-1 text-primary"></i>
                      {{ item.day_name }}
                    </span>
                    <div class="small text-muted d-flex align-items-center gap-1 mt-1">
                      <i class="bi bi-clock"></i>
                      <span>{{ item.from_time }} - {{ item.to_time }}</span>
                      <span v-if="item.duration_label" class="badge rounded-pill bg-info-subtle text-info py-0 px-1 ms-1">
                        {{ item.duration_label }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Status -->
                <td class="py-3 text-center">
                  <span
                    class="badge rounded-pill px-3 py-1 fw-semibold"
                    :class="item.status === 'available' ? 'badge-status-active' : 'badge-status-inactive'"
                  >
                    <i :class="item.status === 'available' ? 'bi bi-check-circle-fill me-1' : 'bi bi-pause-circle-fill me-1'"></i>
                    {{ item.status_label }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="pe-4 py-3 text-end">
                  <div class="d-inline-flex align-items-center gap-1">
                    <!-- Quick Preview Button -->
                    <button
                      type="button"
                      class="btn btn-sm btn-icon-action btn-outline-secondary"
                      @click="openDetails(item)"
                      :title="labels.quickView"
                    >
                      <i class="bi bi-eye"></i>
                    </button>

                    <!-- Edit Button -->
                    <a
                      v-if="editRouteTemplate"
                      :href="getEditUrl(item.id)"
                      class="btn btn-sm btn-icon-action btn-outline-info"
                      :title="labels.edit"
                    >
                      <i class="bi bi-pencil"></i>
                    </a>

                    <!-- Delete Button -->
                    <button
                      v-if="deleteRouteTemplate"
                      type="button"
                      class="btn btn-sm btn-icon-action btn-outline-danger"
                      @click="confirmDelete(item)"
                      :title="labels.delete"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View (< 768px) -->
        <div class="d-md-none p-3 d-flex flex-column gap-3">
          <!-- Loading Skeleton -->
          <div v-if="loading" v-for="n in 3" :key="'mob-skeleton-' + n" class="card border rounded-3 p-3 shadow-sm">
            <div class="placeholder-glow">
              <span class="placeholder col-7 rounded py-2 mb-2"></span>
              <span class="placeholder col-4 rounded py-1 mb-2"></span>
              <span class="placeholder col-10 rounded py-1"></span>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="rows.length === 0" class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted opacity-50 mb-2"></i>
            <h6 class="fw-semibold text-muted">{{ labels.noData }}</h6>
            <p class="small text-muted mb-3">{{ labels.adjustFilters }}</p>
            <button v-if="hasActiveFilters" type="button" class="btn btn-outline-primary btn-sm" @click="resetAllFilters">
              {{ labels.clearFilters }}
            </button>
          </div>

          <!-- Mobile Cards -->
          <div
            v-else
            v-for="item in rows"
            :key="'mob-' + item.id"
            class="card border rounded-3 p-3 shadow-sm mobile-item-card"
          >
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
              <div class="d-flex align-items-center gap-2 text-truncate">
                <div
                  class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                  :class="item.group_type === 'online' ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary'"
                  style="width: 36px; height: 36px;"
                >
                  <i :class="item.group_type === 'online' ? 'bi bi-camera-video fs-6' : 'bi bi-building fs-6'"></i>
                </div>
                <div class="text-truncate">
                  <div class="fw-bold text-truncate" style="color: var(--text-primary);">{{ item.group_name }}</div>
                  <span
                    class="badge rounded-pill px-2 py-0-5 small"
                    :class="item.group_type === 'online' ? 'badge-type-online' : 'badge-type-inperson'"
                  >
                    {{ item.group_type === 'online' ? labels.directOnlineGroup : labels.inPersonGroup }}
                  </span>
                </div>
              </div>
              <span
                class="badge rounded-pill px-2 py-1 small"
                :class="item.status === 'available' ? 'badge-status-active' : 'badge-status-inactive'"
              >
                {{ item.status_label }}
              </span>
            </div>

            <div class="mt-2 pt-2 border-top">
              <div class="small mb-1">
                <span class="text-muted">{{ labels.meetingTopic }}:</span>
                <span class="fw-semibold ms-1" style="color: var(--text-primary);">{{ item.topic_name }}</span>
              </div>
              <div class="small d-flex align-items-center gap-1 text-muted mb-2">
                <i class="bi bi-clock"></i>
                <span>{{ item.day_name }} &bull; {{ item.from_time }} - {{ item.to_time }}</span>
                <span v-if="item.duration_label" class="badge bg-light text-dark border py-0 px-1 ms-1">
                  {{ item.duration_label }}
                </span>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-2">
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-2"
                @click="openDetails(item)"
              >
                <i class="bi bi-eye"></i>
                {{ labels.quickView }}
              </button>
              <a
                v-if="editRouteTemplate"
                :href="getEditUrl(item.id)"
                class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 px-2"
              >
                <i class="bi bi-pencil"></i>
                {{ labels.edit }}
              </a>
              <button
                v-if="deleteRouteTemplate"
                type="button"
                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 px-2"
                @click="confirmDelete(item)"
              >
                <i class="bi bi-trash"></i>
                {{ labels.delete }}
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination Controls Footer -->
        <div class="card-footer bg-transparent border-top py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-2 small text-muted">
            <span>{{ paginationInfo }}</span>
            <div class="d-flex align-items-center gap-1 ms-2">
              <select v-model="pageSize" @change="onPageSizeChange" class="form-select form-select-sm" style="width: 75px;">
                <option :value="10">10</option>
                <option :value="15">15</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
              </select>
            </div>
          </div>

          <!-- Page Navigation -->
          <div class="d-flex align-items-center gap-1">
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage <= 1 || loading"
              @click="goToPage(1)"
              :title="labels.first"
            >
              <i :class="isRtl ? 'bi bi-chevron-double-right' : 'bi bi-chevron-double-left'"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage <= 1 || loading"
              @click="goToPage(currentPage - 1)"
              :title="labels.prev"
            >
              <i :class="isRtl ? 'bi bi-chevron-right' : 'bi bi-chevron-left'"></i>
            </button>

            <span class="px-2 small fw-semibold text-muted">
              {{ currentPage }} / {{ totalPages || 1 }}
            </span>

            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage >= totalPages || loading"
              @click="goToPage(currentPage + 1)"
              :title="labels.next"
            >
              <i :class="isRtl ? 'bi bi-chevron-left' : 'bi bi-chevron-right'"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage >= totalPages || loading"
              @click="goToPage(totalPages)"
              :title="labels.last"
            >
              <i :class="isRtl ? 'bi bi-chevron-double-left' : 'bi bi-chevron-double-right'"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- QUICK PREVIEW MODAL / SLIDE-OVER                                         -->
    <!-- ========================================================================= -->
    <div
      v-if="showModal"
      class="modal fade show d-block custom-modal-backdrop"
      tabindex="-1"
      role="dialog"
      aria-modal="true"
      @click.self="closeModal"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modal-glassmorphic">
          <div class="modal-header border-bottom py-3 px-4">
            <div class="d-flex align-items-center gap-2">
              <div
                class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                :class="selectedItem?.group_type === 'online' ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary'"
                style="width: 36px; height: 36px;"
              >
                <i :class="selectedItem?.group_type === 'online' ? 'bi bi-camera-video-fill fs-5' : 'bi bi-building fs-5'"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold mb-0" style="color: var(--text-primary);">
                  {{ labels.meetingDetails }}
                </h5>
                <div class="small text-muted">{{ selectedItem?.group_name }}</div>
              </div>
            </div>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <div v-if="selectedItem" class="row g-3">
              <!-- Basic Overview -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.groupName }}</div>
                  <div class="fw-bold fs-6 mb-2" style="color: var(--text-primary);">
                    {{ selectedItem.group_name }}
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span
                      class="badge rounded-pill px-2 py-1"
                      :class="selectedItem.group_type === 'online' ? 'badge-type-online' : 'badge-type-inperson'"
                    >
                      {{ selectedItem.group_type === 'online' ? labels.directOnlineGroup : labels.inPersonGroup }}
                    </span>
                    <span
                      class="badge rounded-pill px-2 py-1"
                      :class="selectedItem.status === 'available' ? 'badge-status-active' : 'badge-status-inactive'"
                    >
                      {{ selectedItem.status_label }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Topic & Day Schedule -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.meetingTopic }}</div>
                  <div class="fw-bold fs-6 mb-2 text-primary">
                    {{ selectedItem.topic_name }}
                  </div>
                  <div class="small text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ selectedItem.day_name }}</span>
                  </div>
                  <div class="small text-muted d-flex align-items-center gap-1 mt-1">
                    <i class="bi bi-clock"></i>
                    <span>{{ selectedItem.from_time }} - {{ selectedItem.to_time }}</span>
                    <span v-if="selectedItem.duration_label" class="badge rounded-pill bg-info-subtle text-info py-0 px-1 ms-1">
                      {{ selectedItem.duration_label }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Meeting Options / Formats -->
              <div v-if="selectedItem.options_labels && selectedItem.options_labels.length" class="col-12">
                <div class="p-3 rounded-3 detail-info-box">
                  <div class="small text-muted fw-semibold mb-2">{{ labels.meetingOptions }}</div>
                  <div class="d-flex flex-wrap gap-2">
                    <span
                      v-for="(opt, idx) in selectedItem.options_labels"
                      :key="idx"
                      class="badge bg-primary-subtle text-primary px-3 py-1-5 rounded-pill"
                    >
                      <i class="bi bi-tag-fill me-1 small"></i>
                      {{ opt }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Location / Zoom link -->
              <div class="col-12">
                <div class="p-3 rounded-3 detail-info-box">
                  <div class="small text-muted fw-semibold mb-2">
                    {{ selectedItem.group_type === 'online' ? labels.platformLink : labels.addressLocation }}
                  </div>
                  <!-- Online Link -->
                  <div v-if="selectedItem.zoom_link" class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-truncate text-muted font-monospace small" style="max-width: 80%;">
                      {{ selectedItem.zoom_link }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                        @click="copyToClipboard(selectedItem.zoom_link)"
                      >
                        <i class="bi bi-clipboard"></i>
                        {{ copyNotice || labels.copyLink }}
                      </button>
                      <a
                        :href="selectedItem.zoom_link"
                        target="_blank"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1"
                      >
                        <i class="bi bi-box-arrow-up-right"></i>
                        {{ labels.joinMeeting }}
                      </a>
                    </div>
                  </div>
                  <!-- Physical Address -->
                  <div v-else-if="selectedItem.location_address || selectedItem.map_location">
                    <div v-if="selectedItem.location_address" class="mb-2 text-dark">
                      <i class="bi bi-geo-alt me-1 text-danger"></i>
                      {{ selectedItem.location_address }}
                    </div>
                    <a
                      v-if="selectedItem.map_location"
                      :href="selectedItem.map_location"
                      target="_blank"
                      class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                    >
                      <i class="bi bi-map"></i>
                      {{ labels.viewOnMap }}
                    </a>
                  </div>
                  <div v-else class="text-muted small">
                    {{ labels.noLink }}
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="selectedItem.notes" class="col-12">
                <div class="p-3 rounded-3 detail-info-box">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.notes }}</div>
                  <div class="small text-muted">{{ selectedItem.notes }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
            <a
              v-if="editRouteTemplate && selectedItem"
              :href="getEditUrl(selectedItem.id)"
              class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold"
            >
              <i class="bi bi-pencil-square"></i>
              {{ labels.edit }}
            </a>
            <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="closeModal">
              {{ labels.close }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  fetchUrl: {
    type: String,
    required: true
  },
  createRoute: {
    type: String,
    default: ''
  },
  createLabel: {
    type: String,
    default: 'Add Meeting'
  },
  editRouteTemplate: {
    type: String,
    default: ''
  },
  deleteRouteTemplate: {
    type: String,
    default: ''
  },
  days: {
    type: Array,
    default: () => []
  },
  initialKpiStats: {
    type: Object,
    default: () => ({
      total_meetings: 0,
      in_person_meetings: 0,
      online_meetings: 0,
      active_meetings: 0
    })
  },
  labels: {
    type: Object,
    default: () => ({})
  },
  csrfToken: {
    type: String,
    default: ''
  }
});

const isRtl = computed(() => {
  return document.documentElement.getAttribute('dir') === 'rtl';
});

// State
const rows = ref([]);
const loading = ref(false);
const totalRows = ref(0);
const currentPage = ref(1);
const pageSize = ref(15);
const sortColumn = ref('id');
const sortDirection = ref('desc');

// Filters
const searchQuery = ref('');
const selectedDay = ref('');
const selectedType = ref('');
const selectedStatus = ref('');

// KPIs
const kpiData = reactive({
  total_meetings: props.initialKpiStats?.total_meetings || 0,
  in_person_meetings: props.initialKpiStats?.in_person_meetings || 0,
  online_meetings: props.initialKpiStats?.online_meetings || 0,
  active_meetings: props.initialKpiStats?.active_meetings || 0
});

// Modal state
const showModal = ref(false);
const selectedItem = ref(null);
const copyNotice = ref('');

let debounceTimer = null;
const onSearchInput = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    currentPage.value = 1;
    fetchData();
  }, 350);
};

const clearSearch = () => {
  searchQuery.value = '';
  currentPage.value = 1;
  fetchData();
};

const hasActiveFilters = computed(() => {
  return !!(searchQuery.value || selectedDay.value || selectedType.value || selectedStatus.value);
});

const resetAllFilters = () => {
  searchQuery.value = '';
  selectedDay.value = '';
  selectedType.value = '';
  selectedStatus.value = '';
  currentPage.value = 1;
  fetchData();
};

const totalPages = computed(() => {
  return Math.ceil(totalRows.value / pageSize.value) || 1;
});

const paginationInfo = computed(() => {
  if (totalRows.value === 0) return props.labels.noData || 'No entries';
  const start = (currentPage.value - 1) * pageSize.value + 1;
  const end = Math.min(currentPage.value * pageSize.value, totalRows.value);
  if (isRtl.value) {
    return `عرض من ${start} إلى ${end} من إجمالي ${totalRows.value} سجل`;
  }
  return `Showing ${start} to ${end} of ${totalRows.value} entries`;
});

// Fetch Data
const fetchData = async () => {
  loading.value = true;
  try {
    const response = await axios.get(props.fetchUrl, {
      params: {
        page: currentPage.value,
        pagesize: pageSize.value,
        sort_column: sortColumn.value,
        sort_direction: sortDirection.value,
        search: searchQuery.value,
        day_id: selectedDay.value || undefined,
        type: selectedType.value || undefined,
        status: selectedStatus.value || undefined
      },
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = response.data;
    if (data && Array.isArray(data.data)) {
      rows.value = data.data;
      totalRows.value = data.total || data.data.length;
    } else if (Array.isArray(data)) {
      rows.value = data;
      totalRows.value = data.length;
    }
  } catch (err) {
    console.error('Failed to fetch meetings data:', err);
  } finally {
    loading.value = false;
  }
};

const setSort = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
  currentPage.value = 1;
  fetchData();
};

const getSortIcon = (column) => {
  if (sortColumn.value !== column) return 'bi-arrow-down-up text-muted opacity-50';
  return sortDirection.value === 'asc' ? 'bi-sort-up text-primary' : 'bi-sort-down text-primary';
};

const goToPage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
  fetchData();
};

const onPageSizeChange = () => {
  currentPage.value = 1;
  fetchData();
};

const getEditUrl = (id) => {
  return props.editRouteTemplate ? props.editRouteTemplate.replace('{id}', id) : `/meeting/${id}/edit`;
};

const openDetails = (item) => {
  selectedItem.value = item;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedItem.value = null;
  copyNotice.value = '';
};

const copyToClipboard = async (text) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
    copyNotice.value = props.labels.linkCopied || 'Copied!';
    setTimeout(() => {
      copyNotice.value = '';
    }, 2500);
  } catch (e) {
    console.warn('Copy failed:', e);
  }
};

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: props.labels.confirmDelete || 'Are you sure?',
    text: `${item.group_name} - ${item.topic_name}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: props.labels.delete || 'Delete',
    cancelButtonText: props.labels.close || 'Cancel',
    reverseButtons: isRtl.value
  });

  if (result.isConfirmed) {
    const deleteUrl = props.deleteRouteTemplate
      ? props.deleteRouteTemplate.replace('{id}', item.id)
      : `/meeting/${item.id}`;

    try {
      await axios.delete(deleteUrl, {
        headers: {
          'X-CSRF-TOKEN': props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      Swal.fire({
        title: props.labels.deletedSuccess || 'Deleted!',
        icon: 'success',
        timer: 1800,
        showConfirmButton: false
      });

      // Update KPI
      if (kpiData.total_meetings > 0) kpiData.total_meetings--;
      if (item.group_type === 'online' && kpiData.online_meetings > 0) kpiData.online_meetings--;
      if (item.group_type === 'in_person' && kpiData.in_person_meetings > 0) kpiData.in_person_meetings--;
      if (item.status === 'available' && kpiData.active_meetings > 0) kpiData.active_meetings--;

      fetchData();
    } catch (err) {
      console.error('Delete failed:', err);
      // Fallback form post
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = deleteUrl;

      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'DELETE';
      form.appendChild(methodInput);

      const tokenInput = document.createElement('input');
      tokenInput.type = 'hidden';
      tokenInput.name = '_token';
      tokenInput.value = props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      form.appendChild(tokenInput);

      document.body.appendChild(form);
      form.submit();
    }
  }
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.meetings-datatable-container {
  width: 100%;
}

.kpi-card {
  border-radius: 14px;
  background: var(--glass-bg, #ffffff);
  border: 1px solid var(--glass-border, rgba(0, 0, 0, 0.08)) !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}

.glass-card {
  background: var(--glass-bg, #ffffff);
  border-radius: 16px;
  border: 1px solid var(--glass-border, rgba(0, 0, 0, 0.08));
}

.search-box {
  position: relative;
  display: flex;
  align-items: center;
}

.search-box-wrapper {
  min-width: 240px;
  flex: 1 1 240px;
}

.search-input {
  width: 100%;
  padding: 0.45rem 2rem 0.45rem 2.2rem;
  border-radius: 8px;
  border: 1px solid rgba(0, 0, 0, 0.12);
  background: rgba(255, 255, 255, 0.85);
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.2s;
}

.search-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  color: #6c757d;
  pointer-events: none;
}

[dir="rtl"] .search-icon {
  left: auto;
  right: 0.75rem;
}

[dir="rtl"] .search-input {
  padding: 0.45rem 2.2rem 0.45rem 2rem;
}

.search-clear-btn {
  position: absolute;
  right: 0.5rem;
  background: transparent;
  border: none;
  color: #adb5bd;
  cursor: pointer;
}

[dir="rtl"] .search-clear-btn {
  right: auto;
  left: 0.5rem;
}

.filter-select {
  border-radius: 8px;
  font-size: 0.85rem;
  border: 1px solid rgba(0, 0, 0, 0.12);
  background-color: rgba(255, 255, 255, 0.85);
}

.custom-datatable thead th {
  background: rgba(0, 0, 0, 0.02);
  font-size: 0.78rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
  color: #6c757d;
  border-bottom: 2px solid rgba(0, 0, 0, 0.05);
}

.table-data-row {
  transition: background-color 0.15s ease;
}

.table-data-row:hover {
  background-color: rgba(13, 110, 253, 0.03);
}

.cursor-pointer {
  cursor: pointer;
  user-select: none;
}

.sort-icon {
  font-size: 0.8rem;
}

.btn-icon-action {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.15s ease;
}

.btn-icon-action:hover {
  transform: scale(1.05);
}

.badge-type-online {
  background: rgba(13, 202, 240, 0.15);
  color: #0aa2c0;
  font-weight: 600;
}

.badge-type-inperson {
  background: rgba(13, 110, 253, 0.12);
  color: #0d6efd;
  font-weight: 600;
}

.badge-status-active {
  background: rgba(25, 135, 84, 0.15);
  color: #198754;
}

.badge-status-inactive {
  background: rgba(255, 193, 7, 0.2);
  color: #b58105;
}

.custom-modal-backdrop {
  background-color: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(4px);
}

.modal-glassmorphic {
  background: var(--glass-bg, #ffffff);
  backdrop-filter: blur(20px);
  border-radius: 18px;
  border: 1px solid var(--glass-border, rgba(0, 0, 0, 0.12));
}

.detail-info-box {
  background: rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.mobile-item-card {
  background: var(--glass-bg, #ffffff);
  border: 1px solid rgba(0, 0, 0, 0.08) !important;
}
</style>
