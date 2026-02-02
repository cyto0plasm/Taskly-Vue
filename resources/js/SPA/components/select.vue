<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: [String, Number], // For v-model binding
  label: { type: String, required: true },
  name: { type: String, required: true },
  options: { type: Array, default: () => [] }, // [{ value: '1', text: 'Option 1' }]
  colorHex: { type: String, default: '#10B981' },
});

const emit = defineEmits(['update:modelValue']);
const isFocused = ref(false);
</script>

<template>
  <div>
    <label :for="name" class="block text-sm font-semibold text-gray-700 mb-2">
      {{ label }}
    </label>

    <select
      :id="name"
      :name="name"
      :value="modelValue"
      @input="e => emit('update:modelValue', e.target.value)"
      @focus="isFocused = true"
      @blur="isFocused = false"
      class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl transition-all outline-none"
      :style="{
        borderColor: isFocused ? colorHex : '#d1d5db',
        boxShadow: isFocused ? `0 0 0 2px ${colorHex}33` : 'none'
      }"
    >
      <option
        v-for="(option, index) in options"
        :key="index"
        :value="option.value"
      >
        {{ option.text }}
      </option>
    </select>
  </div>
</template>
