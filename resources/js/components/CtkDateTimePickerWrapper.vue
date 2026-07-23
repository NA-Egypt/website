<template>
  <div class="ctk-flatpickr-wrapper">
    <input
      ref="inputRef"
      type="text"
      class="form-control flatpickr-custom-input"
      :placeholder="placeholder || (timeOnly ? 'اختر الوقت' : 'اختر التاريخ')"
      :value="modelValue"
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
    dateFormat: isTime ? 'h:i K' : 'Y-m-d',
    time_24hr: false, // Strict 12-hour AM/PM format
    defaultDate: props.modelValue || null,
    onChange: (selectedDates, dateStr) => {
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

.flatpickr-custom-input {
  font-size: 0.85rem !important;
  height: 38px !important;
  border-radius: 10px !important;
  text-align: center !important;
  background-color: #ffffff !important;
}

.flatpickr-calendar {
  font-size: 0.85rem !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}
</style>
