<!-- SectionHeader.vue -->
<script setup>
import { computed } from "vue";

const props = defineProps({
  title: { type: String, required: true },
  activeCount: { type: Number, default: 0 },
  loading: { type: Boolean, default: false },
  collapsed: { type: Boolean, default: false },
  showClear: { type: Boolean, default: false }
});

const emit = defineEmits(["toggle", "clear"]);

function onToggle() {
  emit("toggle");
}

function onClear() {
  emit("clear");
}
// Reactive style for border-bottom
const borderStyle = computed(() => {
  if (!props.collapsed) return 'none';
  return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    ? '1px solid rgb(142, 142, 142)'  // dark
    : '1px solid rgb(99, 99, 99)';     // light
});
</script>

<template>
  <div
    :style="{ borderBottom: borderStyle }"
  class="px-4 py-2 bg-linear-to-br from-[#eeeeee] to-[#fffbfb] dark:from-[#232422] dark:to-[#1b2132] rounded-lg   flex justify-between items-center"
  >
    <!-- Left: title + count + loading -->
    <div class="flex items-center gap-3">
      <h3 class="draggable-handle cursor-move text-sm font-semibold text-gray-700 dark:text-white">
        {{ title }}
      </h3>
      <span v-if="activeCount > 0"
            class="inline-flex items-center justify-center p-1 w-auto h-5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full">
        {{ activeCount }}
      </span>

      <div v-if="loading" class="flex gap-1">
        <span class="w-1 h-1 bg-gray-700 rounded-full animate-bounce delay-0"></span>
        <span class="w-1 h-1 bg-gray-700 rounded-full animate-bounce delay-200"></span>
        <span class="w-1 h-1 bg-gray-700 rounded-full animate-bounce delay-400"></span>
      </div>
    </div>

    <!-- Right: clear + arrow -->
    <div class="flex items-center gap-3">
      <button v-if="showClear && activeCount > 0" @click="onClear"
              class="text-xs text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition">
        Clear
      </button>

      <svg @click="onToggle"
           :class="{ 'rotate-180': collapsed }"
           class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform cursor-pointer"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </div>
</template>

<style scoped>
.animate-bounce {
  animation: bounce 0.6s infinite ease-in-out;
}
.delay-0 { animation-delay: 0s; }
.delay-200 { animation-delay: 0.2s; }
.delay-400 { animation-delay: 0.4s; }

@keyframes bounce {
  0%, 80%, 100% { transform: scale(1); opacity: 0.3; }
  40% { transform: scale(1.2); opacity: 1; }
}

#lightBorder{
    border-bottom: rgb(99, 99, 99) solid 1px;
}
#darkBorder{
    border-bottom: rgb(142, 142, 142) solid 1px;

}
#noneBorder{
    border-bottom: none;

}

</style>
