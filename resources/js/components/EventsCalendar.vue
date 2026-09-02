<template>
  <div class="calendar-app-wrapper" :dir="isRtl ? 'rtl' : 'ltr'">
    <!-- Main Card Container -->
    <div class="calendar-card">
      
      <!-- Top Control Bar -->
      <div class="calendar-header">
        <!-- Navigation Buttons -->
        <div class="nav-controls">
          <button @click="prevPeriod" type="button" class="btn-nav" :title="isRtl ? 'السابق' : 'Previous'">
            <svg class="nav-icon" :class="{ 'rotate-180': isRtl }" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <button @click="goToday" type="button" class="btn-today">
            {{ isRtl ? 'اليوم' : 'Today' }}
          </button>

          <button @click="nextPeriod" type="button" class="btn-nav" :title="isRtl ? 'التالي' : 'Next'">
            <svg class="nav-icon" :class="{ 'rotate-180': isRtl }" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>

        <!-- Period Title -->
        <h2 class="period-title">{{ currentPeriodLabel }}</h2>

        <!-- View Switcher & Action Button -->
        <div class="header-actions">
          <div class="view-switcher">
            <button
              v-for="v in availableViews"
              :key="v.id"
              @click="currentView = v.id"
              type="button"
              class="view-btn"
              :class="{ active: currentView === v.id }"
            >
              {{ isRtl ? v.labelAr : v.labelEn }}
            </button>
          </div>

          <button v-if="canCreate || canManage" @click="openCreateModal()" type="button" class="btn-add-event">
            <svg class="add-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ isRtl ? 'إضافة حدث' : 'Add Event' }}</span>
          </button>
        </div>
      </div>

      <!-- MONTH VIEW -->
      <div v-if="currentView === 'month'" class="month-view-container">
        <div class="weekday-header-grid">
          <div v-for="(dayName, idx) in weekdayNames" :key="idx" class="weekday-cell">
            {{ dayName }}
          </div>
        </div>

        <div class="month-days-grid">
          <div
            v-for="cell in monthCells"
            :key="cell.dateStr"
            @click="onCellClick(cell.dateStr)"
            class="day-cell"
            :class="{
              'other-month': !cell.isCurrentMonth,
              'is-today': cell.isToday
            }"
          >
            <div class="cell-top">
              <span class="day-number" :class="{ 'today-badge': cell.isToday }">
                {{ cell.dayNum }}
              </span>
              <span v-if="canCreate || canManage" class="cell-add-plus">+</span>
            </div>

            <div class="cell-events-list">
              <div
                v-for="ev in cell.events"
                :key="ev.id + '-' + ev.start"
                @click.stop="openViewModal(ev)"
                class="event-pill"
                :style="{ backgroundColor: ev.color || '#00698f' }"
              >
                <span v-if="ev.is_featured" class="featured-star">★</span>
                <span class="event-title-text">{{ ev.title }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- WEEK VIEW -->
      <div v-else-if="currentView === 'week'" class="week-view-container">
        <div class="week-header-grid">
          <div v-for="wDay in weekDays" :key="wDay.dateStr" class="week-header-cell">
            <div class="week-day-name">{{ wDay.dayName }}</div>
            <div class="week-day-num" :class="{ 'today-text': wDay.isToday }">{{ wDay.dayNum }}</div>
          </div>
        </div>

        <div class="week-body-grid">
          <div
            v-for="wDay in weekDays"
            :key="wDay.dateStr"
            @click="onCellClick(wDay.dateStr)"
            class="week-day-column"
          >
            <div class="week-events-stack">
              <div
                v-for="ev in wDay.events"
                :key="ev.id + '-' + ev.start"
                @click.stop="openViewModal(ev)"
                class="week-event-card"
                :style="{ backgroundColor: ev.color || '#00698f' }"
              >
                <div class="event-card-title">{{ ev.title }}</div>
                <div class="event-card-time">{{ formatTime(ev.start) }} - {{ formatTime(ev.end) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- DAY VIEW -->
      <div v-else-if="currentView === 'day'" class="day-view-container">
        <div class="day-view-header">
          <h3>{{ currentDayFormatted }}</h3>
        </div>

        <div v-if="dayViewEvents.length === 0" class="empty-state">
          {{ isRtl ? 'لا توجد أحداث في هذا اليوم' : 'No events scheduled for this day' }}
        </div>

        <div v-else class="day-events-list">
          <div
            v-for="ev in dayViewEvents"
            :key="ev.id + '-' + ev.start"
            @click="openViewModal(ev)"
            class="day-event-row"
            :style="{ borderLeftColor: ev.color || '#00698f', borderRightColor: isRtl ? (ev.color || '#00698f') : 'transparent' }"
          >
            <div class="day-event-details">
              <h4 class="day-event-title">{{ ev.title }}</h4>
              <p v-if="ev.description" class="day-event-desc">{{ ev.description }}</p>
              <div class="day-event-meta">
                <span v-if="ev.location">📍 {{ ev.location }}</span>
                <span v-if="ev.organizer">👤 {{ ev.organizer }}</span>
              </div>
            </div>
            <div class="day-event-time">
              {{ formatTime(ev.start) }} - {{ formatTime(ev.end) }}
            </div>
          </div>
        </div>
      </div>

      <!-- LIST VIEW -->
      <div v-else-if="currentView === 'list'" class="list-view-container">
        <div v-if="sortedEvents.length === 0" class="empty-state">
          {{ isRtl ? 'لا توجد أحداث قادمة' : 'No upcoming events' }}
        </div>

        <div v-else class="list-events-stack">
          <div
            v-for="ev in sortedEvents"
            :key="ev.id + '-' + ev.start"
            @click="openViewModal(ev)"
            class="list-event-card"
          >
            <div class="list-card-accent" :style="{ backgroundColor: ev.color || '#00698f' }"></div>
            <div class="list-card-body">
              <div class="list-card-header">
                <h4 class="list-card-title">{{ ev.title }}</h4>
                <span v-if="ev.is_featured" class="featured-badge">★ {{ isRtl ? 'مميز' : 'Featured' }}</span>
              </div>
              <p v-if="ev.description" class="list-card-desc">{{ ev.description }}</p>
              <div class="list-card-tags">
                <span v-if="ev.location">📍 {{ ev.location }}</span>
                <span v-if="ev.organizer">👤 {{ ev.organizer }}</span>
                <span v-if="ev.formatted_recurrence">🔄 {{ ev.formatted_recurrence }}</span>
              </div>
            </div>
            <div class="list-card-date">
              <div class="date-main">{{ formatDate(ev.start) }}</div>
              <div class="date-sub">{{ formatTime(ev.start) }} - {{ formatTime(ev.end) }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- VIEW EVENT MODAL -->
    <div v-if="activeViewModal" class="modal-overlay" @click.self="activeViewModal = false">
      <div class="modal-card">
        <button @click="activeViewModal = false" class="modal-close-btn">✕</button>

        <div class="modal-header-row">
          <div class="modal-color-strip" :style="{ backgroundColor: activeEvent?.color || '#00698f' }"></div>
          <div>
            <h3 class="modal-event-title">{{ activeEvent?.title }}</h3>
            <span v-if="activeEvent?.is_featured" class="featured-text">★ {{ isRtl ? 'حدث مميز' : 'Featured Event' }}</span>
          </div>
        </div>

        <div class="modal-body-content">
          <div class="info-row">
            <span class="info-label">📅 {{ isRtl ? 'الوقت:' : 'Time:' }}</span>
            <span class="info-val">{{ formatDate(activeEvent?.start) }} ({{ formatTime(activeEvent?.start) }} - {{ formatTime(activeEvent?.end) }})</span>
          </div>

          <div v-if="activeEvent?.location" class="info-row">
            <span class="info-label">📍 {{ isRtl ? 'الموقع:' : 'Location:' }}</span>
            <span class="info-val">{{ activeEvent.location }}</span>
          </div>

          <div v-if="activeEvent?.organizer" class="info-row">
            <span class="info-label">👤 {{ isRtl ? 'المنظم:' : 'Organizer:' }}</span>
            <span class="info-val">{{ activeEvent.organizer }}</span>
          </div>

          <div v-if="activeEvent?.formatted_recurrence" class="info-row">
            <span class="info-label">🔄 {{ isRtl ? 'التكرار:' : 'Recurrence:' }}</span>
            <span class="info-val">{{ activeEvent.formatted_recurrence }}</span>
          </div>

          <div v-if="activeEvent?.description" class="info-desc-box">
            <p>{{ activeEvent.description }}</p>
          </div>
        </div>

        <div class="modal-footer-actions">
          <button v-if="canEditActiveEvent" @click="deleteActiveEvent()" type="button" class="btn-delete">
            {{ isRtl ? 'حذف' : 'Delete' }}
          </button>
          
          <button v-if="canEditActiveEvent" @click="openEditModal(activeEvent)" type="button" class="btn-edit">
            {{ isRtl ? 'تعديل' : 'Edit' }}
          </button>

          <button @click="activeViewModal = false" type="button" class="btn-close-action">
            {{ isRtl ? 'إغلاق' : 'Close' }}
          </button>
        </div>
      </div>
    </div>

    <!-- CREATE / EDIT FORM MODAL -->
    <div v-if="activeFormModal" class="modal-overlay" @click.self="activeFormModal = false">
      <div class="modal-card modal-card-lg">
        <button @click="activeFormModal = false" class="modal-close-btn">✕</button>

        <h3 class="form-title">
          {{ form.id ? (isRtl ? 'تعديل الحدث' : 'Edit Event') : (isRtl ? 'إضافة حدث جديد' : 'Add New Event') }}
        </h3>

        <form @submit.prevent="saveForm" class="calendar-form">
          <div class="form-group">
            <label class="form-label">{{ isRtl ? 'العنوان' : 'Title' }} *</label>
            <input v-model="form.title" type="text" required class="form-control" />
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label">{{ isRtl ? 'تاريخ ووقت البدء' : 'Start Date & Time' }} *</label>
              <input v-model="form.start" type="datetime-local" required class="form-control" />
            </div>

            <div class="form-group">
              <label class="form-label">{{ isRtl ? 'تاريخ ووقت الانتهاء' : 'End Date & Time' }} *</label>
              <input v-model="form.end" type="datetime-local" required class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">{{ isRtl ? 'الوصف' : 'Description' }}</label>
            <textarea v-model="form.description" rows="3" class="form-control"></textarea>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label">{{ isRtl ? 'المنظم' : 'Organizer' }}</label>
              <input v-model="form.organizer" type="text" class="form-control" />
            </div>

            <div class="form-group">
              <label class="form-label">{{ isRtl ? 'الموقع' : 'Location' }}</label>
              <input v-model="form.location" type="text" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">{{ isRtl ? 'لون الحدث' : 'Event Color' }}</label>
            <div class="color-picker-row">
              <button
                v-for="c in colorPresets"
                :key="c"
                @click.prevent="form.color = c"
                type="button"
                class="color-swatch"
                :class="{ active: form.color === c }"
                :style="{ backgroundColor: c }"
              ></button>
              <input v-model="form.color" type="color" class="color-native-input" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">{{ isRtl ? 'نمط التكرار' : 'Recurrence' }}</label>
            <select v-model="formRecurrenceSelect" class="form-control">
              <option value="once">{{ isRtl ? 'مرة واحدة' : 'Once' }}</option>
              <option value="weekly">{{ isRtl ? 'أسبوعياً' : 'Weekly' }}</option>
              <option value="monthly">{{ isRtl ? 'شهرياً' : 'Monthly' }}</option>
              <option value="every_two_months">{{ isRtl ? 'كل شهرين' : 'Every Two Months' }}</option>
              <option value="1st">{{ isRtl ? 'الأسبوع الأول' : '1st Week' }}</option>
              <option value="2nd">{{ isRtl ? 'الأسبوع الثاني' : '2nd Week' }}</option>
              <option value="3rd">{{ isRtl ? 'الأسبوع الثالث' : '3rd Week' }}</option>
              <option value="4th">{{ isRtl ? 'الأسبوع الرابع' : '4th Week' }}</option>
              <option value="last">{{ isRtl ? 'الأسبوع الأخير' : 'Last Week' }}</option>
            </select>
          </div>

          <div class="form-checkbox-row">
            <input v-model="form.is_featured" type="checkbox" id="is_featured_cb" class="form-checkbox" />
            <label for="is_featured_cb" class="checkbox-label">{{ isRtl ? 'حدث مميز (يظهر في المقدمة)' : 'Featured Event' }}</label>
          </div>

          <div class="modal-footer-actions">
            <button @click="activeFormModal = false" type="button" class="btn-close-action">
              {{ isRtl ? 'إلغاء' : 'Cancel' }}
            </button>

            <button type="submit" :disabled="saving" class="btn-submit">
              {{ saving ? (isRtl ? 'جارٍ الحفظ...' : 'Saving...') : (isRtl ? 'حفظ الحدث' : 'Save Event') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EventsCalendar',
  props: {
    initialEvents: {
      type: Array,
      default: () => []
    },
    fetchUrl: {
      type: String,
      default: '/web-calendar-events'
    },
    storeUrl: {
      type: String,
      default: '/web-calendar-events'
    },
    canManage: {
      type: Boolean,
      default: false
    },
    canCreate: {
      type: Boolean,
      default: false
    },
    currentUserId: {
      type: [Number, String],
      default: null
    },
    locale: {
      type: String,
      default: 'ar'
    },
    csrfToken: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      currentView: 'month',
      currentDate: new Date(),
      events: [],
      activeViewModal: false,
      activeFormModal: false,
      activeEvent: null,
      saving: false,
      colorPresets: ['#00698f', '#28a745', '#dc3545', '#fd7e14', '#6f42c1', '#17a2b8'],
      formRecurrenceSelect: 'once',
      form: {
        id: null,
        title: '',
        start: '',
        end: '',
        description: '',
        color: '#00698f',
        organizer: '',
        location: '',
        recurrence: ['once'],
        is_featured: false
      },
      availableViews: [
        { id: 'month', labelEn: 'Month', labelAr: 'شهري' },
        { id: 'week', labelEn: 'Week', labelAr: 'أسبوعي' },
        { id: 'day', labelEn: 'Day', labelAr: 'يومي' },
        { id: 'list', labelEn: 'List', labelAr: 'قائمة' }
      ]
    };
  },
  computed: {
    canEditActiveEvent() {
      if (!this.activeEvent) return false;
      if (this.canManage) return true;
      if (this.canCreate && this.currentUserId && this.activeEvent.user_id && Number(this.activeEvent.user_id) === Number(this.currentUserId)) {
        return true;
      }
      return false;
    },
    isRtl() {
      return this.locale === 'ar';
    },
    weekdayNames() {
      return this.isRtl
        ? ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']
        : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    },
    currentPeriodLabel() {
      const year = this.currentDate.getFullYear();
      const monthIndex = this.currentDate.getMonth();
      const monthNamesAr = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
      const monthNamesEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      
      const mName = this.isRtl ? monthNamesAr[monthIndex] : monthNamesEn[monthIndex];
      return `${mName} ${year}`;
    },
    currentDayFormatted() {
      return this.currentDate.toLocaleDateString(this.isRtl ? 'ar-EG' : 'en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    monthCells() {
      const year = this.currentDate.getFullYear();
      const month = this.currentDate.getMonth();
      
      const firstDayOfMonth = new Date(year, month, 1);
      const lastDayOfMonth = new Date(year, month + 1, 0);

      const startDayOfWeek = firstDayOfMonth.getDay();
      const daysInMonth = lastDayOfMonth.getDate();

      const cells = [];
      const todayStr = this.formatDateISO(new Date());

      // Previous month
      const prevMonthLastDay = new Date(year, month, 0).getDate();
      for (let i = startDayOfWeek - 1; i >= 0; i--) {
        const dNum = prevMonthLastDay - i;
        const d = new Date(year, month - 1, dNum);
        const dateStr = this.formatDateISO(d);
        cells.push({
          dateStr,
          dayNum: dNum,
          isCurrentMonth: false,
          isToday: dateStr === todayStr,
          events: this.getEventsForDate(dateStr)
        });
      }

      // Current month
      for (let dNum = 1; dNum <= daysInMonth; dNum++) {
        const d = new Date(year, month, dNum);
        const dateStr = this.formatDateISO(d);
        cells.push({
          dateStr,
          dayNum: dNum,
          isCurrentMonth: true,
          isToday: dateStr === todayStr,
          events: this.getEventsForDate(dateStr)
        });
      }

      // Next month padding to fill grid
      const totalCells = cells.length > 35 ? 42 : 35;
      const remaining = totalCells - cells.length;
      for (let dNum = 1; dNum <= remaining; dNum++) {
        const d = new Date(year, month + 1, dNum);
        const dateStr = this.formatDateISO(d);
        cells.push({
          dateStr,
          dayNum: dNum,
          isCurrentMonth: false,
          isToday: dateStr === todayStr,
          events: this.getEventsForDate(dateStr)
        });
      }

      return cells;
    },
    weekDays() {
      const startOfWeek = new Date(this.currentDate);
      const day = startOfWeek.getDay();
      startOfWeek.setDate(startOfWeek.getDate() - day);

      const days = [];
      const todayStr = this.formatDateISO(new Date());

      for (let i = 0; i < 7; i++) {
        const d = new Date(startOfWeek);
        d.setDate(d.getDate() + i);
        const dateStr = this.formatDateISO(d);
        days.push({
          dateStr,
          dayNum: d.getDate(),
          dayName: this.weekdayNames[i],
          isToday: dateStr === todayStr,
          events: this.getEventsForDate(dateStr)
        });
      }
      return days;
    },
    dayViewEvents() {
      const dateStr = this.formatDateISO(this.currentDate);
      return this.getEventsForDate(dateStr);
    },
    sortedEvents() {
      return [...this.events].sort((a, b) => new Date(a.start) - new Date(b.start));
    }
  },
  watch: {
    formRecurrenceSelect(val) {
      this.form.recurrence = [val];
    },
    currentDate() {
      this.loadEvents();
    }
  },
  mounted() {
    if (this.initialEvents && this.initialEvents.length > 0) {
      this.events = this.initialEvents;
    }
    this.loadEvents();
  },
  methods: {
    async loadEvents() {
      try {
        const year = this.currentDate.getFullYear();
        const start = `${year - 1}-01-01T00:00:00`;
        const end = `${year + 2}-12-31T23:59:59`;
        const delimiter = this.fetchUrl.includes('?') ? '&' : '?';
        const url = `${this.fetchUrl}${delimiter}start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;

        const res = await fetch(url, {
          headers: { 'Accept': 'application/json' }
        });
        if (res.ok) {
          const json = await res.json();
          this.events = json.data || json;
        }
      } catch (err) {
        console.error('Failed loading events', err);
      }
    },
    getEventsForDate(dateStr) {
      return this.events.filter(ev => {
        if (!ev.start) return false;
        let evDateStr = '';
        if (typeof ev.start === 'string') {
          evDateStr = ev.start.split('T')[0].split(' ')[0];
        } else {
          evDateStr = this.formatDateISO(new Date(ev.start));
        }
        return evDateStr === dateStr;
      });
    },
    formatDateISO(date) {
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      return `${y}-${m}-${d}`;
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString(this.isRtl ? 'ar-EG' : 'en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    formatTime(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleTimeString(this.isRtl ? 'ar-EG' : 'en-US', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    prevPeriod() {
      const d = new Date(this.currentDate);
      if (this.currentView === 'month') {
        d.setMonth(d.getMonth() - 1);
      } else if (this.currentView === 'week') {
        d.setDate(d.getDate() - 7);
      } else if (this.currentView === 'day') {
        d.setDate(d.getDate() - 1);
      }
      this.currentDate = d;
    },
    nextPeriod() {
      const d = new Date(this.currentDate);
      if (this.currentView === 'month') {
        d.setMonth(d.getMonth() + 1);
      } else if (this.currentView === 'week') {
        d.setDate(d.getDate() + 7);
      } else if (this.currentView === 'day') {
        d.setDate(d.getDate() + 1);
      }
      this.currentDate = d;
    },
    goToday() {
      this.currentDate = new Date();
    },
    onCellClick(dateStr) {
      if (!this.canCreate && !this.canManage) return;
      this.openCreateModal(dateStr);
    },
    openViewModal(ev) {
      this.activeEvent = ev;
      this.activeViewModal = true;
    },
    openCreateModal(dateStr = null) {
      const dStr = dateStr || this.formatDateISO(new Date());
      this.form = {
        id: null,
        title: '',
        start: `${dStr}T09:00`,
        end: `${dStr}T10:00`,
        description: '',
        color: '#00698f',
        organizer: '',
        location: '',
        recurrence: ['once'],
        is_featured: false
      };
      this.formRecurrenceSelect = 'once';
      this.activeFormModal = true;
    },
    openEditModal(ev) {
      this.activeViewModal = false;
      const formatDTLocal = (str) => {
        if (!str) return '';
        const d = new Date(str);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const h = String(d.getHours()).padStart(2, '0');
        const min = String(d.getMinutes()).padStart(2, '0');
        return `${y}-${m}-${day}T${h}:${min}`;
      };

      this.form = {
        id: ev.id,
        title: ev.title,
        start: formatDTLocal(ev.start),
        end: formatDTLocal(ev.end),
        description: ev.description || '',
        color: ev.color || '#00698f',
        organizer: ev.organizer || '',
        location: ev.location || '',
        recurrence: ev.recurrence || ['once'],
        is_featured: Boolean(ev.is_featured)
      };
      this.formRecurrenceSelect = (ev.recurrence && ev.recurrence[0]) ? ev.recurrence[0] : 'once';
      this.activeFormModal = true;
    },
    async saveForm() {
      this.saving = true;
      try {
        const token = this.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const isEdit = Boolean(this.form.id);
        const url = isEdit ? `${this.storeUrl}/${this.form.id}` : this.storeUrl;
        const method = isEdit ? 'PUT' : 'POST';

        const res = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || ''
          },
          body: JSON.stringify(this.form)
        });

        if (res.ok) {
          this.activeFormModal = false;
          await this.loadEvents();
        } else {
          const err = await res.json();
          alert(err.message || 'Error saving event');
        }
      } catch (err) {
        console.error('Save failed', err);
      } finally {
        this.saving = false;
      }
    },
    async deleteActiveEvent() {
      if (!this.activeEvent || !confirm(this.isRtl ? 'هل أنت تأكد من حذف هذا الحدث؟' : 'Are you sure you want to delete this event?')) return;
      
      try {
        const token = this.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch(`${this.storeUrl}/${this.activeEvent.id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || ''
          }
        });

        if (res.ok) {
          this.activeViewModal = false;
          await this.loadEvents();
        }
      } catch (err) {
        console.error('Delete failed', err);
      }
    }
  }
};
</script>

<style scoped>
/* Glassmorphism & Core Layout Styles */
.calendar-app-wrapper {
  width: 100%;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  color: #1f2937;
}

.calendar-card {
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid #f3f4f6;
  padding: 1.5rem;
  min-height: 650px;
  display: flex;
  flex-direction: column;
}

/* Header */
.calendar-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #f3f4f6;
  margin-bottom: 1.25rem;
}

.nav-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-nav {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-nav:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  transform: translateY(-1px);
}

.nav-icon {
  width: 20px;
  height: 20px;
  color: #374151;
}

.rotate-180 {
  transform: rotate(180deg);
}

.btn-today {
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  background: rgba(0, 105, 143, 0.1);
  color: #00698f;
  font-weight: 600;
  font-size: 0.875rem;
  border: 1px solid rgba(0, 105, 143, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-today:hover {
  background: rgba(0, 105, 143, 0.18);
}

.period-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
  text-align: center;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.view-switcher {
  display: inline-flex;
  padding: 3px;
  background: #f3f4f6;
  border-radius: 0.75rem;
}

.view-btn {
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.8125rem;
  font-weight: 500;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-btn.active {
  background: #ffffff;
  color: #111827;
  font-weight: 700;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.btn-add-event {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  background: #00698f;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 105, 143, 0.3);
  transition: all 0.2s ease;
}

.btn-add-event:hover {
  opacity: 0.92;
  transform: translateY(-1px);
}

.add-icon {
  width: 16px;
  height: 16px;
}

/* Month Grid */
.month-view-container {
  border: 1px solid #f3f4f6;
  border-radius: 0.875rem;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.weekday-header-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  text-align: center;
  font-weight: 600;
  font-size: 0.8125rem;
  color: #4b5563;
  padding: 0.625rem 0;
}

.month-days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  auto-rows: 1fr;
  grid-auto-rows: 1fr;
  flex: 1;
  min-height: 480px;
}

.day-cell {
  min-height: 100px;
  padding: 0.5rem;
  border-bottom: 1px solid #f3f4f6;
  border-right: 1px solid #f3f4f6;
  background: #ffffff;
  position: relative;
  cursor: pointer;
  transition: background 0.15s ease;
}

[dir="rtl"] .day-cell {
  border-right: none;
  border-left: 1px solid #f3f4f6;
}

.day-cell:hover {
  background: #f0f9ff;
}

.day-cell.other-month {
  background: #fafafa;
  color: #9ca3af;
}

.day-cell.is-today {
  background: rgba(0, 105, 143, 0.04);
}

.cell-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.375rem;
}

.day-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  font-size: 0.8125rem;
  font-weight: 600;
  color: #374151;
}

.today-badge {
  background: #00698f;
  color: #ffffff;
  font-weight: 700;
}

.cell-add-plus {
  opacity: 0;
  color: #00698f;
  font-weight: 700;
  font-size: 0.875rem;
  transition: opacity 0.15s ease;
}

.day-cell:hover .cell-add-plus {
  opacity: 1;
}

.cell-events-list {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  max-height: 75px;
  overflow-y: auto;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE / Edge */
}

.cell-events-list::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

.event-pill {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #ffffff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1);
  transition: filter 0.15s ease, box-shadow 0.15s ease;
}

.event-pill:hover {
  filter: brightness(1.12);
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

.featured-star {
  color: #fde047;
  margin-right: 0.25rem;
}

/* Week View */
.week-view-container {
  border: 1px solid #f3f4f6;
  border-radius: 0.875rem;
  overflow: hidden;
}

.week-header-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  text-align: center;
  padding: 0.75rem 0;
}

.week-day-name {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.week-day-num {
  font-size: 1.125rem;
  font-weight: 700;
  color: #111827;
}

.today-text {
  color: #00698f;
}

.week-body-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  min-height: 450px;
}

.week-day-column {
  padding: 0.5rem;
  border-right: 1px solid #f3f4f6;
  min-height: 450px;
  cursor: pointer;
}

[dir="rtl"] .week-day-column {
  border-right: none;
  border-left: 1px solid #f3f4f6;
}

.week-day-column:hover {
  background: #f0f9ff;
}

.week-events-stack {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.week-event-card {
  padding: 0.5rem;
  border-radius: 0.5rem;
  color: #ffffff;
  font-size: 0.75rem;
}

.event-card-title {
  font-weight: 700;
}

.event-card-time {
  opacity: 0.9;
  font-size: 0.6875rem;
}

/* Day & List Views */
.day-view-container, .list-view-container {
  border: 1px solid #f3f4f6;
  border-radius: 0.875rem;
  padding: 1.5rem;
  min-height: 400px;
}

.day-view-header h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-align: center;
}

.empty-state {
  text-align: center;
  color: #9ca3af;
  padding: 3rem 0;
  font-size: 0.9375rem;
}

.day-event-row {
  padding: 1rem;
  border-radius: 0.75rem;
  background: #f9fafb;
  border-left-width: 4px;
  border-left-style: solid;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: all 0.2s ease;
}

.day-event-row:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.day-event-title {
  font-weight: 700;
  font-size: 1rem;
  margin: 0 0 0.25rem 0;
}

.day-event-desc {
  font-size: 0.8125rem;
  color: #4b5563;
  margin: 0 0 0.5rem 0;
}

.day-event-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.day-event-time {
  font-weight: 600;
  font-size: 0.8125rem;
  color: #374151;
}

.list-events-stack {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.list-event-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.875rem;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.list-event-card:hover {
  border-color: #00698f;
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.list-card-accent {
  width: 4px;
  height: 48px;
  border-radius: 2px;
  margin-right: 0.875rem;
}

[dir="rtl"] .list-card-accent {
  margin-right: 0;
  margin-left: 0.875rem;
}

.list-card-body {
  flex: 1;
}

.list-card-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.list-card-title {
  font-size: 1rem;
  font-weight: 700;
  margin: 0;
}

.featured-badge {
  background: #fef3c7;
  color: #92400e;
  font-size: 0.6875rem;
  font-weight: 700;
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
}

.list-card-desc {
  font-size: 0.8125rem;
  color: #6b7280;
  margin: 0.25rem 0;
}

.list-card-tags {
  display: flex;
  gap: 0.75rem;
  font-size: 0.75rem;
  color: #9ca3af;
}

.list-card-date {
  text-align: right;
  font-size: 0.8125rem;
}

[dir="rtl"] .list-card-date {
  text-align: left;
}

.date-main {
  font-weight: 700;
  color: #111827;
}

.date-sub {
  color: #9ca3af;
}

/* Modals */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 999;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-card {
  background: #ffffff;
  border-radius: 1.25rem;
  max-width: 500px;
  width: 100%;
  padding: 1.5rem;
  position: relative;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.modal-card-lg {
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  border: none;
  background: transparent;
  font-size: 1.25rem;
  color: #9ca3af;
  cursor: pointer;
}

[dir="rtl"] .modal-close-btn {
  right: auto;
  left: 1rem;
}

.modal-header-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.modal-color-strip {
  width: 16px;
  height: 32px;
  border-radius: 8px;
}

.modal-event-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
}

.featured-text {
  font-size: 0.75rem;
  color: #d97706;
  font-weight: 600;
}

.modal-body-content {
  margin-bottom: 1.5rem;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  margin-bottom: 0.625rem;
}

.info-label {
  font-weight: 600;
  color: #6b7280;
}

.info-val {
  color: #111827;
}

.info-desc-box {
  padding-top: 0.75rem;
  border-top: 1px solid #f3f4f6;
  font-size: 0.875rem;
  color: #4b5563;
}

.modal-footer-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.btn-delete {
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  background: #fef2f2;
  color: #dc2626;
  font-weight: 600;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
}

.btn-edit {
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  background: #f3f4f6;
  color: #374151;
  font-weight: 600;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
}

.btn-close-action {
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  background: #00698f;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
}

.form-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.calendar-form {
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.form-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
}

.form-control {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border-radius: 0.75rem;
  border: 1px solid #d1d5db;
  font-size: 0.875rem;
  box-sizing: border-box;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}

.color-picker-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.color-swatch {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  transition: transform 0.15s ease;
}

.color-swatch.active {
  transform: scale(1.15);
  border-color: #111827;
}

.color-native-input {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
  cursor: pointer;
}

.form-checkbox-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding-top: 0.25rem;
}

.form-checkbox {
  width: 16px;
  height: 16px;
}

.checkbox-label {
  font-size: 0.8125rem;
  font-weight: 600;
  color: #374151;
}

.btn-submit {
  padding: 0.5rem 1.25rem;
  border-radius: 0.75rem;
  background: #00698f;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.875rem;
  border: none;
  cursor: pointer;
}
</style>
