<template>
  <div>
    <!-- Bulk Action & Create Button Header -->
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2 force-ltr">
      <div v-if="bulkActionRoute && bulkActions.length" class="d-flex align-items-center gap-2">
        <select v-model="selectedBulkAction" class="form-select w-auto">
          <option value="">{{ labels.selectAction }}</option>
          <option v-for="action in bulkActions" :key="action.value" :value="action.value">
            {{ action.label }}
          </option>
        </select>
        <button type="button" class="btn btn-danger" @click="applyBulkAction">
          {{ labels.apply }}
        </button>
      </div>
      <div v-else></div>

      <a v-if="createRoute" :href="createRoute" class="btn btn-primary">
        {{ createLabel }}
      </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card shadow-sm border mb-4 force-ltr">
      <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Global Search -->
        <div style="width: 280px;">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
            <input
              type="text"
              v-model="params.search"
              class="form-control"
              :placeholder="labels.search"
              @input="debounceFetch"
            />
          </div>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" @click="resetSearch">
          {{ labels.resetSearch }}
        </button>
      </div>
    </div>

    <!-- Datatable -->
    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
      <vue3-datatable
        ref="datatableRef"
        :rows="state.rows"
        :columns="columns"
        :loading="state.loading"
        :totalRows="state.totalRows"
        :isServerMode="true"
        :pageSize="params.pagesize"
        :page="params.current_page"
        :sortColumn="params.sort_column"
        :sortDirection="params.sort_direction"
        :paginationInfo="labels.paginationInfo"
        :noDataContent="labels.noDataContent"
        :sortable="true"
        :pagination="true"
        :showPageSize="true"
        :pageSizeOptions="[10, 20, 50, 100]"
        :showNumbersCount="5"
        :hasCheckbox="bulkActions.length > 0"
        @sort-change="onSortChange"
        @page-change="onPageChange"
        @page-size-change="onPageSizeChange"
        @row-select="onRowSelect"
        class="alt-pagination"
      >
        <template #firstArrow>{{ labels.first }}</template>
        <template #lastArrow>{{ labels.last }}</template>
        <template #previousArrow>{{ labels.prev }}</template>
        <template #nextArrow>{{ labels.next }}</template>

        <!-- Dynamic Column Formatting and Alignment Wrapper for ALL data columns -->
        <template v-for="col in dataColumns" :key="col.field" #[col.field]="data">
          <div :class="getCellAlignmentClass(col.renderType ? (col.renderType === 'array' ? getArrayValue(data.value, col.fieldPath, col.arrayKey) : getNestedValue(data.value, col.fieldPath)) : data.value[col.field])">
            <!-- Array mapping (e.g. Roles list) -->
            <span v-if="col.renderType === 'array'">
              {{ getArrayValue(data.value, col.fieldPath, col.arrayKey) }}
            </span>

            <!-- Nested property mapping (e.g. serviceBody.en_name) -->
            <span v-else-if="col.renderType === 'nested'">
              {{ getNestedValue(data.value, col.fieldPath) }}
            </span>

            <!-- Plain HTML or fallback string -->
            <span v-else>
              <a v-if="(col.field === 'location' || col.field === 'location_link') && data.value[col.field]" :href="data.value[col.field]" target="_blank" class="text-primary text-decoration-underline">
                {{ data.value[col.field] }}
              </a>
              <span v-else-if="col.field === 'status'">
                <span class="badge px-2 py-1" :class="getStatusBadgeClass(data.value[col.field])">
                  {{ data.value[col.field] }}
                </span>
              </span>
              <span v-else>
                {{ data.value[col.field] }}
              </span>
            </span>
          </div>
        </template>

        <!-- Default Actions Column -->
        <template #actions="data">
          <div class="d-flex justify-content-center gap-2">
            <button
              v-if="showRouteTemplate"
              type="button"
              class="btn btn-sm btn-outline-info"
              @click="goToUrl(getShowUrl(data.value.id))"
            >
              {{ labels.show }}
            </button>
            <button
              v-if="hasToggleVerificationButton"
              type="button"
              class="btn btn-sm"
              :class="data.value.email_verified_at ? 'btn-outline-warning' : 'btn-outline-success'"
              @click="toggleVerification(data.value.id)"
            >
              {{ data.value.email_verified_at ? labels.unverify : labels.verify }}
            </button>
            <button
              v-if="hasAgendasButton"
              type="button"
              class="btn btn-sm btn-outline-primary"
              @click="goToUrl(getAgendasUrl(data.value.id))"
            >
              {{ labels.agendas }}
            </button>
            <button
              v-if="editRouteTemplate"
              type="button"
              class="btn btn-sm btn-outline-info"
              @click="goToUrl(getEditUrl(data.value.id))"
            >
              {{ labels.edit }}
            </button>
            <button
              v-if="deleteRouteName || deleteRouteTemplate"
              type="button"
              class="btn btn-sm btn-outline-danger"
              @click="confirmDelete(data.value.id)"
            >
              {{ labels.delete }}
            </button>
          </div>
        </template>

      </vue3-datatable>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import axios from 'axios';

