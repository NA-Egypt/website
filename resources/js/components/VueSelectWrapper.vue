<template>
  <Select
    v-model="internalValue"
    :options="options"
    :placeholder="placeholder"
    :disabled="disabled"
    @update:modelValue="handleChange"
  />
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Select } from 'vue3-select-component';

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: null
  },
  options: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Select...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  name: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'picker-change']);

const internalValue = ref(props.modelValue);

watch(() => props.modelValue, (newVal) => {
  if (internalValue.value !== newVal) {
    internalValue.value = newVal;
  }
});

const handleChange = (val) => {
  emit('update:modelValue', val);
  emit('picker-change', val);
};

onMounted(() => {
  if (props.name) {
    const eventName = `update-vue-value-${props.name}`;
    const handler = (e) => {
      internalValue.value = e.detail;
    };
    window.addEventListener(eventName, handler);
    onUnmounted(() => {
      window.removeEventListener(eventName, handler);
    });
  }
});
</script>

<style>
.vue-select {
  width: 100%;
}

/* RTL Support */
html[dir="rtl"] [data-assembled-select],
html[dir="rtl"] [data-select-popover] {
  direction: rtl;
  --vs-option-text-align: right;
}
html[dir="rtl"] [data-assembled-select] [data-select-trigger] {
  text-align: right;
}
html[dir="rtl"] [data-assembled-select] [data-select-input] {
  text-align: right;
}
</style>
