<script setup>
import { computed, onMounted } from 'vue';

const props = defineProps({
  tasks: { type: Array, default: () => [] },
  showAll: { type: Boolean, default: false },
  statusCounts: { type: Object, default: () => ({}) },      // paginated counts
  allStatusCounts: { type: Object, default: () => ({}) },   // total counts
});

const emit = defineEmits(['toggle-show-all']);

// Current counts (top counters)
const counts = computed(() => props.showAll ? props.allStatusCounts : props.statusCounts);

// Total tasks
const totalTasks = computed(() =>
  props.allStatusCounts.done + props.allStatusCounts.in_progress + props.allStatusCounts.pending
);
</script>

<template>
  <div class="border-b dark:border-0 border-gray-200 px-4 sm:px-6 py-3 sm:py-4">

    <!-- Header + toggle -->
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Tasks List</h2>
      <button
        @click="emit('toggle-show-all')"
        class="group flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200"
        :class="showAll
          ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30'
          : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700'">
        <span class="flex items-center gap-2">
          <span>{{ showAll ? 'Show Pages' : 'Enable Sorting' }}</span>
          <svg v-if="showAll" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
          </svg>
        </span>
      </button>
    </div>

    <!-- Status counters -->
    <div class="flex justify-center flex-wrap gap-2 mt-2 text-xs text-gray-500">
      <span class="px-2 py-0.5 rounded-full font-semibold bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300">
        Done: {{ counts.done }}
      </span>
      <span class="px-2 py-0.5 rounded-full font-semibold bg-yellow-100 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-300">
        In Progress: {{ counts.in_progress }}
      </span>
      <span class="px-2 py-0.5 rounded-full font-semibold bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-300">
        Pending: {{ counts.pending }}
      </span>
    </div>

    <!-- Bottom card -->
    <div class="mt-3 inline-block w-full bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800 rounded-lg p-2 text-center shadow-sm text-xs">
      <p v-if="showAll" class="text-green-700 dark:text-green-300 font-medium">
        Drag & drop enabled - All tasks loaded
      </p>
      <p v-else class="text-gray-500 dark:text-gray-400 font-medium">
        Total Tasks: {{ totalTasks }}
      </p>
    </div>
  </div>
</template>
