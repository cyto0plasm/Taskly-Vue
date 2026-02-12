<template>
  <div :class="wrapperClass">
    <label class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
      <slot name="icon" ></slot>
      {{ label }}
    </label>

    <!-- Segmented variant (fits all in one row, underline indicator) -->
    <div v-if="variant === 'segmented'" class="flex items-center gap-0 bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5">
      <button
        v-for="option in options"
        :key="String(option.value)"
        @click="$emit('change', option.value)"
        :class="[
          'flex-1 flex items-center justify-center gap-1.5 px-2 py-1.5 text-xs font-medium rounded-md transition-all duration-200',
          isSelected(option.value)
            ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white'
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
        ]"
      >
        <span
          v-if="option.dot"
          class="w-1.5 h-1.5 rounded-full shrink-0"
          :class="option.dot"
        />
        {{ option.label }}
      </button>
    </div>

    <!-- Chip variant (wrapping pill chips) -->
    <div v-else-if="variant === 'chip'" class="flex flex-wrap gap-1.5">
      <button
        v-for="option in options"
        :key="String(option.value)"
        @click="$emit('change', option.value)"
        :class="[
          'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border transition-all duration-150',
          isSelected(option.value)
            ? selectedClass(option)
            : 'bg-transparent text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-500'
        ]"
      >
        <span
          v-if="option.dot"
          class="w-1.5 h-1.5 rounded-full shrink-0"
          :class="isSelected(option.value) ? 'bg-white/70' : option.dot"
        />
        <svg v-if="isSelected(option.value)" class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
        {{ option.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  label:    { type: String,   required: true },
  options:  { type: Array,    required: true },
  value:    { default: null },
  variant:  { type: String,   default: 'segmented' }, // 'segmented' | 'chip'
  color:    { type: String,   default: 'blue' },
  class:    { type: String,   default: '' },
})

defineEmits(['change'])

const wrapperClass = props.class

function isSelected(val) {
  return val === props.value
}

const colorMap = {
  blue:   'bg-blue-500   border-blue-500   text-white',
  green:  'bg-green-500  border-green-500  text-white',
  purple: 'bg-purple-500 border-purple-500 text-white',
  red:    'bg-red-500    border-red-500    text-white',
  yellow: 'bg-yellow-400 border-yellow-400 text-white',
  orange: 'bg-orange-500 border-orange-500 text-white',
}

function selectedClass(option) {
  if (option.color) return colorMap[option.color] || colorMap.blue
  return colorMap[props.color] || colorMap.blue
}
</script>
