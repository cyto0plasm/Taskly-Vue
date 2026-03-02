<script setup>
import { ref, computed } from 'vue';
import { route }         from 'ziggy-js';
import { useDashboardStore } from '../store/dashboard-store.js';
import {  useTaskStore } from '../store/task-store';
import {  useProjectStore } from '../store/project-store';

const dashboardStore = useDashboardStore();
const taskStore = useTaskStore();
const projectStore = useProjectStore();

// ── Active tab ─────────────────────────────────────────────────────────────
// 'activity' | 'nearDone' | 'overdue'
const activeTab = ref('activity');

// Auto-switch to overdue tab if there are overdue items and nothing recent
// (only on first load, not on every reactive change)
if (!dashboardStore.recentActivity.length && dashboardStore.overdueItems.length) {
    activeTab.value = 'overdue';
}

// ── Routing — use Ziggy so it respects your actual Laravel named routes ────
// Adjust route names here if yours differ
function linkTo(item) {
    try {
        return item.type === 'project'
            ? route('projects.index', item.id)
            : route('tasks.index',    item.id);
    } catch {
        // Fallback if Ziggy isn't available in this context
        return `/${item.type}s/${item.id}`;
    }
}
function LinkAndselect(item) {
    if (item.type === 'project') {
        window.location.href = route('projects.index', item.id);
        projectStore.selectedProjectId = item.id;
        projectStore.selectProject(item.id);
    } else {
        taskStore.selectedTaskId = item.id;
        window.location.href = route('tasks.index', item.id);
        taskStore.selectTask(item.id);
    }
}

// ── Shared icons ───────────────────────────────────────────────────────────
const ICONS = {
    project: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
    task:    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
};

// ── Tab config ─────────────────────────────────────────────────────────────
const tabs = computed(() => [
    {
        key:   'activity',
        label: 'Recent',
        count: dashboardStore.recentActivity.length,
        dot:   false,
    },
    {
        key:   'nearDone',
        label: 'Near Done',
        count: dashboardStore.nearCompletion.length,
        dot:   false,
    },
    {
        key:   'overdue',
        label: 'Overdue',
        count: dashboardStore.overdueItems.length,
        dot:   dashboardStore.overdueItems.length > 0,
    },
]);

const isLoading = computed(() => dashboardStore.loadingWidgets);

// ── Activity helpers ───────────────────────────────────────────────────────
const STATUS = {
    done:        { dot: 'bg-emerald-400', label: 'Done',        color: 'text-emerald-500' },
    in_progress: { dot: 'bg-indigo-400',  label: 'In progress', color: 'text-indigo-500'  },
    pending:     { dot: 'bg-gray-300',    label: 'Pending',     color: 'text-gray-400'    },
};
function statusOf(s)  { return STATUS[s] ?? STATUS.pending; }

