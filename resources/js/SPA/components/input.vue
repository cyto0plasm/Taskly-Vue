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
</script>

<template>
  <div class="border-none bg-transparent">
    <label :for="name" class="block text-sm font-semibold text-gray-700 mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <input
      :type="type"
      :id="name"
      :name="name"
      :placeholder="placeholder"
      :required="required"
      :value="modelValue"
      @input="e => emit('update:modelValue', e.target.value)"
      @focus="isFocused = true"
      @blur="isFocused = false"
      class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl transition-all outline-none"
      :style="{
        borderColor: colorHex,
        boxShadow: isFocused ? `0 0 0 1px ${colorHex}` : 'none'
      }"
      autocomplete="off"
    />
  </div>
</template>
