<template>
  <div>
    <!-- Advanced Filter Form -->
    <div class="card shadow-sm border mb-4">
      <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">
          <i class="bi bi-funnel-fill me-2"></i>Filters
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="filter_model" class="form-label small fw-bold text-secondary">Model</label>
            <select v-model="filters.filter_model" id="filter_model" class="form-select" @change="fetchData">
              <option value="">All Models</option>
              <option v-for="model in availableModels" :key="model" :value="model">
                {{ model }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="filter_operation" class="form-label small fw-bold text-secondary">Operation</label>
            <select v-model="filters.filter_operation" id="filter_operation" class="form-select" @change="fetchData">
              <option value="">All Operations</option>
              <option v-for="op in availableOperations" :key="op" :value="op">
                {{ op.charAt(0).toUpperCase() + op.slice(1) }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="search_user" class="form-label small fw-bold text-secondary">User</label>
            <input
              type="text"
              v-model="filters.search_user"
              id="search_user"
              class="form-control"
              placeholder="User name or email..."
              @input="debounceFetch"
            />
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-secondary">Date Range</label>
            <div class="input-group">
              <input type="date" v-model="filters.date_from" id="date_from" class="form-control" @change="fetchData" />
              <span class="input-group-text">to</span>
              <input type="date" v-model="filters.date_to" id="date_to" class="form-control" @change="fetchData" />
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <!-- Global Search -->
          <div style="width: 250px;">
            <input
              type="text"
              v-model="params.search"
              class="form-control form-control-sm"
              placeholder="Search all fields..."
              @input="debounceFetch"
            />
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm" @click="resetFilters">
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Datatable -->
    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
      <vue3-datatable
        :rows="state.rows"
        :columns="columns"
        :loading="state.loading"
        :totalRows="state.totalRows"
        :isServerMode="true"
        :pageSize="params.pagesize"
        :page="params.current_page"
        :sortColumn="params.sort_column"
        :sortDirection="params.sort_direction"
        @change="changeServer"
        @changeServer="changeServer"
        class="alt-pagination"
      >
        <!-- Operation Slot -->
        <template #operation="data">
          <span :class="['badge', getBadgeClass(data.value.operation), 'px-2', 'py-1']">
            {{ data.value.operation.charAt(0).toUpperCase() + data.value.operation.slice(1) }}
          </span>
        </template>

        <!-- Model Slot -->
        <template #model="data">
          <strong class="text-primary">{{ data.value.model }}</strong>
        </template>

        <!-- User Slot -->
        <template #user="data">
          <div v-if="data.value.user" class="text-start small">
            <strong>{{ data.value.user.name }}</strong><br />
            <span class="text-muted">{{ data.value.user.email }}</span>
          </div>
          <span v-else class="text-muted italic">System / Guest</span>
        </template>

        <!-- Date Slot -->
        <template #date="data">
          {{ formatDate(data.value.created_at) }}
        </template>

        <!-- Time Slot -->
        <template #time="data">
          {{ formatTime(data.value.created_at) }}
        </template>

        <!-- Context Slot -->
        <template #context="data">
          <span v-if="data.value.model === 'Meeting'" class="small text-secondary">
            {{ data.value.group_name || 'N/A' }}
          </span>
          <span v-else-if="data.value.model === 'Group'" class="small text-secondary">
            {{ data.value.group_name || 'N/A' }}
          </span>
          <span v-else class="text-muted small">N/A</span>
        </template>

        <!-- Actions Slot -->
        <template #actions="data">
          <button
            type="button"
            :class="[
              'btn',
              'btn-sm',
              isExpanded(data.value.id) ? 'btn-secondary text-white' : 'btn-outline-primary',
              'toggle-details'
            ]"
            @click="toggleRow(data.value.id)"
          >
            {{ isExpanded(data.value.id) ? 'Hide' : 'Show' }}
          </button>
        </template>
      </vue3-datatable>
    </div>

    <!-- Custom Collapsible Detail Rows rendered outside Vue3-Datatable to match layout styling -->
    <div v-for="row in state.rows" :key="'details-' + row.id">
      <div v-if="isExpanded(row.id)" class="p-3 text-start bg-light rounded border mt-2 mb-3">
        <!-- Context Badges -->
        <div class="row g-2 mb-3">
          <div class="col-md-4">
            <div class="p-2 border rounded bg-white small text-truncate">
              <strong>IP Address:</strong> <code>{{ row.ip_address || 'N/A' }}</code>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-2 border rounded bg-white small text-truncate" :title="row.url">
              <strong>URL:</strong> <code>{{ row.url || 'N/A' }}</code>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-2 border rounded bg-white small text-truncate" :title="row.user_agent">
              <strong>User Agent:</strong> <span class="text-muted">{{ row.user_agent || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Values Table -->
        <div class="card border shadow-sm">
          <div class="card-header bg-white py-2 fw-bold text-secondary small">
            <i class="bi bi-list-task me-1"></i>Database Changes
          </div>
          <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0 align-middle small text-center">
              <thead>
                <tr class="table-light text-secondary">
                  <th style="width: 25%;">Field</th>
                  <th v-if="row.operation === 'update'" style="width: 37.5%;">Original Value</th>
                  <th v-if="row.operation === 'update'" style="width: 37.5%;">New Value</th>
                  <th v-else>Value</th>
                </tr>
              </thead>
              <tbody>
                <template v-if="row.operation === 'create' && row.new_values">
                  <tr v-for="(val, key) in row.new_values" :key="key">
                    <td><strong>{{ key }}</strong></td>
                    <td class="text-success">
                      <span class="badge bg-success-light text-success me-1">+</span>
                      <code>{{ formatValue(val) }}</code>
                    </td>
                  </tr>
                </template>

                <template v-else-if="row.operation === 'delete' && row.old_values">
                  <tr v-for="(val, key) in row.old_values" :key="key">
                    <td><strong>{{ key }}</strong></td>
                    <td class="text-danger text-decoration-line-through">
                      <span class="badge bg-danger-light text-danger me-1">-</span>
                      <code>{{ formatValue(val) }}</code>
                    </td>
                  </tr>
                </template>

                <template v-else-if="row.operation === 'update' && (row.old_values || row.new_values)">
                  <tr v-for="(newVal, key) in row.new_values" :key="key">
                    <td><strong>{{ key }}</strong></td>
                    <td class="text-danger text-decoration-line-through">
                      <code>{{ formatValue(row.old_values ? row.old_values[key] : '') }}</code>
                    </td>
                    <td class="text-success fw-bold">
                      <code>{{ formatValue(newVal) }}</code>
                    </td>
                  </tr>
                </template>

                <template v-else>
                  <template v-if="row.details">
                    <tr v-for="(val, key) in row.details" :key="key">
                      <td><strong>{{ key }}</strong></td>
                      <td>
                        <code>{{ formatValue(val) }}</code>
                      </td>
                    </tr>
                  </template>
                  <tr v-else>
                    <td :colspan="row.operation === 'update' ? 3 : 2" class="text-center py-2 text-muted">
                      No details logged.
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import axios from 'axios';

// Props
const props = defineProps({
  fetchUrl: {
    type: String,
    required: true
  },
  availableModels: {
    type: Array,
    default: () => []
  },
  availableOperations: {
    type: Array,
    default: () => []
  }
});

// Columns config
const columns = ref([
  { field: 'operation', title: 'Operation', sort: true },
  { field: 'model', title: 'Model', sort: true },
  { field: 'user', title: 'User', sort: false },
  { field: 'date', title: 'Date', sort: false },
  { field: 'time', title: 'Time', sort: false },
  { field: 'context', title: 'Context', sort: false },
  { field: 'actions', title: 'Action', sort: false, headerClass: 'text-center', cellClass: 'text-center' }
]);

// Table reactive state
const state = reactive({
  rows: [],
  totalRows: 0,
  loading: false
});

// Consolidated local table parameters
const params = reactive({
  current_page: 1,
  pagesize: 15,
  search: '',
  sort_column: 'created_at',
  sort_direction: 'desc'
});

// Filters
const filters = reactive({
  filter_model: '',
  filter_operation: '',
  search_user: '',
  date_from: '',
  date_to: ''
});

// Expanded row details tracking
const expandedRows = ref([]);

// Debounce timer
let debounceTimer = null;

const getBadgeClass = (op) => {
  if (op === 'create') return 'bg-success';
  if (op === 'update') return 'bg-info text-dark';
  if (op === 'delete') return 'bg-danger';
  return 'bg-secondary';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toISOString().slice(0, 10);
};

const formatTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toTimeString().slice(0, 8);
};

const formatValue = (val) => {
  if (val === null || val === undefined) return '';
  if (typeof val === 'object') return JSON.stringify(val);
  return val;
};

const isExpanded = (id) => expandedRows.value.includes(id);

const toggleRow = (id) => {
  if (expandedRows.value.includes(id)) {
    expandedRows.value = expandedRows.value.filter(rowId => rowId !== id);
  } else {
    expandedRows.value.push(id);
  }
};

const changeServer = (newParams) => {
  params.current_page = newParams.current_page || newParams.page || params.current_page;
  params.pagesize = newParams.pagesize || newParams.pageSize || params.pagesize;
  params.sort_column = newParams.sort_column === 'date' || newParams.sort_column === 'time' ? 'created_at' : (newParams.sort_column || params.sort_column);
  params.sort_direction = newParams.sort_direction || params.sort_direction;
  fetchData();
};

const debounceFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    params.current_page = 1;
    fetchData();
  }, 300);
};

const fetchData = async () => {
  state.loading = true;
  try {
    const response = await axios.get(props.fetchUrl, {
      params: {
        page: params.current_page,
        pagesize: params.pagesize,
        sort_column: params.sort_column,
        sort_direction: params.sort_direction,
        search: params.search,
        ...filters
      }
    });
    // Map Laravel response structure
    state.rows = response.data.data;
    state.totalRows = response.data.total;
  } catch (error) {
    console.error('Error fetching transactions:', error);
  } finally {
    state.loading = false;
  }
};

const resetFilters = () => {
  filters.filter_model = '';
  filters.filter_operation = '';
  filters.search_user = '';
  filters.date_from = '';
  filters.date_to = '';
  params.search = '';
  params.current_page = 1;
  fetchData();
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>

.bg-success-light {
  background-color: rgba(40, 167, 69, 0.1);
}
.bg-danger-light {
  background-color: rgba(220, 53, 69, 0.1);
}
.text-decoration-line-through {
  text-decoration: line-through;
}
.toggle-details {
  transition: all 0.2s ease-in-out;
}
</style>