const props = defineProps({
  fetchUrl: {
    type: String,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  createRoute: {
    type: String,
    default: ''
  },
  createLabel: {
    type: String,
    default: 'Add New'
  },
  bulkActionRoute: {
    type: String,
    default: ''
  },
  bulkActions: {
    type: Array,
    default: () => []
  },
  bulkIdsName: {
    type: String,
    default: 'ids[]'
  },
  editRouteTemplate: {
    type: String,
    default: ''
  },
  showRouteTemplate: {
    type: String,
    default: ''
  },
  hasAgendasButton: {
    type: Boolean,
    default: false
  },
  hasToggleVerificationButton: {
    type: Boolean,
    default: false
  },
  deleteRouteName: {
    type: String,
    default: ''
  },
  deleteRouteTemplate: {
    type: String,
    default: ''
  }
});

const state = reactive({
  rows: [],
  totalRows: 0,
  loading: false
});

const params = reactive({
  current_page: 1,
  pagesize: 15,
  search: '',
  sort_column: 'id',
  sort_direction: 'desc'
});

const selectedBulkAction = ref('');
const selectedRowIds = ref([]);
const datatableRef = ref(null);
let debounceTimer = null;

// Find columns requiring custom render logic
const customColumns = computed(() => {
  return props.columns.filter(col => col.renderType);
});

// All columns excluding checkbox and action buttons
const dataColumns = computed(() => {
  return props.columns.filter(col => col.field !== 'checkbox' && col.field !== 'actions' && !col.hide);
});

const isAr = computed(() => {
  return document.documentElement.lang === 'ar';
});

const labels = computed(() => {
  if (isAr.value) {
    return {
      selectAction: 'اختر إجراءً',
      apply: 'تطبيق',
      search: 'بحث...',
      resetSearch: 'إعادة تعيين البحث',
      show: 'عرض',
      verify: 'تحقق',
      unverify: 'إلغاء التحقق',
      agendas: 'الأجندة',
      edit: 'تعديل',
      delete: 'حذف',
      paginationInfo: 'يظهر {0} إلى {1} من {2} المدخلات',
      noDataContent: 'لا توجد بيانات متاحة',
      confirmDelete: 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
      confirmBulk: 'هل أنت متأكد أنك تريد تطبيق هذا الإجراء على العناصر المحددة؟',
      noItems: 'لم يتم تحديد أي عناصر.',
      first: 'الأول',
      last: 'الأخير',
      prev: 'السابق',
      next: 'التالي'
    };
  }
  return {
    selectAction: 'Select Action',
    apply: 'Apply',
    search: 'Search...',
    resetSearch: 'Reset Search',
    show: 'Show',
    verify: 'Verify',
    unverify: 'Unverify',
    agendas: 'Agendas',
    edit: 'Edit',
    delete: 'Delete',
    paginationInfo: 'Showing {0} to {1} of {2} entries',
    noDataContent: 'No data available',
    confirmDelete: 'Are you sure you want to delete this item?',
    confirmBulk: 'Are you sure you want to apply this action to selected items?',
    noItems: 'No items selected.',
    first: 'First',
    last: 'Last',
    prev: 'Prev',
    next: 'Next'
  };
});


const getEditUrl = (id) => {
  console.log('GenericDataTable getEditUrl called for ID:', id, 'Template:', props.editRouteTemplate);
  return props.editRouteTemplate.replace('{id}', id);
};

const getShowUrl = (id) => {
  console.log('GenericDataTable getShowUrl called for ID:', id, 'Template:', props.showRouteTemplate);
  return props.showRouteTemplate.replace('{id}', id);
};

const goToUrl = (url) => {
  console.log('GenericDataTable goToUrl triggered:', url);
  window.location.href = url;
};

const getStatusBadgeClass = (value) => {
  if (!value) return 'bg-secondary';
  const val = String(value).trim().toLowerCase();
  
  if (val.includes('verified') || val.includes('مفعل') || val.includes('active') || val.includes('نشط') || val.includes('تم التحقق')) {
    return 'bg-success';
  }
  if (val.includes('unverified') || val.includes('غير مفعل') || val.includes('inactive') || val.includes('غير نشط') || val.includes('لم يتم التحقق')) {
    return 'bg-warning text-dark';
  }
  return 'bg-secondary';
};

const getAgendasUrl = (id) => {
  return `/serviceBody/${id}/agendas`;
};

const toggleVerification = (id) => {
  console.log('GenericDataTable toggleVerification called for ID:', id);
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/subscribers/${id}/toggle-verification`;
  
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_token';
    input.value = csrfToken;
    form.appendChild(input);
  }
  
  document.body.appendChild(form);
  form.submit();
};

const getNestedValue = (obj, path) => {
  if (!obj || !path) return '-';
  return path.split('.').reduce((acc, part) => acc && acc[part] !== undefined ? acc[part] : '-', obj);
};

const getArrayValue = (obj, path, key) => {
  const arr = getNestedValue(obj, path);
  if (!Array.isArray(arr)) return '-';
  return arr.map(item => item[key]).join(', ');
};

const confirmDelete = (id) => {
  if (confirm(labels.value.confirmDelete)) {
    const deleteUrl = props.deleteRouteTemplate
      ? props.deleteRouteTemplate.replace('{id}', id)
      : props.editRouteTemplate.replace('{id}', id).replace('/edit', '');
    
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
    tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    form.appendChild(tokenInput);

    document.body.appendChild(form);
    form.submit();
  }
};

const getCellAlignmentClass = (value) => {
  if (!value) return isAr.value ? 'ar-cell' : 'en-cell';
  const str = String(value);
  // Matches email, location, phone number, id, date/time format, or english texts
  if (/^[a-zA-Z0-9@._:\-\/\s]+$/.test(str.trim())) {
    return 'en-cell';
  }
  return 'ar-cell';
};

const applyBulkAction = () => {
  if (!selectedBulkAction.value) return;
  
  // Directly query the component selections to ensure select-all states are captured
  const selectedRows = datatableRef.value ? datatableRef.value.getSelectedRows() : [];
  const ids = selectedRows.map(row => row.id);
  
  if (ids.length === 0) {
    alert(labels.value.noItems);
    return;
  }
  if (confirm(labels.value.confirmBulk)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.bulkActionRoute;

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    form.appendChild(tokenInput);

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = selectedBulkAction.value;
    form.appendChild(actionInput);

    ids.forEach(id => {
      const idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = props.bulkIdsName;
      idInput.value = id;
      form.appendChild(idInput);
    });

    document.body.appendChild(form);
    form.submit();
  }
};

const onSortChange = (sortData) => {
  // Library emits sortChange with {field, direction, offset, limit}
  if (sortData && sortData.field) {
    params.sort_column    = sortData.field;
    params.sort_direction = sortData.direction || 'asc';
    params.current_page   = 1;
    fetchData();
  }
};

const onPageChange = (page) => {
  params.current_page = page;
  fetchData();
};

const onPageSizeChange = (pageSize) => {
  params.pagesize = pageSize;
  params.current_page = 1;
  fetchData();
};

const onRowSelect = (selectedRows) => {
  selectedRowIds.value = selectedRows.map(row => row.id);
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
        search: params.search
      }
    });
    state.rows = response.data.data;
    state.totalRows = response.data.total;
    selectedRowIds.value = []; // Reset selections on reload
  } catch (error) {
    console.error('Error fetching generic datatable data:', error);
  } finally {
    state.loading = false;
  }
};

const resetSearch = () => {
  params.search = '';
  params.current_page = 1;
  fetchData();
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.row-checkbox {
  cursor: pointer;
}
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
.force-ltr {
  direction: ltr !important;
}
.force-ltr select, .force-ltr input, .force-ltr button {
  direction: ltr !important;
}
/* Force LTR layout on datatable pagination footer controls */
.alt-pagination {
  border: 1px solid #000000 !important;
  padding: 12px !important;
  border-radius: 8px !important;
}
.alt-pagination .bh-pagination {
  direction: ltr !important;
}
.alt-pagination .bh-pagination-info {
  direction: ltr !important;
}
.alt-pagination .bh-page-size {
  direction: ltr !important;
}
/* If layout is Arabic, default all table cells to align right (RTL) */
html[lang="ar"] .alt-pagination td {
  text-align: right !important;
  direction: rtl !important;
}
/* If layout is English, default all table cells to align left (LTR) */
html[lang="en"] .alt-pagination td {
  text-align: left !important;
  direction: ltr !important;
}
/* Center all table headers and sorting indicators */
.alt-pagination th {
  text-align: center !important;
}
.alt-pagination th > div {
  justify-content: center !important;
}
.alt-pagination th .bh-content {
  justify-content: center !important;
}
</style>
