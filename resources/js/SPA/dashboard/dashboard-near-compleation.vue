<script setup>
import { computed } from 'vue';
import { useDashboardStore } from '../store/dashboard-store';

const store = useDashboardStore();

const items     = computed(() => store.nearCompletion);
const isLoading = computed(() => store.loadingWidgets);

// Colour the progress bar based on how close to done
function barColor(pct) {
    if (pct >= 95) return 'bg-emerald-400';
    if (pct >= 90) return 'bg-teal-400';
    return 'bg-indigo-400';
}

function dueDateLabel(dateStr) {
    if (!dateStr) return null;
    const d    = new Date(dateStr);
    const diff = Math.ceil((d - Date.now()) / 86_400_000);
    if (diff < 0)  return { text: `${Math.abs(diff)}d overdue`, cls: 'text-red-500' };
    if (diff === 0) return { text: 'Due today',                  cls: 'text-orange-500' };
    if (diff === 1) return { text: 'Due tomorrow',               cls: 'text-amber-500' };
    return { text: `${diff}d left`, cls: 'text-gray-400 dark:text-gray-500' };
}
</script>

<template>
    <div class="bg-white dark:bg-[#1a1b1a]
                ring-1 ring-gray-200 dark:ring-gray-700
                rounded-2xl shadow-sm px-5 py-4 flex flex-col gap-3">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Near Completion
                </p>
                <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5">
                    Projects at 80 %+ — finish them first
                </p>
            </div>
            <!-- Trophy icon -->
            <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2a1 1 0 01.894.553l2.382 4.826 5.327.774a1 1 0 01.555 1.705l-3.854 3.757.91 5.307a1 1 0 01-1.451 1.054L12 17.347l-4.763 2.504a1 1 0 01-1.451-1.054l.91-5.307L2.842 9.858a1 1 0 01.555-1.705l5.327-.774L11.106 2.553A1 1 0 0112 2z"/>
            </svg>
        </div>

        <!-- Skeleton -->
        <template v-if="isLoading">
            <div v-for="i in 3" :key="i" class="animate-pulse space-y-2">
                <div class="flex justify-between">
                    <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-1/2" />
                    <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-8" />
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full" />
            </div>
        </template>

        <!-- Empty -->
        <div v-else-if="items.length === 0"
             class="text-center py-4 text-xs text-gray-400 dark:text-gray-500">
            No projects near completion yet.
        </div>

        <!-- List -->
        <template v-else>
            <a v-for="p in items" :key="p.id"
               :href="`/projects/${p.id}`"
               class="group block hover:bg-gray-50 dark:hover:bg-gray-800/50
                      rounded-lg px-1.5 py-1.5 -mx-1.5 transition-colors duration-150">

                <!-- Name row -->
                <div class="flex items-start justify-between gap-2 mb-1.5">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate
                              group-hover:text-indigo-600 dark:group-hover:text-indigo-400
                              transition-colors duration-150 leading-tight">
                        {{ p.name }}
                    </p>
                    <span class="text-sm font-bold shrink-0"
                          :class="barColor(p.completion_pct).replace('bg-', 'text-')">
                        {{ p.completion_pct }}%
                    </span>
                </div>

                <!-- Progress bar -->
                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden mb-1.5">
                    <div :class="[barColor(p.completion_pct),
                                  'h-full rounded-full transition-all duration-700']"
                         :style="{ width: p.completion_pct + '%' }" />
                </div>

                <!-- Meta row -->
                <div class="flex items-center justify-between">
                    <!-- Tasks left badge -->
                    <span class="inline-flex items-center gap-1 text-[10px] font-medium
                                 text-gray-500 dark:text-gray-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                        {{ p.tasks_left }} task{{ p.tasks_left !== 1 ? 's' : '' }} left
                    </span>

                    <!-- Due date -->
                    <span v-if="p.end_date"
                          class="text-[10px] font-medium"
                          :class="dueDateLabel(p.end_date)?.cls">
                        {{ dueDateLabel(p.end_date)?.text }}
                    </span>
                </div>
            </a>
        </template>
    </div>
</template>
