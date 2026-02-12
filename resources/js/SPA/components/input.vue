<script setup>
import {ref} from "vue"
const props = defineProps({
  modelValue: String,
  label: { type: String, default: 'Label' },
  name: { type: String, default: 'inputName' },
  type: { type: String, default: 'text' },
  required: { type: Boolean, default: false },
  placeholder: { type: String, default: 'Enter value' },
  colorHex: { type: String, default: '#6B3EEA' }
});

const emit = defineEmits(['update:modelValue']);
const isFocused = ref(false);
const inputRef = ref(null);

const openPicker = () => {
  if (inputRef.value && props.type === 'date') {
    inputRef.value.showPicker?.();
  }
};
</script>

<template>
  <div class="relative border-none bg-transparent">
    <label :for="name" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <input
        ref="inputRef"
        :type="type"
        :id="name"
        :name="name"
        :placeholder="placeholder"
        :required="required"
        :value="modelValue"
        @input="e => emit('update:modelValue', e.target.value)"
        @focus="isFocused = true"
        @blur="isFocused = false"
        class="dark:bg-[#2A2A2A] dark:text-white text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 dark:border-gray-500 rounded-xl transition-all outline-none"
        :class="{ 'pr-10': type === 'date' }"
         :style="isFocused ? {
        borderColor: colorHex,
        boxShadow: `0 0 0 2px ${colorHex}33`
      } : {}"
        autocomplete="off"
      />
      <svg
        v-if="type === 'date'"
        @click="openPicker"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400 cursor-pointer"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path d="M6 2a1 1 0 00-1 1v1H3a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM3 8h14v8H3V8z"/>
      </svg>
    </div>
  </div>
</template>

<style scoped>
/* Hide default date picker icon */
input[type="date"]::-webkit-calendar-picker-indicator {
  display: none;
  -webkit-appearance: none;
}

input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
