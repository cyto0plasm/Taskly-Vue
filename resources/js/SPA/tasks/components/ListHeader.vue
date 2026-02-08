<script setup>
import { computed, onMounted } from 'vue';

const props = defineProps({
      isLoadMoreMode: { type: Boolean, default: false },
  statusCounts: { type: Object, default: () => ({}) },      // current page status counts in page mode
  allStatusCounts: { type: Object, default: () => ({}) },   // all tasks status counts in load more mode
  totalTasksCount: { type: Number, default: 0 }  ,// total tasks
  loadedCount: {type:Number,default: 0}          // total tasks loaded
});

const emit = defineEmits(['toggle-show-all']);


const visibleStatusCounts = computed(() => {
  return props.isLoadMoreMode
    ? props.allStatusCounts
    : props.statusCounts;
});

const visibleTotal = computed(() =>
  props.isLoadMoreMode
    ? props.loadedCount
    : props.totalTasksCount
);




const buttonConfig = computed(() => ({
  label: props.isLoadMoreMode ? 'Show Pages' : 'Enable Sorting',
  tooltip: props.isLoadMoreMode ? 'View all pages ' : 'Enable Sort Mode',
}));

</script>

<template>
  <div class="bg-[#FAFAFA] dark:bg-[#222321] px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-sm">

    <!-- Top: Title + toggle button -->
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-white">Tasks List</h2>

      <button
        @click="emit('toggle-show-all')"
        :title="buttonConfig.tooltip"
        class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200
          border
          hover:scale-105
          dark:focus:outline-none
          focus:outline-none"
        :class="isLoadMoreMode
          ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800'
          : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700'">
        <span>{{ buttonConfig.label }}</span>
        <svg v-if="isLoadMoreMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
        </svg>
      </button>
    </div>

    <!-- Status counters (centered pills) -->
    <div class="flex flex-wrap justify-center gap-2 text-xs mt-2">
      <span class="px-2 py-0.5 rounded-full font-semibold bg-green-100 dark:bg-green-800">Done: {{ visibleStatusCounts.done }}</span>
      <span class="px-2 py-0.5 rounded-full font-semibold bg-yellow-100 dark:bg-yellow-800">In Progress: {{ visibleStatusCounts.in_progress }}</span>
      <span class="px-2 py-0.5 rounded-full font-semibold bg-red-100 dark:bg-red-800">Pending: {{ visibleStatusCounts.pending }}</span>
    </div>

    <!-- Bottom info card -->
    <div class="mt-3 w-full bg-gray-100 dark:bg-[#222321] border border-gray-200 dark:border-gray-700 rounded-lg p-2 text-center text-xs font-medium">
      <p v-if="isLoadMoreMode" class="text-green-700 dark:text-green-300">
        Drag & drop enabled – Showing {{ visibleTotal }} of {{ totalTasksCount }} tasks
      </p>
      <p v-else class="text-gray-600 dark:text-gray-400">
        Total Tasks: {{ totalTasksCount }}
      </p>
    </div>

  </div>
</template>
