<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: String, // For v-model
  label: { type: String, required: true },
  name: { type: String, required: true },
  rows: { type: Number, default: 3 },
  placeholder: { type: String, default: '' },
  colorHex: { type: String, default: '#10B981' }
});

const emit = defineEmits(['update:modelValue']);
const isFocused = ref(false);
</script>

<template>
  <div>
    <label :for="name" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
      {{ label }}
    </label>

    <textarea
      :id="name"
      :name="name"
      :rows="rows"
      :placeholder="placeholder"
      :value="modelValue"
      @input="e => emit('update:modelValue', e.target.value)"
      @focus="isFocused = true"
      @blur="isFocused = false"
      class="dark:bg-[#2A2A2A] dark:text-white text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 dark:border-gray-500  rounded-xl transition-all resize-none outline-none"
       :style="isFocused ? {
        borderColor: colorHex,
        boxShadow: `0 0 0 2px ${colorHex}33`
      } : {}"
    ></textarea>
  </div>
</template>
