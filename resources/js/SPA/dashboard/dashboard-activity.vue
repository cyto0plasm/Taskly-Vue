<script setup>
import { computed } from 'vue';
import { useDashboardStore } from '../store/dashboard-store';

const store = useDashboardStore();

// ── Helpers ────────────────────────────────────────────────────────────────
const PROJECT_ICON = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
const TASK_ICON    = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';

const STATUS_STYLE = {
    done:        { dot: 'bg-emerald-400', label: 'Done',        text: 'text-emerald-500' },
    in_progress: { dot: 'bg-indigo-400',  label: 'In progress', text: 'text-indigo-500'  },
    pending:     { dot: 'bg-gray-300',    label: 'Pending',     text: 'text-gray-400'    },
};

function statusStyle(s)  { return STATUS_STYLE[s] ?? STATUS_STYLE.pending; }
function icon(type)      { return type === 'project' ? PROJECT_ICON : TASK_ICON; }
function routeFor(item)  { return `/${item.type}s/${item.id}`; }

function timeAgo(rawDate) {
    const diff = Date.now() - new Date(rawDate).getTime();
    const mins  = Math.floor(diff / 60_000);
    const hours = Math.floor(diff / 3_600_000);
    const days  = Math.floor(diff / 86_400_000);
    if (mins  < 1)  return 'just now';
    if (mins  < 60) return `${mins}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days  < 7)  return `${days}d ago`;
    return new Date(rawDate).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

const items    = computed(() => store.recentActivity);
const isLoading = computed(() => store.loadingWidgets);
</script>

<template>
    <div class="bg-white dark:bg-[#1a1b1a]
                ring-1 ring-gray-200 dark:ring-gray-700
                rounded-2xl shadow-sm px-5 py-4 flex flex-col gap-3">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-semibold uppercase tracking-wider
                      text-gray-400 dark:text-gray-500">
                Recent Activity
            </p>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Skeleton -->
        <template v-if="isLoading">
            <div v-for="i in 5" :key="i"
                 class="flex items-center gap-3 animate-pulse">
                <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-800 shrink-0" />
                <div class="flex-1 space-y-1.5">
                    <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-3/4" />
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-1/3" />
                </div>
            </div>
        </template>

        <!-- Empty -->
        <div v-else-if="items.length === 0"
             class="text-center py-4 text-xs text-gray-400 dark:text-gray-500">
            No recent activity yet.
        </div>

        <!-- List -->
        <template v-else>
            <a v-for="item in items" :key="item.type + item.id"
               :href="routeFor(item)"
               class="flex items-center gap-3 group hover:bg-gray-50 dark:hover:bg-gray-800/50
                      rounded-lg px-1.5 py-1 -mx-1.5 transition-colors duration-150">

                <!-- Icon chip -->
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                            bg-gray-100 dark:bg-gray-800
                            group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/40
                            transition-colors duration-150">
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-indigo-500
                                transition-colors duration-150"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="icon(item.type)" />
                    </svg>
                </div>

                <!-- Name + meta -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate
                              group-hover:text-indigo-600 dark:group-hover:text-indigo-400
                              transition-colors duration-150">
                        {{ item.name }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span :class="[statusStyle(item.status).dot,
                                       'w-1.5 h-1.5 rounded-full shrink-0']" />
                        <span :class="[statusStyle(item.status).text,
                                       'text-[10px] font-medium']">
                            {{ statusStyle(item.status).label }}
                        </span>
                        <span class="text-[10px] text-gray-300 dark:text-gray-600">·</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 capitalize">
                            {{ item.type }}
                        </span>
                    </div>
                </div>

                <!-- Time -->
                <span class="text-[10px] text-gray-400 dark:text-gray-500 shrink-0 tabular-nums">
                    {{ timeAgo(item.updated_at) }}
                </span>
            </a>
        </template>
    </div>
</template>
