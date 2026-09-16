<template>
  <div class="helpline-dashboard-container" dir="rtl">
    <!-- Top Period & Quick Actions Toolbar -->
    <div class="card border-0 shadow-sm glass-card mb-4">
      <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="period-icon-badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
              <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <div>
              <div class="text-muted small fw-medium">دورة التقرير المحددة (تنتهي بأول ثلاثاء شهرياً)</div>
              <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <span>{{ currentPeriod.label }}</span>
                <span v-if="loading" class="spinner-border spinner-border-sm text-primary" role="status"></span>
              </h5>
            </div>
          </div>

          <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Cycle Selector Dropdown -->
            <!-- Cycle Selector Dropdown / Badge -->
            <div class="d-flex align-items-center gap-2">
              <select 
                v-if="Object.keys(cycles).length > 1"
                v-model="selectedCycleKey" 
                class="form-select form-select-sm rounded-pill border shadow-sm px-3" 
                style="min-width: 200px;"
                @change="onCycleChange"
              >
                <option v-for="(c, key) in cycles" :key="key" :value="key">
                  {{ c.label }}
                </option>
              </select>

              <span v-else class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fs-6">
                <i class="bi bi-calendar2-check me-1"></i> {{ currentPeriod.label }}
              </span>

              <!-- All Time Button (Only shown if multiple cycles exist or currently active) -->
              <button 
                v-if="Object.keys(cycles).length > 1 || selectedPeriodType === 'all_time'"
                type="button" 
                class="btn btn-sm rounded-pill px-3 shadow-sm"
                :class="selectedPeriodType === 'all_time' ? 'btn-primary' : 'btn-outline-secondary'"
                @click="setAllTime"
              >
                كافة المكالمات
              </button>
            </div>

            <!-- Export Actions -->
            <div class="btn-group">
              <a 
                :href="exportExcelUrl" 
                class="btn btn-sm btn-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                style="background-color: #059669; border-color: #059669;"
              >
                <i class="bi bi-file-earmark-excel-fill"></i>
                <span>تصدير إكسل</span>
              </a>
              <a 
                v-if="exportPdfRoute" 
                :href="exportPdfUrl" 
                target="_blank" 
                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1 ms-1"
              >
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <span>تقرير PDF</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top KPI Summary Cards -->
    <div class="row g-3 mb-4">
      <!-- Total Calls -->
      <div class="col-6 col-md-3">
        <div 
          class="card border-0 shadow-sm kpi-card h-100 cursor-pointer"
          :class="{ 'border-primary-active': activeFilterCount === 0 }"
          @click="resetFilters"
          title="عرض كل مكالمات الفترة"
        >
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
              <i class="bi bi-telephone-inbound-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">إجمالي المكالمات</div>
              <div class="kpi-value fs-3 fw-bold text-dark">{{ metrics.total_calls }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Duration Breakdown -->
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm kpi-card h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted small fw-medium">توزيع مدة المكالمات</span>
              <i class="bi bi-stopwatch text-info fs-5"></i>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
              <button 
                type="button" 
                class="btn btn-xs rounded-pill px-2 py-1 flex-grow-1 text-center"
                :class="filters.duration === 'less_than_5' ? 'btn-success' : 'btn-outline-success'"
                @click="toggleDurationFilter('less_than_5')"
              >
                &lt; 5 د ({{ metrics.duration_less_than_5 }})
              </button>
              <button 
                type="button" 
                class="btn btn-xs rounded-pill px-2 py-1 flex-grow-1 text-center"
                :class="filters.duration === 'more_than_5' ? 'btn-info' : 'btn-outline-info'"
                @click="toggleDurationFilter('more_than_5')"
              >
                &gt; 5 د ({{ metrics.duration_more_than_5 }})
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 12 Calls -->
      <div class="col-6 col-md-3">
        <div 
          class="card border-0 shadow-sm kpi-card h-100 cursor-pointer"
          :class="{ 'border-warning-active': filters.step12 === '1' }"
          @click="toggleStep12Filter"
        >
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis">
              <i class="bi bi-arrow-repeat fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">تحويل خطوة 12</div>
              <div class="kpi-value fs-3 fw-bold text-dark d-flex align-items-baseline gap-2">
                <span>{{ metrics.step_12_calls }}</span>
                <span class="text-muted small fs-6">({{ step12Percent }}%)</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Meeting Discussion Flag -->
      <div class="col-6 col-md-3">
        <div 
          class="card border-0 shadow-sm kpi-card h-100 cursor-pointer"
          :class="{ 'border-danger-active': filters.discuss === '1' }"
          @click="toggleDiscussFilter"
        >
          <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon-wrapper rounded-3 p-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger">
              <i class="bi bi-chat-heart-fill fs-3"></i>
            </div>
            <div>
              <div class="kpi-label text-muted small fw-medium">للمناقشة بالاجتماع</div>
              <div class="kpi-value fs-3 fw-bold text-dark">{{ metrics.discuss_in_meeting_calls }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Visual Analytics & Interactive Progress Cards -->
    <div class="row g-3 mb-4">
      <!-- Shifts Visual Breakdown -->
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm glass-card h-100">
          <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <i class="bi bi-clock-history text-primary"></i>
              <span>توزيع الورديات / مواعيد الاتصال</span>
            </h6>
            <span class="badge bg-light text-muted border">انقر للفلترة</span>
          </div>
          <div class="card-body px-4 py-3">
            <div class="d-flex flex-column gap-2">
              <div 
                v-for="(count, shiftName) in metrics.shift_counts" 
                :key="shiftName"
                class="shift-progress-item p-2 rounded-3 cursor-pointer transition-all"
                :class="{ 'bg-primary-subtle border border-primary': filters.shift === shiftName }"
                @click="toggleShiftFilter(shiftName)"
              >
                <div class="d-flex justify-content-between align-items-center small mb-1">
                  <span class="fw-semibold text-dark" dir="ltr">{{ shiftName }}</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-white text-dark border px-2">{{ count }} مكالمة</span>
                    <span class="text-muted small" style="width: 38px; text-align: left;">{{ calcPercent(count) }}%</span>
                  </div>
                </div>
                <div class="progress" style="height: 6px;">
                  <div 
                    class="progress-bar bg-primary" 
                    role="progressbar" 
                    :style="{ width: calcPercent(count) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Caller Types & Sources Breakdown -->
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm glass-card h-100">
          <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <i class="bi bi-people text-success"></i>
              <span>فئات المتصلين ومصادر المعرفة</span>
            </h6>
            <span class="badge bg-light text-muted border">انقر للفلترة</span>
          </div>
          <div class="card-body px-4 py-3">
            <div class="mb-3">
              <div class="text-muted small mb-2 fw-medium">أعلى فئات المتصلين:</div>
              <div class="d-flex flex-wrap gap-1">
                <button 
                  v-for="(count, cType) in topCallerTypes" 
                  :key="cType"
                  type="button" 
                  class="btn btn-xs rounded-pill d-flex align-items-center gap-1 px-2 py-1 shadow-none"
                  :class="filters.caller_type === cType ? 'btn-primary' : 'btn-light border'"
                  @click="toggleCallerTypeFilter(cType)"
                >
                  <span>{{ cType }}</span>
                  <span class="badge rounded-pill bg-white text-dark">{{ count }}</span>
                </button>
              </div>
            </div>

            <hr class="my-2 opacity-25">

            <div>
              <div class="text-muted small mb-2 fw-medium">متطوعو الرد في هذه الدورة:</div>
              <div class="d-flex flex-wrap gap-1" style="max-height: 120px; overflow-y: auto;">
                <button 
                  v-for="(count, vName) in metrics.volunteer_counts" 
                  :key="vName"
                  type="button" 
                  class="btn btn-xs rounded-pill d-flex align-items-center gap-1 px-2 py-1 shadow-none"
                  :class="filters.volunteer === vName ? 'btn-dark' : 'btn-outline-secondary'"
                  @click="toggleVolunteerFilter(vName)"
                >
                  <i class="bi bi-person-circle"></i>
                  <span>{{ vName }}</span>
                  <span class="badge rounded-pill bg-secondary text-white">{{ count }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Calls Log Data Card -->
    <div class="card border-0 shadow-sm glass-card">
      <!-- Table Header & Controls Bar -->
      <div class="card-header bg-transparent border-0 pt-4 px-4 pb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <i class="bi bi-list-columns-reverse text-primary"></i>
              <span>سجل استجابات المكالمات</span>
            </h5>
            <span class="badge rounded-pill bg-primary px-3 py-1 fs-6">{{ pagination.total }}</span>
          </div>

          <!-- Multi-Select Bulk Actions -->
          <div v-if="selectedIds.length > 0" class="d-flex align-items-center gap-2 bg-primary-subtle border border-primary-subtle rounded-pill px-3 py-1">
            <span class="small text-primary fw-bold">تم تحديد {{ selectedIds.length }} مكالمة</span>
            <button 
              type="button" 
              class="btn btn-xs btn-success rounded-pill px-2 py-1 d-flex align-items-center gap-1"
              @click="exportSelectedExcel"
            >
              <i class="bi bi-download"></i> تصدير المحدد
            </button>
            <button 
              type="button" 
              class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1"
              @click="clearSelection"
            >
              إلغاء
            </button>
          </div>

          <!-- Filter & Search Controls -->
          <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search Box -->
            <div class="input-group input-group-sm shadow-sm rounded-pill border bg-white overflow-hidden" style="width: 260px;">
              <span class="input-group-text bg-transparent border-0 pe-3 ps-1 text-muted">
                <i class="bi bi-search"></i>
              </span>
              <input 
                type="text" 
                v-model="searchQuery" 
                class="form-control border-0 bg-transparent text-end"
                placeholder="بحث في الملخص أو المتطوع..." 
                @input="onSearchInput"
                style="box-shadow: none; font-size: 0.85rem;"
              >
              <button 
                v-if="searchQuery" 
                type="button" 
                class="btn border-0 bg-transparent text-muted px-2"
                @click="clearSearch"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>

            <!-- Page Size -->
            <select v-model="pagination.per_page" class="form-select form-select-sm rounded-pill border" style="width: 90px;" @change="fetchData(1)">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>

            <!-- Reset Filters -->
            <button 
              v-if="activeFilterCount > 0" 
              type="button" 
              class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm"
              @click="resetFilters"
              title="مسح جميع الفلاتر"
            >
              <i class="bi bi-arrow-counterclockwise"></i>
              <span>مسح الفلاتر ({{ activeFilterCount }})</span>
            </button>
          </div>
        </div>

        <!-- Active Filters Bar -->
        <div v-if="activeFilterCount > 0" class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top">
          <span class="text-muted small fw-medium">الفلاتر المطبقة:</span>
          
          <span v-if="filters.duration" class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>المدة: {{ filters.duration === 'less_than_5' ? 'أقل من 5 د' : 'أكثر من 5 د' }}</span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.duration = ''; fetchData(1)"></i>
          </span>

          <span v-if="filters.shift" class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>الوردية: <span dir="ltr" class="d-inline-block">{{ filters.shift }}</span></span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.shift = ''; fetchData(1)"></i>
          </span>

          <span v-if="filters.caller_type" class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>فئة المتصل: {{ filters.caller_type }}</span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.caller_type = ''; fetchData(1)"></i>
          </span>

          <span v-if="filters.volunteer" class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>المتطوع: {{ filters.volunteer }}</span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.volunteer = ''; fetchData(1)"></i>
          </span>

          <span v-if="filters.step12 === '1'" class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>تحويل خطوة 12 فقط</span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.step12 = ''; fetchData(1)"></i>
          </span>

          <span v-if="filters.discuss === '1'" class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 d-flex align-items-center gap-1">
            <span>للمناقشة بالاجتماع فقط</span>
            <i class="bi bi-x cursor-pointer fs-6" @click="filters.discuss = ''; fetchData(1)"></i>
          </span>
        </div>
      </div>

      <!-- Table Body -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 text-center" style="width: 100%;">
            <thead class="table-light">
              <tr>
                <th style="width: 40px;">
                  <input 
                    type="checkbox" 
                    class="form-check-input" 
                    :checked="isAllSelected" 
                    @change="toggleSelectAll" 
                    title="تحديد كل الصفحة"
                  >
                </th>
                <th style="width: 60px;">#</th>
                <th style="width: 150px;">التاريخ والتوقيت</th>
                <th style="width: 130px;">الوردية</th>
                <th>المتطوع</th>
                <th>فئة المتصل</th>
                <th style="width: 110px;">المدة</th>
                <th style="width: 90px;">خطوة 12</th>
                <th style="width: 90px;">اجتماع</th>
                <th class="text-start" style="min-width: 250px;">ملخص المكالمة</th>
                <th style="width: 130px;">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="call in calls" :key="call.id">
                <tr 
                  class="call-row cursor-pointer"
                  :class="{ 'table-active': expandedRows.includes(call.id) }"
                  @click="toggleRowExpand(call.id)"
                >
                  <td @click.stop>
                    <input 
                      type="checkbox" 
                      class="form-check-input" 
                      :value="call.id" 
                      v-model="selectedIds"
                    >
                  </td>
                  <td class="text-muted fw-bold">
                    <i 
                      class="bi me-1 transition-transform" 
                      :class="expandedRows.includes(call.id) ? 'bi-chevron-down text-primary' : 'bi-chevron-left text-muted'"
                    ></i>
                    {{ call.id }}
                  </td>
                  <td class="small text-nowrap">
                    <div class="fw-semibold text-dark">{{ formatDate(call.call_date) }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;" dir="ltr">{{ formatTime(call.entry_time) }}</div>
                  </td>
                  <td class="small text-nowrap">
                    <span class="badge rounded-pill bg-light text-dark border px-2 py-1" dir="ltr">{{ call.call_time_shift }}</span>
                  </td>
                  <td class="fw-bold small text-nowrap">
                    <i class="bi bi-person-circle text-primary me-1"></i>
                    {{ call.effective_volunteer_name }}
                  </td>
                  <td class="small text-nowrap">
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2 py-1">{{ call.effective_caller_type }}</span>
                  </td>
                  <td>
                    <span 
                      class="badge rounded-pill px-2 py-1"
                      :class="call.duration === 'less_than_5' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-info-subtle text-info border border-info-subtle'"
                    >
                      {{ call.duration === 'less_than_5' ? '< 5 د' : '> 5 د' }}
                    </span>
                  </td>
                  <td>
                    <span v-if="call.is_step_12" class="badge rounded-pill bg-warning text-dark px-2 py-1">
                      <i class="bi bi-check-circle-fill me-1"></i> نعم
                    </span>
                    <span v-else class="text-muted small">لا</span>
                  </td>
                  <td>
                    <button 
                      type="button" 
                      class="btn btn-xs rounded-pill px-2 py-1"
                      :class="call.discuss_in_meeting ? 'btn-danger' : 'btn-outline-secondary'"
                      @click.stop="toggleDiscuss(call)"
                      :title="call.discuss_in_meeting ? 'معلمة للمناقشة في الاجتماع (انقر للإلغاء)' : 'انقر للتعليم للمناقشة بالاجتماع'"
                    >
                      <i class="bi" :class="call.discuss_in_meeting ? 'bi-chat-heart-fill' : 'bi-chat'"></i>
                    </button>
                  </td>
                  <td class="text-start">
                    <div class="text-truncate small" style="max-width: 320px;" :title="call.call_brief">
                      {{ call.call_brief }}
                    </div>
                  </td>
                  <td @click.stop>
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <!-- Copy formatted brief to WhatsApp -->
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline-success rounded-pill px-2 py-1"
                        @click="copyCallToClipboard(call)"
                        title="نسخ ملخص المكالمة للواتساب"
                      >
                        <i class="bi bi-whatsapp"></i>
                      </button>

                      <!-- Delete Call -->
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1"
                        @click="confirmDelete(call)"
                        title="حذف السجل"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Expandable Row Accordion Detail Card -->
                <tr v-if="expandedRows.includes(call.id)" :key="'exp-' + call.id" class="accordion-detail-row bg-light">
                  <td colspan="11" class="p-3 text-start">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                      <div class="row g-3">
                        <div class="col-12 col-md-8">
                          <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-card-text"></i>
                            <span>نبذة وملخص المكالمة:</span>
                          </h6>
                          <div class="p-3 rounded-3 bg-light text-dark mb-3" style="line-height: 1.7; white-space: pre-line;">
                            {{ call.call_brief }}
                          </div>

                          <div v-if="call.additional_info">
                            <h6 class="fw-bold text-secondary small mb-1">
                              <i class="bi bi-info-circle me-1"></i> معلومات وملاحظات إضافية:
                            </h6>
                            <div class="p-2 rounded bg-light small text-muted">
                              {{ call.additional_info }}
                            </div>
                          </div>
                        </div>

                        <div class="col-12 col-md-4 border-end">
                          <h6 class="fw-bold text-dark small mb-3">بيانات المكالمة بالتفصيل:</h6>
                          <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                            <li><strong>المتطوع:</strong> {{ call.effective_volunteer_name }}</li>
                            <li><strong>تاريخ المكالمة:</strong> {{ formatDate(call.call_date) }}</li>
                            <li><strong>وقت التسجيل الفعلي:</strong> {{ call.entry_time }}</li>
                            <li><strong>الوردية:</strong> <span dir="ltr" class="d-inline-block">{{ call.call_time_shift }}</span></li>
                            <li><strong>فئة المتصل:</strong> {{ call.effective_caller_type }}</li>
                            <li><strong>مصدر المعرفة:</strong> {{ call.effective_referral_source }}</li>
                            <li><strong>المدة:</strong> {{ call.duration_label }}</li>
                            <li>
                              <strong>خطوة 12:</strong>
                              <span :class="call.is_step_12 ? 'text-warning fw-bold' : 'text-muted'">{{ call.is_step_12 ? 'نعم (تم التحويل)' : 'لا' }}</span>
                            </li>
                            <li>
                              <strong>مناقشة بالاجتماع:</strong>
                              <span :class="call.discuss_in_meeting ? 'text-danger fw-bold' : 'text-muted'">{{ call.discuss_in_meeting ? 'نعم' : 'لا' }}</span>
                            </li>
                          </ul>

                          <div class="mt-3 pt-3 border-top d-flex gap-2">
                            <button 
                              type="button" 
                              class="btn btn-sm btn-outline-success w-100 rounded-pill d-flex align-items-center justify-content-center gap-1"
                              @click="copyCallToClipboard(call)"
                            >
                              <i class="bi bi-clipboard-check"></i> نسخ للواتساب
                            </button>
                            <button 
                              type="button" 
                              class="btn btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1"
                              :class="call.discuss_in_meeting ? 'btn-danger' : 'btn-outline-danger'"
                              @click="toggleDiscuss(call)"
                            >
                              <i class="bi bi-chat-heart"></i> {{ call.discuss_in_meeting ? 'إلغاء الاجتماع' : 'مناقشة بالاجتماع' }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>

              <!-- Empty State -->
              <tr v-if="calls.length === 0 && !loading">
                <td colspan="11" class="py-5 text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                  لا توجد مكالمات مسجلة مطابقة لمعايير البحث في هذه الدورة.
                </td>
              </tr>

              <!-- Loading Skeleton / Shimmer -->
              <tr v-if="loading">
                <td colspan="11" class="py-4 text-center text-primary">
                  <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                  <span>جاري تحديث البيانات...</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Table Footer & Pagination -->
      <div class="card-footer bg-transparent border-top py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="text-muted small">
          عرض <span class="fw-semibold">{{ paginationStart }}</span> إلى <span class="fw-semibold">{{ paginationEnd }}</span> من أصل <span class="fw-semibold">{{ pagination.total }}</span> مكالمة
        </div>

        <div class="d-flex align-items-center gap-2">
          <button 
            type="button" 
            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
            :disabled="pagination.current_page <= 1 || loading"
            @click="fetchData(pagination.current_page - 1)"
          >
            السابق
          </button>

          <span class="px-2 small fw-semibold text-muted">
            {{ pagination.current_page }} / {{ pagination.last_page || 1 }}
          </span>

          <button 
            type="button" 
            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
            :disabled="pagination.current_page >= pagination.last_page || loading"
            @click="fetchData(pagination.current_page + 1)"
          >
            التالي
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({})
  },
  initialMetrics: {
    type: Object,
    default: () => ({})
  },
  initialCycles: {
    type: Object,
    default: () => ({})
  },
  currentCycleKey: {
    type: String,
    default: ''
  },
  periodLabel: {
    type: String,
    default: ''
  },
  fetchUrl: {
    type: String,
    default: '/helpline/data'
  },
  exportExcelRoute: {
    type: String,
    default: '/helpline/export/excel'
  },
  exportPdfRoute: {
    type: String,
    default: '/helpline/export/pdf'
  },
  csrfToken: {
    type: String,
    default: ''
  }
});

// State
const loading = ref(false);
const calls = ref(props.initialData.data || []);
const metrics = ref(props.initialMetrics || {});
const cycles = ref(props.initialCycles || {});
const selectedCycleKey = ref(props.currentCycleKey);
const selectedPeriodType = ref('cycle');
const currentPeriod = ref({
  label: props.periodLabel || 'الدورة الحالية',
  key: props.currentCycleKey
});

const pagination = ref({
  current_page: props.initialData.current_page || 1,
  last_page: props.initialData.last_page || 1,
  per_page: props.initialData.per_page || 20,
  total: props.initialData.total || 0,
});

const filters = ref({
  duration: '',
  shift: '',
  caller_type: '',
  volunteer: '',
  step12: '',
  discuss: '',
});

const searchQuery = ref('');
let searchTimeout = null;

const expandedRows = ref([]);
const selectedIds = ref([]);

// Computed
const paginationStart = computed(() => {
  if (pagination.value.total === 0) return 0;
  return (pagination.value.current_page - 1) * pagination.value.per_page + 1;
});

const paginationEnd = computed(() => {
  const end = pagination.value.current_page * pagination.value.per_page;
  return end > pagination.value.total ? pagination.value.total : end;
});

const step12Percent = computed(() => {
  if (!metrics.value.total_calls) return 0;
  return Math.round((metrics.value.step_12_calls / metrics.value.total_calls) * 100);
});

const activeFilterCount = computed(() => {
  let count = 0;
  if (filters.value.duration) count++;
  if (filters.value.shift) count++;
  if (filters.value.caller_type) count++;
  if (filters.value.volunteer) count++;
  if (filters.value.step12) count++;
  if (filters.value.discuss) count++;
  if (searchQuery.value) count++;
  return count;
});

const isAllSelected = computed(() => {
  if (calls.value.length === 0) return false;
  return calls.value.every(c => selectedIds.value.includes(c.id));
});

const topCallerTypes = computed(() => {
  return metrics.value.caller_type_counts || {};
});

const exportExcelUrl = computed(() => {
  let url = `${props.exportExcelRoute}?period_type=${selectedPeriodType.value}&cycle=${selectedCycleKey.value}`;
  return url;
});

const exportPdfUrl = computed(() => {
  return `${props.exportPdfRoute}?cycle=${selectedCycleKey.value}`;
});

// Methods
const calcPercent = (count) => {
  if (!metrics.value.total_calls) return 0;
  return Math.round((count / metrics.value.total_calls) * 100);
};

const formatDate = (d) => {
  if (!d) return '-';
  if (typeof d === 'string' && d.length >= 10) {
    return d.substring(0, 10);
  }
  return d;
};

const formatTime = (t) => {
  if (!t) return '-';
  try {
    const date = new Date(t);
    return date.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
  } catch (e) {
    return t;
  }
};

const fetchData = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    params.append('per_page', pagination.value.per_page);
    params.append('period_type', selectedPeriodType.value);
    params.append('cycle', selectedCycleKey.value);

    if (searchQuery.value) params.append('search', searchQuery.value);
    if (filters.value.duration) params.append('filter_duration', filters.value.duration);
    if (filters.value.shift) params.append('filter_shift', filters.value.shift);
    if (filters.value.caller_type) params.append('filter_caller_type', filters.value.caller_type);
    if (filters.value.volunteer) params.append('filter_volunteer', filters.value.volunteer);
    if (filters.value.step12) params.append('filter_step12', filters.value.step12);
    if (filters.value.discuss) params.append('filter_discuss', filters.value.discuss);

    const response = await fetch(`${props.fetchUrl}?${params.toString()}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!response.ok) throw new Error('Network error');

    const result = await response.json();
    calls.value = result.calls.data || [];
    pagination.value.current_page = result.calls.current_page;
    pagination.value.last_page = result.calls.last_page;
    pagination.value.total = result.calls.total;
    metrics.value = result.reportData || {};
    currentPeriod.value = result.period || currentPeriod.value;
  } catch (err) {
    console.error('Failed to fetch helpline data:', err);
  } finally {
    loading.value = false;
  }
};

const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchData(1);
  }, 350);
};

const clearSearch = () => {
  searchQuery.value = '';
  fetchData(1);
};

const onCycleChange = () => {
  selectedPeriodType.value = 'cycle';
  fetchData(1);
};

const setAllTime = () => {
  selectedPeriodType.value = 'all_time';
  fetchData(1);
};

const toggleDurationFilter = (dur) => {
  filters.value.duration = filters.value.duration === dur ? '' : dur;
  fetchData(1);
};

const toggleStep12Filter = () => {
  filters.value.step12 = filters.value.step12 === '1' ? '' : '1';
  fetchData(1);
};

const toggleDiscussFilter = () => {
  filters.value.discuss = filters.value.discuss === '1' ? '' : '1';
  fetchData(1);
};

const toggleShiftFilter = (shift) => {
  filters.value.shift = filters.value.shift === shift ? '' : shift;
  fetchData(1);
};

const toggleCallerTypeFilter = (ct) => {
  filters.value.caller_type = filters.value.caller_type === ct ? '' : ct;
  fetchData(1);
};

const toggleVolunteerFilter = (vol) => {
  filters.value.volunteer = filters.value.volunteer === vol ? '' : vol;
  fetchData(1);
};

const resetFilters = () => {
  filters.value.duration = '';
  filters.value.shift = '';
  filters.value.caller_type = '';
  filters.value.volunteer = '';
  filters.value.step12 = '';
  filters.value.discuss = '';
  searchQuery.value = '';
  fetchData(1);
};

const toggleRowExpand = (id) => {
  const idx = expandedRows.value.indexOf(id);
  if (idx > -1) {
    expandedRows.value.splice(idx, 1);
  } else {
    expandedRows.value.push(id);
  }
};

const toggleSelectAll = (e) => {
  if (e.target.checked) {
    const pageIds = calls.value.map(c => c.id);
    selectedIds.value = Array.from(new Set([...selectedIds.value, ...pageIds]));
  } else {
    const pageIds = calls.value.map(c => c.id);
    selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id));
  }
};

const clearSelection = () => {
  selectedIds.value = [];
};

const exportSelectedExcel = () => {
  if (selectedIds.value.length === 0) return;
  const url = `${props.exportExcelRoute}?ids=${selectedIds.value.join(',')}`;
  window.location.href = url;
};

const toggleDiscuss = async (call) => {
  try {
    const response = await fetch(`/helpline/calls/${call.id}/toggle-discuss`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken,
        'Accept': 'application/json'
      }
    });

    const result = await response.json();
    if (result.success) {
      call.discuss_in_meeting = result.discuss_in_meeting;
      if (metrics.value.discuss_in_meeting_calls !== undefined) {
        metrics.value.discuss_in_meeting_calls += result.discuss_in_meeting ? 1 : -1;
      }
    }
  } catch (err) {
    console.error('Failed to toggle meeting discuss:', err);
  }
};

const copyCallToClipboard = (call) => {
  const text = `📞 *مكالمة خط المساعدة - NA Egypt*
━━━━━━━━━━━━━━━━━
👤 *المتطوع:* ${call.effective_volunteer_name}
📅 *التاريخ:* ${formatDate(call.call_date)} (${call.call_time_shift})
⏱ *المدة:* ${call.duration === 'less_than_5' ? 'أقل من 5 دقائق' : 'أكثر من 5 دقائق'}
👥 *فئة المتصل:* ${call.effective_caller_type}
📍 *المصدر:* ${call.effective_referral_source}
${call.is_step_12 ? '⚠️ *تحويل لخطوة 12:* نعم' : ''}
${call.discuss_in_meeting ? '📌 *مطلوب مناقشتها بالاجتماع القادم*' : ''}

📝 *ملخص المكالمة:*
${call.call_brief}
${call.additional_info ? `\n💡 *ملاحظات إضافية:* ${call.additional_info}` : ''}`;

  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(() => {
      if (window.Swal) {
        window.Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'تم نسخ ملخص المكالمة بنجاح!',
          showConfirmButton: false,
          timer: 2500
        });
      } else {
        alert('تم نسخ ملخص المكالمة إلى الحافظة!');
      }
    });
  }
};

const confirmDelete = async (call) => {
  let isConfirmed = false;
  if (window.Swal) {
    const res = await window.Swal.fire({
      title: 'حذف سجل المكالمة',
      text: `هل أنت متأكد من حذف المكالمة #${call.id}؟ لا يمكن التراجع عن هذا الإجراء.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'نعم، احذف',
      cancelButtonText: 'إلغاء'
    });
    isConfirmed = res.isConfirmed;
  } else {
    isConfirmed = confirm(`هل أنت متأكد من حذف المكالمة #${call.id}؟`);
  }

  if (isConfirmed) {
    try {
      const response = await fetch(`/helpline/calls/${call.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': props.csrfToken,
          'Accept': 'application/json'
        }
      });

      const res = await response.json();
      if (res.success) {
        calls.value = calls.value.filter(c => c.id !== call.id);
        pagination.value.total--;
        if (metrics.value.total_calls) metrics.value.total_calls--;
        
        if (window.Swal) {
          window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'تم حذف السجل بنجاح',
            showConfirmButton: false,
            timer: 2000
          });
        }
      }
    } catch (err) {
      console.error('Delete failed:', err);
    }
  }
};
</script>

<style scoped>
.helpline-dashboard-container {
  font-family: inherit;
}

.kpi-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 14px;
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}

.border-primary-active {
  border: 2px solid #0d6efd !important;
}

.border-warning-active {
  border: 2px solid #ffc107 !important;
}

.border-danger-active {
  border: 2px solid #dc3545 !important;
}

.shift-progress-item {
  transition: background-color 0.2s ease;
}

.shift-progress-item:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

.call-row {
  transition: background-color 0.15s ease;
}

.cursor-pointer {
  cursor: pointer;
}

.btn-xs {
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
}
</style>
