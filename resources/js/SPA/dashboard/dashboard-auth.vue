<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import Card from "./card.vue";
import DualCard from "./dual-card.vue";
import CardsMenuSvg from "./cards-menu-svg.vue";
import { route } from 'ziggy-js';
import { useDashboardStore } from "../store/dashboard-store";
import DashboardActivity from "./dashboard-activity.vue";
import DashboardNearCompleation from "./dashboard-near-compleation.vue";
import DashboardOverdue from "./dashboard-overdue.vue";
import DashboardWidgets from "./dashboard-widgets.vue";
import DashboardProfile from "./dashboard-profile.vue";
import Shape from "../components/canvas/svg/shape.vue";
import  "../../utils/toolTip.js";
const dashboardStore = useDashboardStore();


const showCards = ref(false);
const selectedType = ref("projects");
const showToolBar = ref(false);

const scrollContainer = ref(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(false);

onMounted(() => {
    dashboardStore.loadDashboardData();
});

const menuItems = [
    { value: "projects", label: "Projects"   },
    { value: "tasks",    label: "Tasks"      },
];

// ── Summary stats ─────────────────────────────────────────────────────────────
const totalItems     = computed(() => (dashboardStore.projectsCount ?? 0) + (dashboardStore.tasksCount ?? 0));
const completedItems = computed(() => (dashboardStore.completedProjects ?? 0) + (dashboardStore.completedTasks ?? 0));
const completionRate = computed(() => {
    if (totalItems.value === 0) return 0;
    return Math.round((completedItems.value / totalItems.value) * 100);
});

const CIRCUMFERENCE = 2 * Math.PI * 30;
const ringOffset = computed(() => CIRCUMFERENCE - (completionRate.value / 100) * CIRCUMFERENCE);
const ringColor  = computed(() => {
    if (completionRate.value >= 75) return '#10b981';
    if (completionRate.value >= 40) return '#f59e0b';
    return '#ef4444';
});
const completionLabel = computed(() => {
    if (totalItems.value === 0)       return 'No work added yet — start a project!';
    if (completionRate.value === 100) return 'Everything done. Excellent work! 🎉';
    if (completionRate.value >= 75)   return 'Almost there — keep pushing!';
    if (completionRate.value >= 40)   return 'Good progress, stay consistent.';
    return 'Early days — build that momentum.';
});
const completionDescription = "the percintage calculation is based on your pending Tasks/Projects";

const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Good morning';
    if (h < 18) return 'Good afternoon';
    return 'Good evening';
});

// Deadline urgency tiers (overdueCount, dueTodayCount, dueTomorrowCount added to store later)
const overdueCount     = computed(() => dashboardStore.overdueCount      ?? 0);
const dueTodayCount    = computed(() => dashboardStore.dueTodayCount     ?? 0);
const dueTomorrowCount = computed(() => dashboardStore.dueTomorrowCount  ?? 0);
const dueThisWeek      = computed(() => (dashboardStore.upcomingProjectDeadlines ?? 0) + (dashboardStore.upcomingTaskDeadlines ?? 0));
const hasUrgent        = computed(() => overdueCount.value > 0 || dueTodayCount.value > 0);

// Workload
const activeItems   = computed(() => (dashboardStore.projectsProgress ?? 0) + (dashboardStore.tasksProgress ?? 0));
const pendingItems  = computed(() => (dashboardStore.pendingProjects   ?? 0) + (dashboardStore.pendingTasks  ?? 0));
const backlogBig    = computed(() => pendingItems.value > activeItems.value * 2);

