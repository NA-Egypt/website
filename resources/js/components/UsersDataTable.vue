<template>
  <div class="users-container" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Top Summary KPI Metric Cards -->
    <div class="row g-3 mb-4">
      <!-- Total Users -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.12); color: #0d6efd;">
              <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.totalUsers || 'Total Users' }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.total_users }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Recently (Last 30 Days) -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(25, 135, 84, 0.12); color: #198754;">
              <i class="bi bi-clock-history fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.activeRecently || 'Active Recently' }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.active_recently }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Never Logged In -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 193, 7, 0.15); color: #d39e00;">
              <i class="bi bi-hourglass-split fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.neverLoggedIn || 'Never Logged In' }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.never_logged_in }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Body Officers -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(13, 202, 240, 0.12); color: #0dcaf0;">
              <i class="bi bi-person-badge-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.serviceBodyOfficers || 'Officers' }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.service_body_officers }}</div>
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
                :placeholder="labels.searchPlaceholder || 'Search users by name, email...'"
                @input="onSearchInput"
              />
              <button
                v-if="searchQuery"
                class="search-clear-btn"
                type="button"
                @click="clearSearch"
                :title="labels.clearFilters || 'Clear Filters'"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>

          <!-- Role Filter -->
          <div class="filter-wrapper" v-if="roles.length > 0">
            <select v-model="selectedRole" @change="fetchData" class="form-select form-select-sm filter-select">
              <option value="">{{ labels.allRoles || 'All Roles' }}</option>
              <option v-for="r in roles" :key="typeof r === 'object' ? r.name : r" :value="typeof r === 'object' ? r.name : r">
                {{ typeof r === 'object' ? r.name : r }}
              </option>
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
            {{ labels.clearFilters || 'Clear Filters' }}
          </button>
        </div>

        <!-- Create Button -->
        <div class="d-flex align-items-center gap-2">
          <a v-if="createRoute" :href="createRoute" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm">
            <i class="bi bi-plus-lg"></i>
            {{ createLabel }}
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
                <th scope="col" class="ps-4 py-3" style="min-width: 250px;" @click="setSort('name')">
                  <div class="d-flex align-items-center gap-1 cursor-pointer">
                    <span>{{ labels.user || 'User' }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('name')"></i>
                  </div>
                </th>
                <th scope="col" class="py-3" style="min-width: 200px;">
                  {{ labels.assignedRoles || 'Roles' }}
                </th>
                <th scope="col" class="py-3" style="min-width: 180px;">
                  {{ labels.associatedServiceBody || 'Service Body' }}
                </th>
                <th scope="col" class="py-3 text-center" style="min-width: 170px;" @click="setSort('last_login_at')">
                  <div class="d-flex align-items-center justify-content-center gap-1 cursor-pointer">
                    <span>{{ labels.lastLogin || 'Last Login' }}</span>
                    <i class="bi sort-icon" :class="getSortIcon('last_login_at')"></i>
                  </div>
                </th>
                <th scope="col" class="pe-4 py-3 text-end" style="min-width: 160px;">
                  {{ labels.actions || 'Actions' }}
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="5" class="text-center py-5">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="rows.length === 0">
                <td colspan="5" class="text-center py-5 text-muted">
                  <div class="d-flex flex-column align-items-center gap-2">
                    <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                    <div class="fw-semibold">{{ labels.noRecords || 'No matching records found' }}</div>
                    <div class="small">{{ labels.tryAdjusting || 'Try adjusting your search or filters' }}</div>
                  </div>
                </td>
              </tr>

              <!-- Data Rows -->
              <tr v-else v-for="item in rows" :key="item.id" class="table-data-row">
                <!-- User Profile -->
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; background: rgba(13, 110, 253, 0.12); color: #0d6efd;">
                      {{ getUserInitials(item.name) }}
                    </div>
                    <div>
                      <div class="fw-bold" style="color: var(--text-primary);">
                        {{ item.name }}
                      </div>
                      <div class="text-muted small">
                        {{ item.email }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Roles Badges -->
                <td class="py-3">
                  <div class="d-flex flex-wrap gap-1">
                    <span
                      v-for="r in (item.roles || [])"
                      :key="r.id || r.name"
                      class="badge rounded-pill px-2 py-1 small fw-semibold"
                      :class="getRoleBadgeClass(r.name)"
                    >
                      {{ r.name }}
                    </span>
                    <span v-if="!item.roles || item.roles.length === 0" class="text-muted small fst-italic">
                      None
                    </span>
                  </div>
                </td>

                <!-- Service Body -->
                <td class="py-3">
                  <span v-if="item.service_body" class="badge rounded-pill bg-light text-dark border px-3 py-1-5 fw-semibold">
                    <i class="bi bi-building me-1 text-primary"></i>
                    {{ isRtl ? (item.service_body.ar_name || item.service_body.en_name) : (item.service_body.en_name || item.service_body.ar_name) }}
                  </span>
                  <span v-else class="text-muted small">N/A</span>
                </td>

                <!-- Last Login -->
                <td class="py-3 text-center">
                  <div v-if="item.last_login_at" class="d-inline-flex flex-column align-items-center">
                    <span class="badge rounded-pill login-badge px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1">
                      <i class="bi bi-clock-history text-primary"></i>
                      {{ item.last_login_at_formatted || item.last_login_at }}
                    </span>
                    <span v-if="item.last_login_human" class="small text-muted mt-1 login-relative-time">
                      {{ item.last_login_human }}
                    </span>
                  </div>
                  <span v-else class="badge rounded-pill never-badge px-3 py-1-5 fw-semibold">
                    <i class="bi bi-dash-circle me-1 opacity-50"></i>
                    {{ labels.never || 'Never' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="pe-4 py-3 text-end">
                  <div class="d-flex align-items-center justify-content-end gap-1">
                    <!-- Quick View -->
                    <button
                      type="button"
                      class="btn btn-sm btn-icon-action btn-outline-info"
                      @click="openPreview(item)"
                      :title="labels.quickView || 'Quick View'"
                    >
                      <i class="bi bi-eye"></i>
                    </button>

                    <!-- Impersonate User -->
                    <button
                      v-if="canImpersonate && impersonateRouteTemplate && !isItemSuperAdmin(item)"
                      type="button"
                      class="btn btn-sm btn-icon-action btn-outline-warning"
                      @click="impersonateUser(item)"
                      :title="labels.impersonateUser || 'Impersonate User'"
                    >
                      <i class="bi bi-box-arrow-in-right"></i>
                    </button>

                    <!-- Edit -->
                    <a
                      v-if="editRouteTemplate"
                      :href="getEditUrl(item.id)"
                      class="btn btn-sm btn-icon-action btn-outline-primary"
                      :title="labels.edit || 'Edit'"
                    >
                      <i class="bi bi-pencil"></i>
                    </a>

                    <!-- Delete -->
                    <button
                      v-if="deleteRouteTemplate"
                      type="button"
                      class="btn btn-sm btn-icon-action btn-outline-danger"
                      @click="confirmDelete(item)"
                      :title="labels.delete || 'Delete'"
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
        <div class="d-block d-md-none p-3">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="rows.length === 0" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
            <div class="fw-semibold mt-2">{{ labels.noRecords || 'No matching records found' }}</div>
            <div class="small">{{ labels.tryAdjusting || 'Try adjusting your search or filters' }}</div>
          </div>

          <!-- Mobile Cards -->
          <div v-else class="d-flex flex-column gap-3">
            <div
              v-for="item in rows"
              :key="'mob-' + item.id"
              class="card border-0 shadow-sm rounded-3 mobile-item-card"
            >
              <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; background: rgba(13, 110, 253, 0.12); color: #0d6efd;">
                      {{ getUserInitials(item.name) }}
                    </div>
                    <div>
                      <div class="fw-bold" style="color: var(--text-primary);">{{ item.name }}</div>
                      <div class="text-muted small">{{ item.email }}</div>
                    </div>
                  </div>
                  <span v-if="item.last_login_at" class="badge rounded-pill login-badge px-2 py-1 small d-inline-flex align-items-center gap-1">
                    <i class="bi bi-clock-history"></i>
                    {{ item.last_login_human || item.last_login_at_formatted }}
                  </span>
                  <span v-else class="badge rounded-pill never-badge px-2 py-1 small">
                    {{ labels.never || 'Never' }}
                  </span>
                </div>

                <!-- Roles -->
                <div class="d-flex flex-wrap gap-1 mb-2">
                  <span
                    v-for="r in (item.roles || [])"
                    :key="'mob-r-' + (r.id || r.name)"
                    class="badge rounded-pill px-2 py-0-5 small"
                    :class="getRoleBadgeClass(r.name)"
                  >
                    {{ r.name }}
                  </span>
                </div>

                <div v-if="item.service_body" class="mb-1 small text-muted">
                  <i class="bi bi-building me-1 text-primary"></i>
                  {{ isRtl ? (item.service_body.ar_name || item.service_body.en_name) : (item.service_body.en_name || item.service_body.ar_name) }}
                </div>

                <div class="mb-2 small d-flex align-items-center gap-1 text-muted">
                  <i class="bi bi-clock-history text-primary"></i>
                  <span class="fw-medium">{{ labels.lastLogin || 'Last Login' }}:</span>
                  <span v-if="item.last_login_at" class="fw-semibold text-dark">{{ item.last_login_at_formatted || item.last_login_at }}</span>
                  <span v-else class="text-muted fst-italic">{{ labels.never || 'Never' }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top mt-2">
                  <button
                    v-if="canImpersonate && impersonateRouteTemplate && !isItemSuperAdmin(item)"
                    type="button"
                    class="btn btn-sm btn-outline-warning rounded-pill px-2"
                    @click="impersonateUser(item)"
                    :title="labels.impersonateUser || 'Impersonate'"
                  >
                    <i class="bi bi-box-arrow-in-right"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-info rounded-pill px-3"
                    @click="openPreview(item)"
                  >
                    <i class="bi bi-eye me-1"></i>
                    {{ labels.quickView || 'View' }}
                  </button>
                  <a
                    v-if="editRouteTemplate"
                    :href="getEditUrl(item.id)"
                    class="btn btn-sm btn-outline-primary rounded-pill px-3"
                  >
                    <i class="bi bi-pencil me-1"></i>
                    {{ labels.edit || 'Edit' }}
                  </a>
                  <button
                    v-if="deleteRouteTemplate"
                    type="button"
                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                    @click="confirmDelete(item)"
                  >
                    <i class="bi bi-trash me-1"></i>
                    {{ labels.delete || 'Delete' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer & Pagination -->
        <div class="card-footer bg-transparent border-top py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div class="text-muted small">
            {{ labels.showing || 'Showing' }}
            <span class="fw-semibold">{{ paginationStart }}</span>
            {{ labels.to || 'to' }}
            <span class="fw-semibold">{{ paginationEnd }}</span>
            {{ labels.of || 'of' }}
            <span class="fw-semibold">{{ totalRows }}</span>
            {{ labels.entries || 'entries' }}
          </div>

          <div class="d-flex align-items-center gap-2">
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage <= 1 || loading"
              @click="goToPage(1)"
              :title="labels.first || 'First'"
            >
              <i :class="isRtl ? 'bi bi-chevron-double-right' : 'bi bi-chevron-double-left'"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage <= 1 || loading"
              @click="goToPage(currentPage - 1)"
              :title="labels.prev || 'Previous'"
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
              :title="labels.next || 'Next'"
            >
              <i :class="isRtl ? 'bi bi-chevron-left' : 'bi bi-chevron-right'"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary px-2"
              :disabled="currentPage >= totalPages || loading"
              @click="goToPage(totalPages)"
              :title="labels.last || 'Last'"
            >
              <i :class="isRtl ? 'bi bi-chevron-double-left' : 'bi bi-chevron-double-right'"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Preview Modal -->
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
              <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-person fs-5"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold mb-0" style="color: var(--text-primary);">
                  {{ labels.userDetails || 'User Details' }}
                </h5>
                <div class="small text-muted">{{ selectedItem?.name }} ({{ selectedItem?.email }})</div>
              </div>
            </div>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <div v-if="selectedItem" class="row g-3">
              <!-- Name & Email -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.user || 'Name & Email' }}</div>
                  <div class="fw-bold fs-6 mb-1" style="color: var(--text-primary);">
                    {{ selectedItem.name }}
                  </div>
                  <div class="text-muted small">
                    <i class="bi bi-envelope me-1"></i>
                    {{ selectedItem.email }}
                  </div>
                </div>
              </div>

              <!-- Last Login Date/Time -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.lastLogin || 'Last Login' }}</div>
                  <div class="mt-1">
                    <span v-if="selectedItem.last_login_at" class="badge rounded-pill login-badge px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1">
                      <i class="bi bi-clock-history text-primary"></i>
                      {{ selectedItem.last_login_at_formatted || selectedItem.last_login_at }}
                      <span v-if="selectedItem.last_login_human" class="text-muted fw-normal ms-1">({{ selectedItem.last_login_human }})</span>
                    </span>
                    <span v-else class="badge rounded-pill never-badge px-3 py-1-5 fw-semibold">
                      <i class="bi bi-dash-circle me-1 opacity-50"></i>
                      {{ labels.never || 'Never' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Roles -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-2">{{ labels.assignedRoles || 'Assigned Roles' }}</div>
                  <div class="d-flex flex-wrap gap-1">
                    <span
                      v-for="r in (selectedItem.roles || [])"
                      :key="'modal-r-' + (r.id || r.name)"
                      class="badge rounded-pill px-2 py-1 small fw-semibold"
                      :class="getRoleBadgeClass(r.name)"
                    >
                      {{ r.name }}
                    </span>
                    <span v-if="!selectedItem.roles || selectedItem.roles.length === 0" class="text-muted small">
                      None
                    </span>
                  </div>
                </div>
              </div>

              <!-- Associated Service Body -->
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 detail-info-box h-100">
                  <div class="small text-muted fw-semibold mb-1">{{ labels.associatedServiceBody || 'Service Body' }}</div>
                  <div class="fw-bold fs-6 mt-1" style="color: var(--text-primary);">
                    {{ selectedItem.service_body ? (isRtl ? (selectedItem.service_body.ar_name || selectedItem.service_body.en_name) : (selectedItem.service_body.en_name || selectedItem.service_body.ar_name)) : 'N/A' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <button
                v-if="canImpersonate && impersonateRouteTemplate && selectedItem && !isItemSuperAdmin(selectedItem)"
                type="button"
                class="btn btn-warning btn-sm px-3 text-dark fw-semibold"
                @click="impersonateUser(selectedItem)"
              >
                <i class="bi bi-box-arrow-in-right me-1"></i>
                {{ labels.impersonateUser || 'Impersonate' }}
              </button>
              <a
                v-if="editRouteTemplate && selectedItem"
                :href="getEditUrl(selectedItem.id)"
                class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold"
              >
                <i class="bi bi-pencil-square"></i>
                {{ labels.edit || 'Edit' }}
              </a>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="closeModal">
              {{ labels.close || 'Close' }}
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
    default: 'Add User'
  },
  editRouteTemplate: {
    type: String,
    default: ''
  },
  deleteRouteTemplate: {
    type: String,
    default: ''
  },
  impersonateRouteTemplate: {
    type: String,
    default: ''
  },
  canImpersonate: {
    type: Boolean,
    default: false
  },
  roles: {
    type: Array,
    default: () => []
  },
  initialKpiStats: {
    type: Object,
    default: () => ({
      total_users: 0,
      active_recently: 0,
      never_logged_in: 0,
      service_body_officers: 0
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
const selectedRole = ref('');

// KPIs
const kpiData = reactive({
  total_users: props.initialKpiStats?.total_users || 0,
  active_recently: props.initialKpiStats?.active_recently || 0,
  never_logged_in: props.initialKpiStats?.never_logged_in || 0,
  service_body_officers: props.initialKpiStats?.service_body_officers || 0
});

// Modal state
const showModal = ref(false);
const selectedItem = ref(null);

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
  return searchQuery.value !== '' || selectedRole.value !== '';
});

const resetAllFilters = () => {
  searchQuery.value = '';
  selectedRole.value = '';
  currentPage.value = 1;
  fetchData();
};

const totalPages = computed(() => {
  return Math.ceil(totalRows.value / pageSize.value) || 1;
});

const paginationStart = computed(() => {
  if (totalRows.value === 0) return 0;
  return (currentPage.value - 1) * pageSize.value + 1;
});

const paginationEnd = computed(() => {
  const end = currentPage.value * pageSize.value;
  return end > totalRows.value ? totalRows.value : end;
});

const setSort = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = column === 'last_login_at' ? 'desc' : 'asc';
  }
  currentPage.value = 1;
  fetchData();
};

const getSortIcon = (column) => {
  if (sortColumn.value !== column) return 'bi-arrow-down-up opacity-25';
  return sortDirection.value === 'asc' ? 'bi-sort-up text-primary' : 'bi-sort-down text-primary';
};

const goToPage = (page) => {
  if (page < 1 || page > totalPages.value || page === currentPage.value) return;
  currentPage.value = page;
  fetchData();
};

const getEditUrl = (id) => {
  return props.editRouteTemplate ? props.editRouteTemplate.replace('__ID__', id) : '#';
};

const getImpersonateUrl = (id) => {
  return props.impersonateRouteTemplate ? props.impersonateRouteTemplate.replace('__ID__', id) : '#';
};

const getUserInitials = (name) => {
  if (!name) return 'U';
  const parts = name.trim().split(/\s+/);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return parts[0].substring(0, 2).toUpperCase();
};

const getRoleBadgeClass = (roleName) => {
  const r = (roleName || '').toLowerCase();
  if (r.includes('super admin')) return 'role-badge-super-admin';
  if (r.includes('admin')) return 'role-badge-admin';
  if (r.includes('rsc')) return 'role-badge-rsc';
  if (r.includes('committee')) return 'role-badge-committee';
  if (r.includes('workgroup')) return 'role-badge-workgroup';
  if (r.includes('gsr')) return 'role-badge-gsr';
  if (r.includes('servicebody')) return 'role-badge-servicebody';
  return 'role-badge-default';
};

const openPreview = (item) => {
  selectedItem.value = item;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedItem.value = null;
};

const fetchData = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
      pagesize: pageSize.value,
      sort_by: sortColumn.value,
      sort_column: sortColumn.value,
      sort_dir: sortDirection.value,
      sort_direction: sortDirection.value
    };

    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim();
    }
    if (selectedRole.value) {
      params.role_name = selectedRole.value;
    }

    const response = await axios.get(props.fetchUrl, {
      params,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = response.data;
    if (data && data.data) {
      rows.value = data.data;
      totalRows.value = data.total || 0;
      currentPage.value = data.current_page || 1;
    } else if (Array.isArray(data)) {
      rows.value = data;
      totalRows.value = data.length;
    }
  } catch (error) {
    console.error('Failed to load users:', error);
  } finally {
    loading.value = false;
  }
};

const isItemSuperAdmin = (item) => {
  return (item.roles || []).some(r => (typeof r === 'object' ? r.name : r) === 'super admin');
};

const impersonateUser = async (item) => {
  const confirmMsg = (props.labels.confirmImpersonate || 'Are you sure you want to impersonate user ":name"?')
    .replace(':name', item.name || item.email);

  const result = await Swal.fire({
    title: props.labels.impersonateUser || 'Impersonate User',
    text: confirmMsg,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ffc107',
    cancelButtonColor: '#6c757d',
    confirmButtonText: props.labels.impersonateUser || 'Impersonate',
    cancelButtonText: props.labels.close || 'Cancel',
    reverseButtons: isRtl.value
  });

  if (result.isConfirmed) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = getImpersonateUrl(item.id);

    const token = props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = '_token';
      input.value = token;
      form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
  }
};

const confirmDelete = async (item) => {
  const confirmMsg = (props.labels.confirmDelete || 'Are you sure you want to delete user ":name"?')
    .replace(':name', item.name || item.email || item.id);

  const result = await Swal.fire({
    title: props.labels.delete || 'Delete',
    text: confirmMsg,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: props.labels.delete || 'Delete',
    cancelButtonText: props.labels.close || 'Cancel',
    reverseButtons: isRtl.value
  });

  if (result.isConfirmed) {
    try {
      const deleteUrl = props.deleteRouteTemplate.replace('__ID__', item.id);
      const res = await axios.delete(deleteUrl, {
        headers: {
          'X-CSRF-TOKEN': props.csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (res.data?.success || res.status === 200 || res.status === 204) {
        Swal.fire({
          icon: 'success',
          title: props.labels.deletedSuccess || 'User deleted successfully.',
          timer: 2000,
          showConfirmButton: false
        });
        kpiData.total_users = Math.max(0, kpiData.total_users - 1);
        fetchData();
      }
    } catch (err) {
      console.error('Delete error:', err);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.response?.data?.message || 'Failed to delete user.'
      });
    }
  }
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.glass-card {
  background: var(--card-bg, #ffffff);
  border-radius: 1rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.kpi-card {
  border-radius: 1rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

.search-box-wrapper {
  min-width: 250px;
}
.search-box {
  position: relative;
  display: flex;
  align-items: center;
}
.search-icon {
  position: absolute;
  left: 0.85rem;
  color: #6c757d;
  pointer-events: none;
}
[dir="rtl"] .search-icon {
  left: auto;
  right: 0.85rem;
}
.search-input {
  width: 100%;
  padding: 0.45rem 2.2rem;
  font-size: 0.875rem;
  border-radius: 2rem;
  border: 1px solid rgba(0, 0, 0, 0.15);
  background-color: var(--input-bg, #f8f9fa);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.search-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
  background-color: #fff;
}
.search-clear-btn {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  color: #adb5bd;
  cursor: pointer;
  padding: 0;
}
[dir="rtl"] .search-clear-btn {
  right: auto;
  left: 0.75rem;
}

.filter-select {
  border-radius: 2rem;
  border: 1px solid rgba(0, 0, 0, 0.15);
  padding: 0.45rem 1.75rem 0.45rem 1rem;
  font-size: 0.875rem;
}
[dir="rtl"] .filter-select {
  padding: 0.45rem 1rem 0.45rem 1.75rem;
}

.custom-datatable th {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6c757d;
  border-bottom: 2px solid rgba(0, 0, 0, 0.05);
}
.table-data-row {
  transition: background-color 0.15s ease;
}

.btn-icon-action {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
}

.custom-modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}
.modal-glassmorphic {
  background: #ffffff;
  border-radius: 1.25rem;
}
.detail-info-box {
  background: rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(0, 0, 0, 0.06);
}
.mobile-item-card {
  border: 1px solid rgba(0, 0, 0, 0.06);
}
.cursor-pointer {
  cursor: pointer;
}

/* Role Badges with explicit, high-contrast colors (no white text on light backgrounds!) */
.role-badge-super-admin {
  background-color: #fee2e2 !important;
  color: #991b1b !important;
  border: 1px solid #fca5a5 !important;
}

.role-badge-admin {
  background-color: #dbeafe !important;
  color: #1e40af !important;
  border: 1px solid #93c5fd !important;
}

.role-badge-rsc {
  background-color: #f3e8ff !important;
  color: #6b21a8 !important;
  border: 1px solid #d8b4fe !important;
}

.role-badge-committee {
  background-color: #dcfce7 !important;
  color: #166534 !important;
  border: 1px solid #86efac !important;
}

.role-badge-workgroup {
  background-color: #fef3c7 !important;
  color: #92400e !important;
  border: 1px solid #fde68a !important;
}

.role-badge-gsr {
  background-color: #e0f2fe !important;
  color: #0369a1 !important;
  border: 1px solid #bae6fd !important;
}

.role-badge-servicebody {
  background-color: #e2e8f0 !important;
  color: #334155 !important;
  border: 1px solid #cbd5e1 !important;
}

.role-badge-default {
  background-color: #f1f5f9 !important;
  color: #475569 !important;
  border: 1px solid #e2e8f0 !important;
}

.login-badge {
  background-color: #f0fdf4 !important;
  color: #15803d !important;
  border: 1px solid #bbf7d0 !important;
}

.never-badge {
  background-color: #f8fafc !important;
  color: #64748b !important;
  border: 1px solid #e2e8f0 !important;
}

.login-relative-time {
  font-size: 0.725rem;
}
</style>
