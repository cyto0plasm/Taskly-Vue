<template>
  <div :class="class" class="flex gap-1 items-center px-2 ">
    <label class="mr-2 text-nowrap min-w-12 block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ label }}</label>
    <div class="flex flex-wrap gap-1">
      <button
        v-for="option in options"
        :key="option.value"
        @click="$emit('change', option.value)"
        :class="[
          'px-2.5 py-1.5 text-xs rounded-md border transition-color duration-200 flex items-center gap-2 cursor-pointer bg-gray-100 hover:bg-white dark:bg-[#131313] dark:hover:bg-[#474745] ',
          isSelected(option.value)
            ? activeClass(option.value)
            : inactiveClass
        ]"
      >
      <span class=" rounded-full w-1 h-1 " :class="[(label=='Priority' &&isSelected(option.value))?priorityActiveClass(props?.value):isSelected(option.value)?'bg-green-500':'bg-gray-500']"></span>
      <span>{{ option.label }}</span>

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
    blue: ' text-black dark:text-white border-gray-400 dark:border-gray-500 bg-gray-100 hover:bg-white dark:bg-[#232422] dark:hover:bg-[#3e3e3d]',
    green: ' text-black dark:text-white border-gray-400 dark:border-gray-500 bg-gray-100 hover:bg-white dark:bg-[#232422] dark:hover:bg-[#3e3e3d]',
    purple: ' text-black dark:text-white border-gray-400 dark:border-gray-500 bg-gray-100 hover:bg-white dark:bg-[#232422] dark:hover:bg-[#3e3e3d]',
    red: ' text-black dark:text-white border-gray-400 dark:border-gray-500 bg-gray-100 hover:bg-white dark:bg-[#232422] dark:hover:bg-[#3e3e3d]',
    yellow: ' text-black dark:text-white border-gray-400 dark:border-gray-500 bg-gray-100 hover:bg-white dark:bg-[#232422] dark:hover:bg-[#3e3e3d]'
  }
  return colors[props.color] || colors.blue
}
function priorityActiveClass(val) {
  const colors = {
    low: 'bg-green-500',
    medium: 'bg-yellow-500',
    high: 'bg-red-500',
  }
  return colors[val] || 'bg-gray-500'
}


// Class for inactive buttons
const inactiveClass = 'bg-gray-200 dark:bg-[#292929] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700'
</script>