// ── Home cards ─────────────────────────────────────────────────────────────────
const homeCards = computed(() => [
    {
        type: "total", title: "Total Overview", description: "Projects vs Tasks", color: "indigo", dual: true,
        left:  { label: "Projects", value: dashboardStore.projectsCount,            route: "projects.index" },
        right: { label: "Tasks",    value: dashboardStore.tasksCount,               route: "tasks.index"    },
    },
    {
        type: "deadlines", title: "Upcoming Deadlines", description: "Next 7 days", color: "red-dark", dual: true,
        left:  { label: "Projects", value: dashboardStore.upcomingProjectDeadlines, route: "projects.index" },
        right: { label: "Tasks",    value: dashboardStore.upcomingTaskDeadlines,    route: "tasks.index"    },
    },
    {
        type: "completed", title: "Completed", description: "Finished items", color: "green", dual: true,
        left:  { label: "Projects", value: dashboardStore.completedProjects,        route: "projects.index" },
        right: { label: "Tasks",    value: dashboardStore.completedTasks,           route: "tasks.index"    },
    },
    {
        type: "progress", title: "In Progress", description: "Currently active", color: "yellow", dual: true,
        left:  { label: "Projects", value: dashboardStore.projectsProgress,         route: "projects.index" },
        right: { label: "Tasks",    value: dashboardStore.tasksProgress,            route: "tasks.index"    },
    },
    {
        type: "pending", title: "Pending", description: "Not started yet", color: "red", dual: true,
        left:  { label: "Projects", value: dashboardStore.pendingProjects,          route: "projects.index" },
        right: { label: "Tasks",    value: dashboardStore.pendingTasks,             route: "tasks.index"    },
    },
]);

const projectCards = computed(() => [
    { type: "projects",          title: "Total Projects",   description: "All projects you’re managing",     number: dashboardStore.projectsCount,            color: "indigo",   route: "projects.index" },
    { type: "deadlines-projects",title: "Deadlines",        description: "Projects with upcoming deadlines", number: dashboardStore.upcomingProjectDeadlines,  color: "red-dark", route: "projects.index" },
    { type: "completed-projects",title: "Completed",        description: "Projects successfully finished",         number: dashboardStore.completedProjects,         color: "green",    route: "projects.index" },
    { type: "progress-projects", title: "Progress",         description: "Projects currently working on",   number: dashboardStore.projectsProgress,         color: "yellow",   route: "projects.index" },
    { type: "pending-projects",  title: "Pending",          description: "Projects not yet started",           number: dashboardStore.pendingProjects,           color: "red",      route: "projects.index" },
]);

const taskCards = computed(() => [
    { type: "tasks",             title: "Total Tasks",      description: "All tasks you’re currently managing",        number: dashboardStore.tasksCount,               color: "teal",     route: "tasks.index" },
    { type: "deadlines-tasks",   title: "Deadlines",        description: "Tasks with upcoming deadlines",    number: dashboardStore.upcomingTaskDeadlines,    color: "red-dark", route: "tasks.index" },
    { type: "completed-tasks",   title: "Completed",        description: "Tasks successfully finished",            number: dashboardStore.completedTasks,           color: "green",    route: "tasks.index" },
    { type: "progress-tasks",    title: "Progress",         description: "Tasks currently working on",      number: dashboardStore.tasksProgress,            color: "yellow",   route: "tasks.index" },
    { type: "pending-tasks",     title: "Pending",          description: "Tasks not yet started",              number: dashboardStore.pendingTasks,             color: "red",      route: "tasks.index" },
]);

const visibleCards = computed(() => {
    if (selectedType.value === "home")     return homeCards.value;
    if (selectedType.value === "projects") return projectCards.value;
    if (selectedType.value === "tasks")    return taskCards.value;
    return [];
});

// ── Scroll helpers ─────────────────────────────────────────────────────────────
function updateScrollState() {
    const el = scrollContainer.value;
    if (!el) return;
    canScrollLeft.value  = el.scrollLeft > 4;
    canScrollRight.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 4;
}
function scrollBy(direction) {
    const el = scrollContainer.value;
    if (!el) return;
    el.scrollBy({ left: direction === "right" ? el.clientWidth * 0.65 : -(el.clientWidth * 0.65), behavior: "smooth" });
}
watch([visibleCards, showToolBar], async () => { await nextTick(); updateScrollState(); });

