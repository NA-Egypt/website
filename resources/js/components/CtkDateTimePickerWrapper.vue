<template>
  <div class="ctk-flatpickr-wrapper">
    <input
      ref="inputRef"
      type="text"
      class="form-control flatpickr-custom-input"
      :placeholder="placeholder || (timeOnly ? 'اختر الوقت' : 'اختر التاريخ')"
    />
    <input type="hidden" :name="name" :id="id" :value="hiddenValue" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import flatpickr from 'flatpickr';
import { Arabic } from 'flatpickr/dist/l10n/ar.js';
import 'flatpickr/dist/flatpickr.min.css';

const props = defineProps({
  name: { type: String, default: '' },
  id: { type: String, default: '' },
  modelValue: { type: [String, Object, Date], default: '' },
  enableTime: { type: [Boolean, String], default: false },
  timeOnly: { type: [Boolean, String], default: false },
  placeholder: { type: String, default: '' },
  locale: { type: String, default: 'ar' }
});

const emit = defineEmits(['update:modelValue', 'change']);

const inputRef = ref(null);
const hiddenValue = ref(props.modelValue || '');
let fpInstance = null;

const isTime = props.timeOnly === true || props.timeOnly === 'true';

onMounted(() => {
  if (!inputRef.value) return;

  const config = {
    locale: props.locale === 'ar' ? Arabic : 'default',
    enableTime: isTime,
    noCalendar: isTime,
    dateFormat: isTime ? 'H:i' : 'Y-m-d',
    altInput: isTime,
    altFormat: isTime ? 'h:i K' : 'Y-m-d',
    time_24hr: false, // 12-hour selection with AM/PM toggle
    defaultDate: props.modelValue || null,
    onChange: (selectedDates, dateStr) => {
      // dateStr holds the formatted 24h 'H:i' (e.g. "14:30") or 'Y-m-d' for server
      hiddenValue.value = dateStr;
      emit('update:modelValue', dateStr);
      emit('change', dateStr);

      const hiddenEl = inputRef.value.parentElement.querySelector('input[type="hidden"]');
      if (hiddenEl) {
        hiddenEl.value = dateStr;
        hiddenEl.dispatchEvent(new Event('change', { bubbles: true }));
      }
      inputRef.value.parentElement.dispatchEvent(
        new CustomEvent('picker-change', { detail: dateStr, bubbles: true })
      );
    }
  };

  fpInstance = flatpickr(inputRef.value, config);
});

watch(() => props.modelValue, (newVal) => {
  hiddenValue.value = newVal || '';
  if (fpInstance) {
    fpInstance.setDate(newVal || '', false);
  }
});

onUnmounted(() => {
  if (fpInstance) {
    fpInstance.destroy();
  }
});
</script>

<style>
.ctk-flatpickr-wrapper {
  width: 100%;
}

.flatpickr-custom-input,
.ctk-flatpickr-wrapper .form-control {
  font-size: 0.85rem !important;
  height: 38px !important;
  border-radius: 10px !important;
  text-align: center !important;
  background-color: #ffffff !important;
}

.flatpickr-calendar {
  font-size: 0.85rem !important;
  border-radius: 14px !important;
  border: 1px solid rgba(50, 85, 127, 0.15) !important;
  box-shadow: 0 12px 30px rgba(31, 38, 135, 0.15) !important;
  overflow: hidden !important;
}

.flatpickr-months {
  background: linear-gradient(135deg, #32557f 0%, #00698f 100%) !important;
  border-radius: 12px 12px 0 0 !important;
  padding: 6px 4px !important;
  align-items: center !important;
}

.flatpickr-month {
  color: #ffffff !important;
  fill: #ffffff !important;
  height: 36px !important;
}

.flatpickr-current-month {
  color: #ffffff !important;
  font-weight: 700 !important;
  font-size: 0.95rem !important;
  padding-top: 4px !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
  color: #ffffff !important;
  font-weight: 700 !important;
  border-radius: 6px !important;
  padding: 2px 6px !important;
  background: rgba(255, 255, 255, 0.12) !important;
  transition: background 0.2s ease !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months:hover {
  background: rgba(255, 255, 255, 0.25) !important;
}

.flatpickr-current-month .numInputWrapper {
  border-radius: 6px !important;
  padding: 2px 4px !important;
  background: rgba(255, 255, 255, 0.12) !important;
  transition: background 0.2s ease !important;
}

.flatpickr-current-month .numInputWrapper:hover {
  background: rgba(255, 255, 255, 0.25) !important;
}

.flatpickr-current-month input.cur-year {
  color: #ffffff !important;
  font-weight: 700 !important;
}

.flatpickr-prev-month,
.flatpickr-next-month {
  color: #ffffff !important;
  fill: #ffffff !important;
  width: 28px !important;
  height: 28px !important;
  border-radius: 50% !important;
  background: rgba(255, 255, 255, 0.15) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  top: 10px !important;
  transition: all 0.2s ease !important;
}

.flatpickr-prev-month:hover,
.flatpickr-next-month:hover {
  background: rgba(255, 255, 255, 0.35) !important;
  transform: scale(1.1) !important;
  color: #ffffff !important;
  fill: #ffffff !important;
}

.flatpickr-prev-month svg,
.flatpickr-next-month svg {
  fill: #ffffff !important;
  width: 12px !important;
  height: 12px !important;
}

span.flatpickr-weekday {
  color: #32557f !important;
  font-weight: 700 !important;
  font-size: 0.8rem !important;
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
  background: #32557f !important;
  border-color: #32557f !important;
}
</style>