function timeAgo(raw) {
    const diff  = Date.now() - new Date(raw).getTime();
    const mins  = Math.floor(diff / 60_000);
    const hours = Math.floor(diff / 3_600_000);
    const days  = Math.floor(diff / 86_400_000);
    if (mins  < 1)  return 'just now';
    if (mins  < 60) return `${mins}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days  === 1) return 'yesterday';
    if (days  < 7)  return `${days}d ago`;
    return new Date(raw).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

// ── Near-done helpers ──────────────────────────────────────────────────────
function barColor(pct) {
    if (pct >= 95) return 'bg-emerald-400';
    if (pct >= 88) return 'bg-teal-400';
    return 'bg-indigo-400';
}
function barTextColor(pct) {
    if (pct >= 95) return 'text-emerald-500';
    if (pct >= 88) return 'text-teal-500';
    return 'text-indigo-500';
}
function dueSoon(dateStr) {
    if (!dateStr) return null;
    const days = Math.ceil((new Date(dateStr) - Date.now()) / 86_400_000);
    if (days < 0)  return { text: `${Math.abs(days)}d overdue`, cls: 'text-red-500' };
    if (days === 0) return { text: 'Due today',                  cls: 'text-orange-500' };
    if (days === 1) return { text: 'Due tomorrow',               cls: 'text-amber-500' };
    if (days <= 7)  return { text: `${days}d left`,              cls: 'text-gray-400' };
    return null;
}

// ── Overdue helpers ────────────────────────────────────────────────────────
const overdueFilter = ref('all'); // 'all' | 'project' | 'task'

const overdueFiltered = computed(() => {
    const all = dashboardStore.overdueItems;
    if (overdueFilter.value === 'all') return all;
    return all.filter(i => i.type === overdueFilter.value);
});

function severityBadge(days) {
    if (days >= 7) return 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400';
    if (days >= 3) return 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400';
    return              'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
}
</script>

<template>
    <div class="group overflow-hidden bg-white dark:bg-[#1a1b1a] ring-1 ring-gray-200 dark:ring-gray-700
                rounded-2xl shadow-sm flex flex-col ">

                <div  class="h-1 rounded-2xl translate-x-full group-hover:translate-x-0 transition-all duration-600 ease-out      bg-linear-to-r from-indigo-500 via-violet-500 to-teal-500 dark:from-indigo-300 dark:via-violet-300 dark:to-teal-300" />
        <!-- ── Tab bar ──────────────────────────────────────────────────── -->
        <div class="flex items-center gap-0.5 px-3 pt-3 border-b
                    border-gray-100 dark:border-gray-800">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                class="relative flex items-center gap-1.5 px-3 py-2 text-xs font-semibold
                       transition-colors duration-150 rounded-t-lg"
                :class="activeTab === tab.key
                    ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            >
                <!-- Pulsing dot for overdue -->
                <span v-if="tab.dot" class="relative flex h-1.5 w-1.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full
                                 rounded-full bg-red-400 opacity-75" />
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-red-500" />
                </span>

                {{ tab.label }}

                <!-- Count pill -->
                <span v-if="tab.count > 0"
                      class="px-1.5 py-0.5 rounded-full text-[9px] font-bold leading-none"
                      :class="activeTab === tab.key
                          ? 'bg-indigo-200 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-300'
                          : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'">
                    {{ tab.count }}
                </span>

                <!-- Active underline -->
                <span v-if="activeTab === tab.key"
                      class="absolute bottom-0 left-0 right-0 h-0.5
                             bg-indigo-500 dark:bg-indigo-400 rounded-t-full" />
            </button>
        </div>

        <!-- ── Content ──────────────────────────────────────────────────── -->
        <div class="px-4 py-3 flex flex-col gap-1
                    max-h-60 overflow-y-auto
                    scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700">

            <!-- ════ SKELETON ════ -->
            <template v-if="isLoading">
                <div v-for="i in 4" :key="i"
                     class="flex items-center gap-3 py-1.5 animate-pulse">
                    <div class="w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-800 shrink-0" />
                    <div class="flex-1 space-y-1.5">
                        <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-3/4" />
                        <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-1/3" />
                    </div>
                    <div class="h-4 w-10 bg-gray-100 dark:bg-gray-800 rounded-full" />
                </div>
            </template>

            <!-- ════ ACTIVITY TAB ════ -->
            <template v-else-if="activeTab === 'activity'">
                <div v-if="!dashboardStore.recentActivity.length"
                     class="py-8 text-center text-xs text-gray-400 dark:text-gray-500">
                    No recent activity yet.
                </div>

                <a v-for="item in dashboardStore.recentActivity"
                   :key="item.type + item.id"
                   @click="LinkAndselect(item)"
                   class="flex items-center gap-3 py-1.5 px-2 -mx-2 rounded-lg group
                          hover:bg-gray-50 dark:hover:bg-gray-800/60
                          transition-colors duration-150">

                    <!-- Icon -->
                    <div class="w-6 h-6 rounded-md shrink-0 flex items-center justify-center
                                bg-gray-100 dark:bg-gray-800
                                group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/40
                                transition-colors">
                        <svg class="w-3 h-3 text-gray-400 group-hover:text-indigo-500 transition-colors"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  :d="ICONS[item.type]" />
                        </svg>
                    </div>

                    <!-- Text -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate
                                  group-hover:text-indigo-600 dark:group-hover:text-indigo-400
                                  transition-colors">
                            {{ item.name }}
                        </p>
                        <div class="flex items-center gap-1 mt-0.5">
                            <span :class="[statusOf(item.status).dot,
                                           'w-1.5 h-1.5 rounded-full shrink-0']" />
                            <span :class="[statusOf(item.status).color, 'text-[10px] font-medium']">
                                {{ statusOf(item.status).label }}
                            </span>
                            <span class="text-[10px] text-gray-300 dark:text-gray-700">·</span>
                            <span class="text-[10px] text-gray-400 capitalize">{{ item.type }}</span>
                        </div>
                    </div>

                    <!-- Time -->
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 shrink-0 tabular-nums">
                        {{ timeAgo(item.updated_at) }}
                    </span>
                </a>
            </template>

            <!-- ════ NEAR DONE TAB ════ -->
            <template v-else-if="activeTab === 'nearDone'">
                <div v-if="!dashboardStore.nearCompletion.length"
                     class="py-8 text-center text-xs text-gray-400 dark:text-gray-500">
                    No projects at 80%+ yet.
                </div>

                <a v-for="p in dashboardStore.nearCompletion"
                   :key="p.id"
                   :href="route('projects.index', p.id)"
                   class="py-2 px-2 -mx-2 rounded-lg group block
                          hover:bg-gray-50 dark:hover:bg-gray-800/60
                          transition-colors duration-150">

                    <!-- Name + % -->
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate
                                  group-hover:text-indigo-600 dark:group-hover:text-indigo-400
                                  transition-colors leading-tight">
                            {{ p.name }}
                        </p>
                        <span class="text-xs font-bold shrink-0"
                              :class="barTextColor(p.completion_pct)">
                            {{ p.completion_pct }}%
                        </span>
                    </div>

                    <!-- Bar -->
                    <div class="h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden mb-1.5">
                        <div :class="[barColor(p.completion_pct),
                                      'h-full rounded-full transition-all duration-700']"
                             :style="{ width: p.completion_pct + '%' }" />
                    </div>

                    <!-- Meta -->
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">
                            {{ p.tasks_left }} task{{ p.tasks_left !== 1 ? 's' : '' }} left
                        </span>
                        <span v-if="dueSoon(p.end_date)"
                              class="text-[10px] font-medium"
                              :class="dueSoon(p.end_date).cls">
                            {{ dueSoon(p.end_date).text }}
                        </span>
                    </div>
                </a>
            </template>

            <!-- ════ OVERDUE TAB ════ -->
            <template v-else-if="activeTab === 'overdue'">

                <!-- Sub-filter pills -->
                <div class="flex items-center gap-1.5 pb-2 border-b
                            border-gray-100 dark:border-gray-800 mb-1">
                    <button
                        v-for="f in [
                            { key: 'all',     label: 'All',      n: dashboardStore.overdueItems.length },
                            { key: 'project', label: 'Projects', n: dashboardStore.overdueItems.filter(i => i.type==='project').length },
                            { key: 'task',    label: 'Tasks',    n: dashboardStore.overdueItems.filter(i => i.type==='task').length },
                        ]"
                        :key="f.key"
                        @click="overdueFilter = f.key"
                        class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                               font-semibold transition-all duration-150"
                        :class="overdueFilter === f.key
                            ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                            : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                    >
                        {{ f.label }}
                        <span class="text-[9px] font-bold opacity-70">{{ f.n }}</span>
                    </button>
                </div>

                <div v-if="!overdueFiltered.length"
                     class="py-8 text-center text-xs text-gray-400 dark:text-gray-500">
                    🎉 Nothing overdue here.
                </div>

                <a v-for="item in overdueFiltered"
                   :key="item.type + item.id"
                   @click="LinkAndselect(item)"
                   class="flex items-center gap-3 py-1.5 px-2 -mx-2 rounded-lg group
                          hover:bg-gray-50 dark:hover:bg-gray-800/60
                          transition-colors duration-150">

                    <!-- Icon -->
                    <div class="w-6 h-6 rounded-md shrink-0 flex items-center justify-center
                                bg-gray-100 dark:bg-gray-800
                                group-hover:bg-red-50 dark:group-hover:bg-red-900/20
                                transition-colors">
                        <svg class="w-3 h-3 text-gray-400 group-hover:text-red-500 transition-colors"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  :d="ICONS[item.type]" />
                        </svg>
                    </div>

                    <!-- Name + type -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate
                                  group-hover:text-red-600 dark:group-hover:text-red-400
                                  transition-colors">
                            {{ item.name }}
                        </p>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 capitalize">
                            {{ item.type }}
                        </span>
                    </div>

                    <!-- Days late badge -->
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold"
                          :class="severityBadge(item.days_late)">
                        {{ item.days_late }}d late
                    </span>
                </a>
            </template>

        </div>
    </div>
</template>

<style scoped>
.scrollbar-thin { scrollbar-width: thin; }
.scrollbar-thin::-webkit-scrollbar       { width: 3px; }
.scrollbar-thin::-webkit-scrollbar-thumb { border-radius: 9999px; background: #e5e7eb; }
</style>