let ro;
onMounted(() => {
    updateScrollState();
    ro = new ResizeObserver(updateScrollState);
    if (scrollContainer.value) ro.observe(scrollContainer.value);
});
onUnmounted(() => ro?.disconnect());
</script>

<template>
<div class="p-4 space-y-6 bg-gray-50 dark:bg-[#1e1f1e] min-h-screen">

    <!-- Header Section with Profile and Quick Actions -->
    <div class="h-fit lg:min-h-53.5 lg:h-53    grid grid-cols-1 lg:grid-cols-12 gap-4  ">
        <div class="lg:col-span-8">
            <DashboardProfile />
        </div>
        <div class="lg:col-span-4">
            <DashboardWidgets />
        </div>
    </div>

    <!-- Main Stats Grid - 3 column layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Completion Card -->
        <div class="bg-white dark:bg-[#1e1f1e] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md  hover:ring-1 transition-shadow ">
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Overall Progress</h3>
                <span data-toolTip="completed items / total items" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300">
                    {{ completedItems }}/{{ totalItems }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 72 72">
                        <circle cx="36" cy="36" r="30" fill="none" stroke="currentColor" stroke-width="6"
                                class="text-gray-200 dark:text-gray-700" />
                        <circle cx="36" cy="36" r="30" fill="none" :stroke="ringColor" stroke-width="6"
                                stroke-linecap="round" :stroke-dasharray="CIRCUMFERENCE"
                                :stroke-dashoffset="ringOffset" class="transition-all duration-700" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold text-gray-800 dark:text-white">{{ completionRate }}%</span>
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-1.5">
                    <p class="text-sm font-medium text-gray-800 dark:text-white mb-1">{{ greeting }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ completionLabel }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400"> <span class="bg-yellow-500 text-gray-700 font-bold rounded-md py-px px-1 mr-1 ">Note:  </span> {{ completionDescription }}</p>

                </div>
            </div>
        </div>
 <!-- Workload Card -->
        <div class="bg-white dark:bg-[#1e1f1e] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md hover:ring-1 transition-shadow ">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Workload Distribution</h3>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ activeItems }}</span>
                    </div>
                    <div data-toolTip="In Progress items" data-position="top" class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-500"
                             :style="{ width: totalItems ? (activeItems / totalItems * 100) + '%' : '0%' }" />
                    </div>
                </div>

                <div>
                    <div  class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Pending</span>
                        <span class="text-sm font-semibold" :class="backlogBig ? 'text-rose-500' : 'text-gray-600'">
                            {{ pendingItems }}
                        </span>
                    </div>
                    <div data-toolTip="Pending items" data-position="top" class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500 rounded-full"
                             :class="backlogBig ? 'bg-rose-500' : 'bg-gray-400 dark:bg-gray-500'"
                             :style="{ width: totalItems ? (pendingItems / totalItems * 100) + '%' : '0%' }" />
                    </div>
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                {{ activeItems === 0 && pendingItems > 0 ? '✨ Start something from backlog' :
                   backlogBig ? '📊 Backlog needs attention' : '✅ Balanced workload' }}
            </p>
        </div>
        <!-- Deadlines Card -->
        <div class="bg-white dark:bg-[#1e1f1e] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md hover:ring-1 transition-shadow  self-end">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Deadlines</h3>
                <span v-if="hasUrgent" class="flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75" />
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-300" />
                    </span>
                    <span class="text-xs font-medium text-red-500">Urgent</span>
                </span>
                <span v-else class="text-xs text-emerald-500 font-medium">All clear</span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-nowrap ">
                <div class="space-y-2  p-1 border-r pr-4 border-[#c7c7c7] border-[#232222]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500" />
                        <span class="text-xs text-gray-600 dark:text-gray-300">Overdue</span>
                        <span class="border w-full border-red-400"></span>
                        <span class="ml-auto text-sm font-semibold text-red-500">{{ overdueCount }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-400" />
                        <span class="text-xs text-gray-600 dark:text-gray-300">Today</span>
                                                <span class="border w-full border-orange-300"></span>

                        <span class="ml-auto text-sm font-semibold text-orange-400">{{ dueTodayCount }}</span>
                    </div>
                </div>
                <div class="space-y-2 p-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400" />
                        <span class="text-xs text-gray-600 dark:text-gray-300">Tomorrow</span>
                                                <span class="border w-full border-amber-300"></span>

                        <span class="ml-auto text-sm font-semibold text-amber-400">{{ dueTomorrowCount }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-300" />
                        <span class="text-xs text-gray-600 dark:text-gray-300">This week</span>
                                                <span class="border w-full border-indigo-300"></span>

                        <span class="ml-auto text-sm font-semibold text-indigo-400">{{ dueThisWeek }}</span>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <!-- Navigation & Cards Section -->
   <div :class="(selectedType =='projects')?'dark:to-[#1b2132] ':'dark:to-[#1b3229] '" class="bg-linear-to-br from-[#eeeeee] to-[#fffbfb] dark:from-[#232422] rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">

    <!-- View Toggle (unchanged) -->
    <div  :class="showCards ? 'mb-4 pb-2  border-b':'mb-1 pb-1 border-0'" class="flex items-center justify-between gap-2  border-gray-100 dark:border-gray-700">
        <div class="flex gap-1.5">
          <button v-for="item in menuItems" :key="item.value"
                @click="selectedType = item.value"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all cursor-pointer"
                :class="selectedType === item.value
                    ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
            <CardsMenuSvg :type="item.value" />
            {{ item.label }}
        </button>
        </div>

        <button  @click="showCards = !showCards" class=" justify-self-end hover:scale-115 hover:opacity-70 active:scale-105  text-gray-800 dark:text-gray-400 cursor-pointer "
            :data-toolTip="showCards ? 'Collapse cards' : 'Expand cards'">
            <Shape v-if="showCards" name="arrow-down" ></Shape>
            <Shape v-else name="arrow-up"></Shape>
        </button>
    </div>

        <!-- Cards Grid - Responsive columns based on view type -->
        <div v-if="showCards" class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-3 auto-rows-[150px]">
        <template v-if="selectedType === 'projects'">
            <Card v-for="card in projectCards" :key="card.type"
                  :title="card.title" :description="card.description"
                  :number="card.number" :color="card.color"
                  :href="route(card.route)" :type="card.type"
                  class="w-full h-full"
                :data-toolTip="`View ${card.title == 'Total Projects'? '': 'all'} ${card.title.toLowerCase()} ${card.title == 'Total Projects'? '': 'projects'}`" />
/>
        </template>
        <template v-else>
            <Card v-for="card in taskCards" :key="card.type"
                  :title="card.title" :description="card.description"
                  :number="card.number" :color="card.color"
                  :href="route(card.route)" :type="card.type"
                  class="w-full h-full"
                  :data-toolTip="`View ${card.title == 'Total Tasks'? '': 'all'} ${card.title.toLowerCase()} ${card.title == 'Total Tasks'? '': 'tasks'}`" />
        </template>
    </div>
</div>

    <!-- Activity Section - Uncomment when ready -->
    <!--
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <DashboardActivity class="lg:col-span-1" />
        <DashboardNearCompleation class="lg:col-span-1" />
        <DashboardOverdue class="lg:col-span-1" />
    </div>
    -->

</div>
</template>

<style scoped>
/* Smooth transitions */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Card hover effects */
.grid > div {
    transition: transform 0.3s ease-in-out, box-shadow 0.2s ease-in;
}

.grid > div:hover {
    transform: translateY(-2px);
}
</style>
