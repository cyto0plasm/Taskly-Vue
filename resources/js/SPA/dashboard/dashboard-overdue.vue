<script setup>
import { ref, computed } from 'vue';
import { useDashboardStore } from '../store/dashboard-store';

const store = useDashboardStore();

// ── Filter state ───────────────────────────────────────────────────────────
// 'all' | 'project' | 'task'
const filter = ref('all');

const allItems  = computed(() => store.overdueItems);
const isLoading = computed(() => store.loadingWidgets);

const filtered = computed(() =>
    filter.value === 'all'
        ? allItems.value
        : allItems.value.filter(i => i.type === filter.value)
);

const projectCount = computed(() => allItems.value.filter(i => i.type === 'project').length);
const taskCount    = computed(() => allItems.value.filter(i => i.type === 'task').length);

// ── Helpers ────────────────────────────────────────────────────────────────
const PROJECT_ICON = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
const TASK_ICON    = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';
function icon(type) { return type === 'project' ? PROJECT_ICON : TASK_ICON; }
function routeFor(item) { return `/${item.type}s/${item.id}`; }

// Severity colour: red > 7d, orange 3–7d, amber < 3d
function severityClass(days) {
    if (days >= 7) return { dot: 'bg-red-500',    text: 'text-red-500',    badge: 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' };
    if (days >= 3) return { dot: 'bg-orange-400', text: 'text-orange-500', badge: 'bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400' };
    return              { dot: 'bg-amber-400',  text: 'text-amber-500',  badge: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' };
}

const STATUS_LABEL = {
    pending:     'Not started',
    in_progress: 'In progress',
};
</script>

<template>
    <div class="bg-white dark:bg-[#1a1b1a]
                ring-1 ring-gray-200 dark:ring-gray-700
                rounded-2xl shadow-sm px-5 py-4 flex flex-col gap-3">

        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Overdue
                </p>
                <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5">
                    Sorted by most days late
                </p>
            </div>
            <!-- Alert icon -->
            <span v-if="allItems.length > 0"
                  class="flex items-center gap-1.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full
                                 rounded-full bg-red-400 opacity-75" />
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500" />
                </span>
                <span class="text-[10px] font-semibold text-red-500">
                    {{ allItems.length }} overdue
                </span>
            </span>
            <span v-else class="text-[10px] font-semibold text-emerald-500">All clear ✓</span>
        </div>

        <!-- Filter tabs -->
        <div class="flex items-center gap-1.5">
            <button
                v-for="tab in [
                    { key: 'all',     label: 'All',      count: allItems.length  },
                    { key: 'project', label: 'Projects', count: projectCount     },
                    { key: 'task',    label: 'Tasks',    count: taskCount        },
                ]"
                :key="tab.key"
                @click="filter = tab.key"
                class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold
                       transition-all duration-150"
                :class="filter === tab.key
                    ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 ring-1 ring-indigo-200 dark:ring-indigo-700'
                    : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
            >
                {{ tab.label }}
                <span class="px-1 py-0.5 rounded-full text-[9px] font-bold leading-none"
                      :class="filter === tab.key
                          ? 'bg-indigo-200 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-300'
                          : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'">
                    {{ tab.count }}
                </span>
            </button>
        </div>

        <!-- Skeleton -->
        <template v-if="isLoading">
            <div v-for="i in 4" :key="i"
                 class="flex items-center gap-3 animate-pulse">
                <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-800 shrink-0" />
                <div class="flex-1 space-y-1.5">
                    <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-2/3" />
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-1/4" />
                </div>
                <div class="h-5 w-14 bg-gray-100 dark:bg-gray-800 rounded-full" />
            </div>
        </template>

        <!-- Empty -->
        <div v-else-if="filtered.length === 0"
             class="text-center py-5 text-xs text-gray-400 dark:text-gray-500">
            <template v-if="allItems.length === 0">
                🎉 Nothing overdue — great work!
            </template>
            <template v-else>
                No {{ filter === 'project' ? 'projects' : 'tasks' }} overdue.
            </template>
        </div>

        <!-- Scrollable list -->
        <div v-else class="flex flex-col gap-1 max-h-[280px] overflow-y-auto -mx-1.5 px-1.5
                           scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700">
            <a v-for="item in filtered" :key="item.type + item.id"
               :href="routeFor(item)"
               class="flex items-center gap-3 group
                      hover:bg-gray-50 dark:hover:bg-gray-800/50
                      rounded-lg px-1.5 py-1.5 transition-colors duration-150">

                <!-- Icon chip -->
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                            bg-gray-100 dark:bg-gray-800
                            group-hover:bg-red-50 dark:group-hover:bg-red-900/20
                            transition-colors duration-150">
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-red-500
                                transition-colors duration-150"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="icon(item.type)" />
                    </svg>
                </div>

                <!-- Name + status -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate
                              group-hover:text-red-600 dark:group-hover:text-red-400
                              transition-colors duration-150">
                        {{ item.name }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 capitalize">
                            {{ item.type }}
                        </span>
                        <span class="text-gray-200 dark:text-gray-700 text-[10px]">·</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">
                            {{ STATUS_LABEL[item.status] ?? item.status }}
                        </span>
                    </div>
                </div>

                <!-- Days late badge -->
                <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold"
                      :class="severityClass(item.days_late).badge">
                    {{ item.days_late }}d late
                </span>
            </a>
        </div>

    </div>
</template>

<style scoped>
.scrollbar-thin { scrollbar-width: thin; }
.scrollbar-thin::-webkit-scrollbar { width: 4px; }
.scrollbar-thin::-webkit-scrollbar-thumb { border-radius: 9999px; }
</style>
