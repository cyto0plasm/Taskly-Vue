<template>
  <div :class="class">
    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ label }}</label>
    <div class="flex flex-wrap gap-1">
      <button
        v-for="option in options"
        :key="option.value"
        @click="$emit('change', option.value)"
        :class="[
          'px-2.5 py-1.5 text-xs rounded-full border transition-all duration-200',
          isSelected(option.value)
            ? activeClass(option.value)
            : inactiveClass
        ]"
      >
        {{ option.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  options: { type: Array, required: true },
  value: { required: false },
  class: { type: String, default: '' },
  color: { type: String, default: 'blue' }, // default color
  colorFn: { type: Function, default: null } // optional dynamic color function
})

// Determine if the button is selected
function isSelected(val) {
  return val === props.value
}

// Class for selected button
function activeClass(val) {
  if (props.colorFn) return props.colorFn(val)
  const colors = {
    blue: 'bg-blue-500 text-white border-blue-500',
    green: 'bg-green-500 text-white border-green-500',
    purple: 'bg-purple-500 text-white border-purple-500',
    red: 'bg-red-500 text-white border-red-500',
    yellow: 'bg-yellow-500 text-white border-yellow-500'
  }
  return colors[props.color] || colors.blue
}

// Class for inactive buttons
const inactiveClass = 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700'
</script>
