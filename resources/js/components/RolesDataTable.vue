<template>
  <div class="roles-manager-container" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Top Summary KPI Metric Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper bg-primary-subtle text-primary rounded-3 p-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-shield-shaded fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.totalRoles }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.total_roles }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(102, 16, 242, 0.1); color: #6610f2;">
              <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.systemRoles }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.system_roles }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper bg-success-subtle text-success rounded-3 p-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-key-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.totalPermissions }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.total_permissions }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper bg-info-subtle text-info rounded-3 p-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">{{ labels.activeAssignments }}</div>
              <div class="kpi-value fs-4 fw-bold" style="color: var(--text-primary);">{{ kpiData.total_assignments }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controls & Action Header -->
    <div class="card border-0 shadow-sm mb-4 glass-card">
      <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
          <!-- Live Search -->
          <div class="search-box-wrapper">
            <div class="search-box">
              <span class="search-icon">
                <i class="bi bi-search"></i>
              </span>
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
                :title="cancelBtnLabel"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>

          <!-- Type Filter -->
          <div class="d-flex align-items-center">
            <select v-model="typeFilter" class="form-select form-select-sm filter-select">
              <option value="all">{{ labels.allTypes }}</option>
              <option value="system">{{ labels.systemRoles }}</option>
              <option value="custom">{{ labels.customRole }}</option>
            </select>
          </div>
        </div>

        <!-- Create Button -->
        <div class="d-flex align-items-center gap-2">
          <a v-if="createRoute" :href="createRoute" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm">
            <i class="bi bi-plus-lg"></i>
            {{ labels.newRole }}
          </a>
        </div>
      </div>
    </div>

    <!-- Roles Table Card -->
    <div class="card border-0 shadow-lg glass-card main-table-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 custom-roles-table">
            <thead>
              <tr class="table-header-row">
                <th scope="col" class="ps-4 py-3 text-uppercase small fw-bold" style="min-width: 240px;">
                  {{ labels.roleInfo }}
                </th>
                <th scope="col" class="py-3 text-uppercase small fw-bold text-center" style="min-width: 140px;">
                  {{ labels.assignedUsers }}
                </th>
                <th scope="col" class="py-3 text-uppercase small fw-bold" style="min-width: 320px;">
                  {{ labels.permissionsBreakdown }}
                </th>
                <th scope="col" class="pe-4 py-3 text-uppercase small fw-bold text-end" style="min-width: 320px;">
                  {{ labels.actions }}
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading Skeleton / Spinner -->
              <tr v-if="loading">
                <td colspan="4" class="text-center py-5">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <div class="text-muted mt-2 small">{{ labels.loading }}</div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="filteredRoles.length === 0">
                <td colspan="4" class="text-center py-5">
                  <div class="text-muted mb-2"><i class="bi bi-shield-x fs-1 opacity-50"></i></div>
                  <h6 class="fw-semibold text-secondary mb-1">{{ labels.noRolesFound }}</h6>
                  <p class="small text-muted mb-0">{{ labels.noRolesMatchCriteria }}</p>
                </td>
              </tr>

              <!-- Roles Rows -->
              <tr
                v-else
                v-for="role in filteredRoles"
                :key="role.id"
                class="role-row"
              >
                <!-- Role Info Column -->
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-start gap-3">
                    <div
                      class="role-avatar rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                      :class="role.is_system_role ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary border'"
                      style="width: 40px; height: 40px;"
                    >
                      <i :class="role.is_system_role ? 'bi bi-shield-lock-fill' : 'bi bi-person-badge'"></i>
                    </div>
                    <div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-bold fs-6 role-name" style="color: var(--text-primary);">
                          {{ role.name }}
                        </span>
                        <span
                          v-if="role.is_system_role"
                          class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small"
                          :title="labels.systemRoleProtectedDesc"
                        >
                          <i class="bi bi-shield-lock me-1"></i>{{ labels.systemRole }}
                        </span>
                        <span
                          v-else
                          class="badge rounded-pill bg-light text-secondary border px-2 py-1 small"
                        >
                          {{ labels.customRole }}
                        </span>
                      </div>
                      <div class="role-desc small text-muted mt-1 text-truncate" style="max-width: 320px;" :title="role.description">
                        {{ role.description || labels.noDescription }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Assigned Users Column -->
                <td class="py-3 text-center">
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 user-count-btn d-inline-flex align-items-center gap-2"
                    @click="openDetailsModal(role, 'users')"
                    :title="labels.clickToViewUsers"
                  >
                    <i class="bi bi-people"></i>
                    <span class="fw-bold">{{ role.users_count ?? (role.users ? role.users.length : 0) }}</span>
                  </button>
                </td>

                <!-- Permissions Breakdown Column -->
                <td class="py-3">
                  <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge rounded-pill bg-primary text-white px-2 py-1 fw-semibold">
                        <i class="bi bi-key me-1"></i>
                        {{ role.permissions_count ?? (role.permissions ? role.permissions.length : 0) }} {{ labels.permissionsLabel }}
                      </span>

                      <!-- Category Breakdown Chips -->
                      <span
                        v-if="role.category_counts && role.category_counts.agenda > 0"
                        class="badge rounded-pill bg-light text-dark border px-2 py-1 small d-inline-flex align-items-center gap-1"
                        :title="labels.catAgenda"
                      >
                        <i class="bi bi-journals text-primary"></i>
                        {{ role.category_counts.agenda }}
                      </span>

                      <span
                        v-if="role.category_counts && role.category_counts.store > 0"
                        class="badge rounded-pill bg-light text-dark border px-2 py-1 small d-inline-flex align-items-center gap-1"
                        :title="labels.catStore"
                      >
                        <i class="bi bi-box-seam text-warning"></i>
                        {{ role.category_counts.store }}
                      </span>

                      <span
                        v-if="role.category_counts && role.category_counts.calendar > 0"
                        class="badge rounded-pill bg-light text-dark border px-2 py-1 small d-inline-flex align-items-center gap-1"
                        :title="labels.catCalendar"
                      >
                        <i class="bi bi-calendar-check text-success"></i>
                        {{ role.category_counts.calendar }}
                      </span>

                      <span
                        v-if="role.category_counts && role.category_counts.forms > 0"
                        class="badge rounded-pill bg-light text-dark border px-2 py-1 small d-inline-flex align-items-center gap-1"
                        :title="labels.catForms"
                      >
                        <i class="bi bi-ui-checks text-info"></i>
                        {{ role.category_counts.forms }}
                      </span>

                      <span
                        v-if="role.category_counts && role.category_counts.general > 0"
                        class="badge rounded-pill bg-light text-dark border px-2 py-1 small d-inline-flex align-items-center gap-1"
                        :title="labels.catGeneral"
                      >
                        <i class="bi bi-gear-fill text-secondary"></i>
                        {{ role.category_counts.general }}
                      </span>

                      <!-- Quick Eye Button to View Details -->
                      <button
                        type="button"
                        class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1"
                        @click="openDetailsModal(role, 'permissions')"
                      >
                        <i class="bi bi-eye"></i>
                        <span class="small">{{ labels.view }}</span>
                      </button>
                    </div>
                  </div>
                </td>

                <!-- Actions Column -->
                <td class="pe-4 py-3 text-end">
                  <div class="d-inline-flex align-items-center gap-2 justify-content-end action-buttons-group">
                    <!-- Assign / Edit Permissions Button -->
                    <a
                      :href="getAssignPermissionsUrl(role.id)"
                      class="btn btn-sm btn-outline-primary action-btn d-inline-flex align-items-center justify-content-center gap-1"
                      :title="labels.assignPermissions"
                    >
                      <i class="bi bi-shield-check"></i>
                      <span>{{ labels.assignPermissions }}</span>
                    </a>

                    <!-- Edit Info Button -->
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-secondary action-btn d-inline-flex align-items-center justify-content-center gap-1"
                      @click="openEditModal(role)"
                      :title="labels.editRoleInfo"
                    >
                      <i class="bi bi-pencil"></i>
                      <span>{{ labels.edit || 'Edit' }}</span>
                    </button>

                    <!-- Delete Button (Disabled for System Roles) -->
                    <button
                      type="button"
                      class="btn btn-sm action-btn d-inline-flex align-items-center justify-content-center gap-1"
                      :class="role.is_system_role ? 'btn-outline-secondary disabled opacity-50' : 'btn-outline-danger'"
                      :disabled="role.is_system_role"
                      @click="!role.is_system_role && confirmDelete(role)"
                      :title="role.is_system_role ? labels.systemRoleCannotDelete : deleteBtnLabel"
                    >
                      <i class="bi bi-trash"></i>
                      <span>{{ deleteBtnLabel }}</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="totalPages > 1" class="card-footer bg-transparent border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="small text-muted">
            {{ paginationInfoText }}
          </div>
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="changePage(currentPage - 1)">{{ labels.prev }}</button>
              </li>
              <li
                v-for="p in totalPages"
                :key="p"
                class="page-item"
                :class="{ active: currentPage === p }"
              >
                <button class="page-link" @click="changePage(p)">{{ p }}</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="changePage(currentPage + 1)">{{ labels.next }}</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ROLE DETAILS MODAL (Categorized Permissions Cards + Assigned Users)       -->
    <!-- ========================================================================= -->
    <div
      v-if="showDetailsModal"
      class="modal fade show d-block custom-modal-backdrop"
      tabindex="-1"
      role="dialog"
      aria-modal="true"
      @click.self="closeDetailsModal"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="background: var(--glass-bg, #ffffff); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid var(--glass-border);">
          <!-- Modal Header -->
          <div class="modal-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
              <div
                class="rounded-circle d-flex align-items-center justify-content-center"
                :class="selectedRole?.is_system_role ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary border'"
                style="width: 44px; height: 44px;"
              >
                <i :class="selectedRole?.is_system_role ? 'bi bi-shield-lock-fill fs-5' : 'bi bi-person-badge fs-5'"></i>
              </div>
              <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <h5 class="modal-title fw-bold mb-0" style="color: var(--text-primary);">
                    {{ selectedRole?.name }}
                  </h5>
                  <span
                    v-if="selectedRole?.is_system_role"
                    class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle small"
                  >
                    <i class="bi bi-shield-lock me-1"></i>{{ labels.systemRole }}
                  </span>
                </div>
                <div class="small text-muted mt-1">
                  {{ selectedRole?.description || labels.noDescription }}
                </div>
              </div>
            </div>
            <button type="button" class="btn-close" @click="closeDetailsModal" aria-label="Close"></button>
          </div>

          <!-- Modal Tabs -->
          <div class="px-4 pt-3 border-bottom">
            <ul class="nav nav-tabs border-bottom-0">
              <li class="nav-item">
                <button
                  class="nav-link fw-semibold pb-3"
                  :class="{ active: detailsActiveTab === 'permissions' }"
                  @click="detailsActiveTab = 'permissions'"
                  type="button"
                >
                  <i class="bi bi-shield-check me-2"></i>
                  {{ labels.permissionsTab }}
                  <span class="badge rounded-pill bg-primary-subtle text-primary ms-1 small">
                    {{ modalPermissionsCount }}
                  </span>
                </button>
              </li>
              <li class="nav-item">
                <button
                  class="nav-link fw-semibold pb-3"
                  :class="{ active: detailsActiveTab === 'users' }"
                  @click="detailsActiveTab = 'users'"
                  type="button"
                >
                  <i class="bi bi-people me-2"></i>
                  {{ labels.assignedUsersTab }}
                  <span class="badge rounded-pill bg-secondary-subtle text-secondary ms-1 small">
                    {{ modalUsersList.length }}
                  </span>
                </button>
              </li>
            </ul>
          </div>

          <!-- Modal Body -->
          <div class="modal-body p-4">
            <!-- Loading state inside modal -->
            <div v-if="modalLoading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <div class="text-muted mt-2 small">{{ labels.loadingDetails }}</div>
            </div>

            <!-- TAB 1: PERMISSIONS -->
            <div v-else-if="detailsActiveTab === 'permissions'">
              <!-- Filter inside permissions -->
              <div class="mb-3">
                <div class="search-box">
                  <span class="search-icon"><i class="bi bi-search"></i></span>
                  <input
                    type="text"
                    v-model="modalPermSearch"
                    class="search-input"
                    :placeholder="labels.searchInPermissions"
                  />
                  <button v-if="modalPermSearch" class="search-clear-btn" type="button" @click="modalPermSearch = ''" :title="cancelBtnLabel">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>
              </div>

              <!-- Categorized Permissions Display -->
              <div v-if="filteredGroupedCategories.length === 0" class="text-center py-4 text-muted">
                <i class="bi bi-shield-slash fs-2 opacity-50"></i>
                <div class="mt-2">{{ labels.noPermissionsAssigned }}</div>
              </div>

              <div v-else class="d-flex flex-column gap-4">
                <div
                  v-for="cat in filteredGroupedCategories"
                  :key="cat.key"
                  class="permission-cat-group"
                >
                  <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <div class="fw-bold d-flex align-items-center gap-2" style="color: var(--text-primary);">
                      <i :class="cat.icon + ' text-primary'"></i>
                      <span>{{ cat.title }}</span>
                      <span class="badge bg-light text-secondary border rounded-pill small">
                        {{ cat.permissions.length }}
                      </span>
                    </div>
                  </div>

                  <!-- Two-Line Cards Grid -->
                  <div class="row g-2">
                    <div
                      v-for="perm in cat.permissions"
                      :key="perm.id"
                      class="col-12 col-md-6"
                    >
                      <div class="permission-item-card p-3 rounded-3 h-100 border d-flex flex-column justify-content-between" style="background: rgba(255,255,255,0.03);">
                        <div>
                          <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-shield-check text-primary fs-6"></i>
                            <span class="fw-bold small" style="color: var(--text-primary);">{{ perm.display_name }}</span>
                          </div>
                          <div v-if="perm.description" class="small text-muted lh-sm mb-2" style="margin-inline-start: 1.5rem;">
                            {{ perm.description }}
                          </div>
                        </div>
                        <div style="margin-inline-start: 1.5rem;">
                          <span class="badge bg-light text-secondary border font-monospace py-1 px-2" style="font-size: 0.72rem;">
                            <i class="bi bi-key-fill me-1 opacity-75"></i>{{ perm.name }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 2: ASSIGNED USERS -->
            <div v-else-if="detailsActiveTab === 'users'">
              <!-- Filter inside users -->
              <div class="mb-3">
                <div class="search-box">
                  <span class="search-icon"><i class="bi bi-search"></i></span>
                  <input
                    type="text"
                    v-model="modalUserSearch"
                    class="search-input"
                    :placeholder="labels.searchInUsers"
                  />
                  <button v-if="modalUserSearch" class="search-clear-btn" type="button" @click="modalUserSearch = ''" :title="cancelBtnLabel">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>
              </div>

              <div v-if="filteredModalUsers.length === 0" class="text-center py-4 text-muted">
                <i class="bi bi-people fs-2 opacity-50"></i>
                <div class="mt-2">{{ labels.noUsersAssigned }}</div>
              </div>

              <div v-else class="row g-2">
                <div
                  v-for="user in filteredModalUsers"
                  :key="user.id"
                  class="col-12 col-md-6"
                >
                  <div class="user-card p-3 rounded-3 border d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.03);">
                    <div class="user-avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                      {{ (user.name || user.email || 'U').charAt(0).toUpperCase() }}
                    </div>
                    <div class="text-truncate">
                      <div class="fw-semibold small text-truncate" style="color: var(--text-primary);">
                        {{ user.name }}
                      </div>
                      <div class="small text-muted text-truncate">
                        {{ user.email }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
            <a
              :href="getAssignPermissionsUrl(selectedRole?.id)"
              class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-2 fw-semibold"
            >
              <i class="bi bi-shield-check"></i>
              {{ labels.assignPermissions }}
            </a>
            <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="closeDetailsModal">
              {{ labels.close }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- EDIT ROLE INFO MODAL (Name & Description)                                  -->
    <!-- ========================================================================= -->
    <div
      v-if="showEditModal"
      class="modal fade show d-block custom-modal-backdrop"
      tabindex="-1"
      role="dialog"
      aria-modal="true"
      @click.self="closeEditModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: var(--glass-bg, #ffffff); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid var(--glass-border);">
          <form @submit.prevent="submitEditRole">
            <div class="modal-header border-bottom py-3 px-4">
              <h5 class="modal-title fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                {{ labels.editRoleInfo }}
              </h5>
              <button type="button" class="btn-close" @click="closeEditModal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
              <div v-if="editError" class="alert alert-danger py-2 px-3 small mb-3">
                {{ editError }}
              </div>

              <!-- Role Name -->
              <div class="mb-3">
                <label class="form-label small fw-semibold" style="color: var(--text-primary);">
                  {{ labels.roleName }} <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  v-model="editForm.name"
                  class="form-control"
                  :disabled="editForm.is_system_role"
                  required
                />
                <div v-if="editForm.is_system_role" class="form-text small text-warning">
                  <i class="bi bi-info-circle me-1"></i>
                  {{ labels.systemRoleCannotRename }}
                </div>
              </div>

              <!-- Role Description -->
              <div class="mb-3">
                <label class="form-label small fw-semibold" style="color: var(--text-primary);">
                  {{ labels.roleDescription }}
                </label>
                <textarea
                  v-model="editForm.description"
                  class="form-control"
                  rows="3"
                  :placeholder="labels.roleDescriptionPlaceholder"
                ></textarea>
              </div>
            </div>

            <div class="modal-footer border-top py-3 px-4 d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="closeEditModal">
                {{ labels.cancel }}
              </button>
              <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold" :disabled="editSubmitting">
                <span v-if="editSubmitting" class="spinner-border spinner-border-sm me-1" role="status"></span>
                {{ labels.saveChanges }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  fetchUrl: {
    type: String,
    required: true
  },
  createRoute: {
    type: String,
    default: ''
  },
  assignPermissionsTemplate: {
    type: String,
    required: true
  },
  detailsRouteTemplate: {
    type: String,
    required: true
  },
  updateRouteTemplate: {
    type: String,
    required: true
  },
  deleteRouteTemplate: {
    type: String,
    required: true
  },
  initialKpiStats: {
    type: Object,
    default: () => ({
      total_roles: 0,
      system_roles: 0,
      total_permissions: 0,
      total_assignments: 0
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

// RTL detection
const isRtl = computed(() => {
  return document.documentElement.getAttribute('dir') === 'rtl';
});

const deleteBtnLabel = computed(() => {
  if (props.labels?.delete && props.labels.delete !== 'messages.delete') {
    return props.labels.delete;
  }
  return isRtl.value ? 'حذف' : 'Delete';
});

const cancelBtnLabel = computed(() => {
  if (props.labels?.cancel && props.labels.cancel !== 'messages.cancel') {
    return props.labels.cancel;
  }
  return isRtl.value ? 'إلغاء' : 'Cancel';
});

// State
const roles = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const typeFilter = ref('all');
const currentPage = ref(1);
const pageSize = ref(15);
const totalRows = ref(0);

const kpiData = reactive({
  total_roles: props.initialKpiStats?.total_roles || 0,
  system_roles: props.initialKpiStats?.system_roles || 0,
  total_permissions: props.initialKpiStats?.total_permissions || 0,
  total_assignments: props.initialKpiStats?.total_assignments || 0
});

// Fetch roles data
const fetchRoles = async () => {
  loading.value = true;
  try {
    const response = await axios.get(props.fetchUrl, {
      params: {
        page: currentPage.value,
        pagesize: 100, // retrieve full list or large batch for rich client filtering
        search: searchQuery.value
      },
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = response.data;
    if (data && Array.isArray(data.data)) {
      roles.value = data.data;
      totalRows.value = data.total || data.data.length;
    } else if (Array.isArray(data)) {
      roles.value = data;
      totalRows.value = data.length;
    }
  } catch (err) {
    console.error('Failed to fetch roles:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchRoles();
});

// Filtered roles based on search and type filter
const filteredRoles = computed(() => {
  let list = roles.value;

  if (typeFilter.value === 'system') {
    list = list.filter(r => r.is_system_role);
  } else if (typeFilter.value === 'custom') {
    list = list.filter(r => !r.is_system_role);
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase();
    list = list.filter(r => {
      const nameMatch = r.name && r.name.toLowerCase().includes(q);
      const descMatch = r.description && r.description.toLowerCase().includes(q);
      const permMatch = r.permissions && r.permissions.some(p => {
        return (p.name && p.name.toLowerCase().includes(q)) ||
               (p.display_name && p.display_name.toLowerCase().includes(q));
      });
      return nameMatch || descMatch || permMatch;
    });
  }

  return list;
});

const totalPages = computed(() => {
  return Math.ceil(filteredRoles.value.length / pageSize.value) || 1;
});

const paginationInfoText = computed(() => {
  const count = filteredRoles.value.length;
  return `${props.labels.showing || 'Showing'} ${count} ${props.labels.rolesList || 'roles'}`;
});

const onSearchInput = () => {
  currentPage.value = 1;
};

const clearSearch = () => {
  searchQuery.value = '';
  currentPage.value = 1;
};

const changePage = (p) => {
  if (p >= 1 && p <= totalPages.value) {
    currentPage.value = p;
  }
};

const getAssignPermissionsUrl = (id) => {
  if (!id) return '#';
  return props.assignPermissionsTemplate.replace('{id}', id);
};

// =========================================================================
// DETAILS MODAL LOGIC
// =========================================================================
const showDetailsModal = ref(false);
const modalLoading = ref(false);
const selectedRole = ref(null);
const detailsActiveTab = ref('permissions');
const modalGroupedCategories = ref([]);
const modalUsersList = ref([]);
const modalPermSearch = ref('');
const modalUserSearch = ref('');

const openDetailsModal = async (role, defaultTab = 'permissions') => {
  selectedRole.value = role;
  detailsActiveTab.value = defaultTab;
  showDetailsModal.value = true;
  modalLoading.value = true;
  modalPermSearch.value = '';
  modalUserSearch.value = '';

  try {
    const url = props.detailsRouteTemplate.replace('{id}', role.id);
    const res = await axios.get(url, {
      headers: { 'Accept': 'application/json' }
    });

    if (res.data) {
      modalGroupedCategories.value = Object.values(res.data.grouped_permissions || {});
      modalUsersList.value = res.data.users || [];
    }
  } catch (err) {
    console.error('Failed to load role details:', err);
  } finally {
    modalLoading.value = false;
  }
};

const closeDetailsModal = () => {
  showDetailsModal.value = false;
  selectedRole.value = null;
  modalGroupedCategories.value = [];
  modalUsersList.value = [];
};

const modalPermissionsCount = computed(() => {
  return modalGroupedCategories.value.reduce((acc, cat) => acc + (cat.permissions ? cat.permissions.length : 0), 0);
});

const filteredGroupedCategories = computed(() => {
  const q = modalPermSearch.value.trim().toLowerCase();
  if (!q) {
    return modalGroupedCategories.value.filter(cat => cat.permissions && cat.permissions.length > 0);
  }

  return modalGroupedCategories.value
    .map(cat => {
      const matchedPerms = (cat.permissions || []).filter(p => {
        return (p.name && p.name.toLowerCase().includes(q)) ||
               (p.display_name && p.display_name.toLowerCase().includes(q)) ||
               (p.description && p.description.toLowerCase().includes(q));
      });
      return {
        ...cat,
        permissions: matchedPerms
      };
    })
    .filter(cat => cat.permissions.length > 0);
});

const filteredModalUsers = computed(() => {
  const q = modalUserSearch.value.trim().toLowerCase();
  if (!q) return modalUsersList.value;
  return modalUsersList.value.filter(u => {
    return (u.name && u.name.toLowerCase().includes(q)) ||
           (u.email && u.email.toLowerCase().includes(q));
  });
});

// =========================================================================
// EDIT ROLE MODAL LOGIC
// =========================================================================
const showEditModal = ref(false);
const editSubmitting = ref(false);
const editError = ref('');
const editForm = reactive({
  id: null,
  name: '',
  description: '',
  is_system_role: false
});

const openEditModal = (role) => {
  editForm.id = role.id;
  editForm.name = role.name;
  editForm.description = role.description || '';
  editForm.is_system_role = !!role.is_system_role;
  editError.value = '';
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editError.value = '';
};

const submitEditRole = async () => {
  editSubmitting.value = true;
  editError.value = '';

  try {
    const url = props.updateRouteTemplate.replace('{id}', editForm.id);
    const res = await axios.put(url, {
      name: editForm.name,
      description: editForm.description
    }, {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (res.data?.success) {
      // Update local role list
      const idx = roles.value.findIndex(r => r.id === editForm.id);
      if (idx !== -1) {
        roles.value[idx].name = editForm.name;
        roles.value[idx].description = editForm.description;
      }
      closeEditModal();
      if (window.Swal) {
        window.Swal.fire({
          icon: 'success',
          title: props.labels.roleUpdatedSuccess || 'Role updated successfully',
          timer: 2000,
          showConfirmButton: false
        });
      }
    }
  } catch (err) {
    if (err.response?.data?.message) {
      editError.value = err.response.data.message;
    } else {
      editError.value = 'Failed to update role. Please check input.';
    }
  } finally {
    editSubmitting.value = false;
  }
};

// =========================================================================
// DELETE ROLE LOGIC
// =========================================================================
const confirmDelete = async (role) => {
  if (role.is_system_role) {
    if (window.Swal) {
      window.Swal.fire({
        icon: 'error',
        title: props.labels.cannotDeleteSystemRole || 'Protected System Role',
        text: props.labels.systemRoleCannotDelete
      });
    }
    return;
  }

  const confirmMsg = (props.labels.confirmDeleteRole || 'Are you sure you want to delete role ":name"?').replace(':name', role.name);

  if (window.Swal) {
    const result = await window.Swal.fire({
      title: confirmMsg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: props.labels.delete || 'Delete',
      cancelButtonText: props.labels.cancel || 'Cancel'
    });
    if (!result.isConfirmed) return;
  } else {
    if (!confirm(confirmMsg)) return;
  }

  try {
    const url = props.deleteRouteTemplate.replace('{id}', role.id);
    const res = await axios.delete(url, {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (res.data?.success || res.status === 200) {
      roles.value = roles.value.filter(r => r.id !== role.id);
      kpiData.total_roles--;
      if (window.Swal) {
        window.Swal.fire({
          icon: 'success',
          title: props.labels.roleDeletedSuccess || 'Role deleted',
          timer: 1500,
          showConfirmButton: false
        });
      }
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to delete role.';
    if (window.Swal) {
      window.Swal.fire({ icon: 'error', title: 'Error', text: msg });
    } else {
      alert(msg);
    }
  }
};
</script>

<style scoped>
.roles-manager-container {
  font-family: inherit;
}

.kpi-card {
  background: var(--glass-bg, rgba(255, 255, 255, 0.85));
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.2)) !important;
  border-radius: 14px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}

.kpi-icon-wrapper {
  width: 52px;
  height: 52px;
}

.glass-card {
  background: var(--glass-bg, rgba(255, 255, 255, 0.85));
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.2));
  border-radius: 16px;
}

.custom-roles-table {
  background: transparent;
}

.table-header-row th {
  background: rgba(0, 0, 0, 0.02);
  color: var(--text-secondary, #6c757d);
  border-bottom: 2px solid var(--glass-border, rgba(0, 0, 0, 0.08));
  letter-spacing: 0.5px;
}

.role-row {
  transition: background-color 0.15s ease;
}

.role-row:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.action-buttons-group {
  white-space: nowrap;
}

.action-btn {
  height: 36px;
  min-height: 36px;
  padding: 0 12px;
  font-size: 0.85rem;
  font-weight: 500;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.action-btn i {
  font-size: 0.95rem;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.user-count-btn {
  transition: all 0.2s ease;
  font-size: 0.85rem;
}

.user-count-btn:hover {
  background-color: var(--bs-primary, #0d6efd);
  color: #fff;
  border-color: var(--bs-primary, #0d6efd);
}

.custom-modal-backdrop {
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(4px);
  z-index: 1055;
}

.permission-item-card {
  transition: all 0.2s ease;
}

.permission-item-card:hover {
  border-color: var(--bs-primary, #0d6efd) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.user-card {
  transition: all 0.2s ease;
}

.user-card:hover {
  border-color: var(--bs-primary, #0d6efd) !important;
}

/* RTL fine-tuning */
[dir="rtl"] .me-1 {
  margin-left: 0.25rem !important;
  margin-right: 0 !important;
}

[dir="rtl"] .me-2 {
  margin-left: 0.5rem !important;
  margin-right: 0 !important;
}

[dir="rtl"] .ms-1 {
  margin-right: 0.25rem !important;
  margin-left: 0 !important;
}

/* Search Box Component Styles */
.search-box-wrapper {
  max-width: 340px;
  width: 100%;
}

.search-box {
  background: var(--input-bg, rgba(255, 255, 255, 0.05));
  border: 1px solid var(--glass-border, rgba(0, 0, 0, 0.15));
  border-radius: 10px;
  padding: 0 12px;
  height: 38px;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.search-box:focus-within {
  border-color: var(--bs-primary, #0d6efd) !important;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.18);
  background: rgba(255, 255, 255, 0.1);
}

.search-icon {
  color: var(--text-secondary, #6c757d);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  flex-shrink: 0;
  line-height: 1;
}

.search-input {
  border: none !important;
  background: transparent !important;
  color: var(--text-primary, #212529) !important;
  font-size: 0.875rem;
  width: 100%;
  outline: none !important;
  box-shadow: none !important;
  padding: 0 !important;
  line-height: 1.5;
}

.search-input::placeholder {
  color: var(--text-secondary, #6c757d);
  opacity: 0.75;
}

.search-clear-btn {
  border: none;
  background: transparent;
  color: var(--text-secondary, #6c757d);
  padding: 0;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 0.85rem;
  transition: color 0.15s;
  line-height: 1;
}

.search-clear-btn:hover {
  color: var(--text-primary, #212529);
}

[dir="rtl"] .search-box {
  direction: rtl;
}

[dir="rtl"] .search-input {
  text-align: right;
  direction: rtl;
}

[dir="ltr"] .search-box {
  direction: ltr;
}

[dir="ltr"] .search-input {
  text-align: left;
  direction: ltr;
}
</style>
