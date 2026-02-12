<script setup>
import {
    ref,
    computed,
    onMounted,
    watch,
    nextTick,
    onBeforeUnmount,
} from "vue";
import { useTaskStore } from "../store/taskStore.js";
import SectionHeader from "../components/main/SectionHeader.vue";
import { useLayoutStore } from "../store/layoutStore.js";
import ListHeader from "../components/main//ListHeader.vue";
import Filters from "../components/main/Filters.vue";
import ListLi from "../components/main//ListLi.vue";
import paginate from "../components/paginate.vue";
import ConfirmDialog from "../components/confirmDialog.vue";
import { scrollToTask } from "../store/uiHelpers.js";
import {
    initTaskSortable,
    initLayoutSortable,
} from "../store/sortableHelpers.js";
const store = useTaskStore();
const layout = useLayoutStore();
layout.setActive("tasks");
const confirmDialogRef = ref(null);

const tasks = computed(() => store.tasks);
const selectedTaskId = computed(() => store.selectedTaskId);
const loading = computed(() => store.loading);
const softLoading = computed(() => store.softLoading);
const loadingMore = computed(() => store.loadingMore);

const isLoadMoreMode = ref(false);
const taskListRef = ref(null);
const layoutRef = ref(null);

//----Sorting----
let taskSortable = null;
let layoutSortable = null;

function onEnter(el) {
    el.style.height = "0";
    el.style.overflow = "hidden";
    requestAnimationFrame(() => (el.style.height = el.scrollHeight + "px"));
}

function onAfterEnter(el) {
    el.style.height = "auto";
    el.style.overflow = "visible";
}

function onLeave(el) {
    el.style.height = el.scrollHeight + "px";
    el.style.overflow = "hidden";
    requestAnimationFrame(() => (el.style.height = "0"));
}

async function loadTasks(
    page = 1,
    perPage = isLoadMoreMode.value ? 100 : 20,
    replace = true,
) {
    await store.loadTasks(page, perPage, replace);
}
async function onDeleteTask(id) {
    const confirmed = await confirmDialogRef.value.openConfirm();
    if (!confirmed) return;

    await store.deleteTask(id);
}
async function loadMore() {
    if (store.loading || store.loadingMore || !store.pagination.hasMore) return;
    await loadTasks(store.pagination.page + 1, 100, false);
}

async function toggleMode() {
    isLoadMoreMode.value = !isLoadMoreMode.value;

    store.pagination.page = 1;

    await store.loadTasks(1, isLoadMoreMode.value ? 100 : 20, true, {
        useSoftLoading: true,
    });

    await nextTick();
    setupTaskSortable();
}

function setupTaskSortable() {
    if (taskSortable) {
        taskSortable.destroy();
        taskSortable = null;
    }

    if (!isLoadMoreMode.value) return;
    if (!taskListRef.value) return;

    taskSortable = initTaskSortable({
        el: taskListRef.value,
        tasks: store.tasks,
        store,
        softLoading,
        enabled: true,
    });
}

onMounted(async () => {
    await loadTasks();
    if (!selectedTaskId.value && tasks.value.length) {
        store.selectTask(tasks.value[0].id);
    }

    setupTaskSortable();

    layoutSortable = initLayoutSortable(layoutRef.value);
});

watch(selectedTaskId, (id) => {
    if (id != null) scrollToTask(id);
});

watch(isLoadMoreMode, async () => {
    await nextTick();
    setupTaskSortable();
});

watch(softLoading, async (val) => {
    if (!val) {
        await nextTick();
        setupTaskSortable();
    }
});

onBeforeUnmount(() => {
    if (taskSortable) {
        taskSortable.destroy();
        taskSortable = null;
    }

    if (layoutSortable) {
        layoutSortable.destroy();
        layoutSortable = null;
    }
});

const borderStyle = computed(() => {
    return window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
        ? "1px solid rgb(142, 142, 142)" // dark
        : "1px solid rgb(216 216 216)"; // light
});
const sectionShadow = computed(() => {
    return "0 2px 5px rgba(0, 0, 0, 0.1)";
});

const sectionEdgeStyles = (key) => {
    const section = layout.sections[key];
    if (!section.visible) return { border: "none" }; // <-- no border if not visible
    return section.showHeaderBar
        ? { border: borderStyle.value }
        : { boxShadow: sectionShadow.value };
};
</script>

<template>
    <ConfirmDialog ref="confirmDialogRef" />

    <div
        class="w-full lg:min-w-[320px] lg:w-md lg:max-w-lg bg-[#FAFAFA] dark:bg-[#222321] rounded-t-lg shadow-md flex flex-col"
    >
        <!---------------- Main Sections ---------------->
        <div ref="layoutRef"  id="sortableSectionsWrapper" class="flex flex-col gap-2">
            <!------------------- Header Section ------------------->
            <transition name="slide-fade">
                <div

                    v-if="layout.sections.header.visible"
                    :style="sectionEdgeStyles('header')"
                    class="rounded-lg"
                    data-layout-id="header"
                >
                    <SectionHeader
                        v-if="layout.sections.header.showHeaderBar"
                        title="Header"
                        :collapsed="layout.sections.header.open"
                        :loading="store.loading"
                        @toggle="layout.toggleSection('header')"
                    />
                    <transition
                        name="filters-collapse"
                        @enter="onEnter"
                        @after-enter="onAfterEnter"
                        @leave="onLeave"
                    >
                        <div v-if="layout.sections.header.open">
                            <ListHeader
                                context="task"
                                :isLoadMoreMode="isLoadMoreMode"
                                :statusCounts="store.pagination.statusCounts"
                                :allStatusCounts="store.allStatusCounts"
                                :total-count="store.pagination.total"
                                :loaded-count="tasks.length"
                                @toggle-show-all="toggleMode"
                            />
                        </div>
                    </transition>
                </div>
            </transition>

            <!------------------- Filters Section ------------------->
            <transition name="slide-fade">
                <div

                    v-if="layout.sections.filters.visible"
                    :style="sectionEdgeStyles('filters')"
                    class="rounded-lg"
                    data-layout-id="filters"
                >
                    <Filters v-model:open="layout.layouts.tasks.sections.filters.open" />
                </div>
            </transition>

            <!------------------- Task List Section ------------------->
            <transition name="slide-fade">
                <div

                    v-if="layout.sections.tasklist.visible"
                    class="rounded-t-lg"
                    :style="sectionEdgeStyles('tasklist')"
                    data-layout-id="tasklist"
                >
                    <!-- Section Header -->
                    <SectionHeader
                        v-if="layout.sections.tasklist.showHeaderBar"
                        :title="`Tasks List - ${isLoadMoreMode ? 'Sort Mode' : 'Page Mode'}`"
                        :collapsed="layout.sections.tasklist.open"
                        :activeCount="tasks.length"
                        :loading="store.loading"
                        @toggle="layout.toggleSection('tasklist')"
                    />

                    <!-- Collapsible Content -->
                    <transition
                        name="filters-collapse"
                        @enter="onEnter"
                        @after-enter="onAfterEnter"
                        @leave="onLeave"
                    >
                        <div
                            v-if="layout.sections.tasklist.open"
                            class="relative"
                        >
                            <div
                                class="relative overflow-y-auto"
                                :style="{
                                    maxHeight: isLoadMoreMode
                                        ? 'calc(80vh - 10rem)'
                                        : 'calc(85vh - 10rem)',
                                }"
                            >
                                <!-- Task List -->
                                <ul
                                    v-show="tasks.length"
                                    ref="taskListRef"
                                    id="sortable-list"
                                    class="p-0 transition-opacity duration-200"
                                    :class="{
                                        'opacity-50 pointer-events-none select-none':
                                            store.softLoading,
                                    }"
                                >
                                    <ListLi
                                        v-for="task in tasks"
                                        :key="task.id"
                                        :task="task"
                                        :is-selected="
                                            task.id === selectedTaskId
                                        "
                                        @delete-task="onDeleteTask"
                                        @select-task="store.selectTask"
                                        :id="`task-${task.id}`"
                                        :isLoadMore="isLoadMoreMode"
                                    />
                                </ul>

                                <!-- Skeleton Loader -->
                                <div
                                    v-if="loading && !softLoading"
                                    class="p-4 space-y-3"
                                >
                                    <div
                                        v-for="i in 3"
                                        :key="i"
                                        class="flex items-center space-x-3 p-4 bg-gray-transparent dark:bg-gray-800/50 rounded-lg animate-pulse"
                                    >
                                        <div
                                            class="w-5 h-5 bg-gray-300 dark:bg-gray-700 rounded mr-2"
                                        ></div>
                                        <div class="flex-1 space-y-2">
                                            <div
                                                class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"
                                            ></div>
                                            <div
                                                class="h-3 bg-gray-200 dark:bg-gray-700/50 rounded w-1/2"
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Soft Loading Overlay -->
                                <div
                                    v-if="softLoading"
                                    class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-black/50 z-20 pointer-events-auto"
                                >
                                    <svg
                                        class="w-8 h-8 animate-spin text-gray-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke-width="4"
                                            stroke-linecap="round"
                                            stroke="currentColor"
                                        />
                                    </svg>
                                </div>

                                <!-- Empty State -->
                                <ul
                                    v-else-if="
                                        !tasks || tasks.length === 0 || loading
                                    "
                                    class="p-6 text-center text-gray-500"
                                >
                                    <li>
                                        <p class="text-sm sm:text-base">
                                            No tasks yet
                                        </p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Load More Button -->
                            <div
                                v-if="
                                    isLoadMoreMode && store.pagination.hasMore
                                "
                                class="flex justify-center py-4"
                            >
                                <button
                                    class="disabled:opacity-50"
                                    :disabled="store.loadingMore"
                                    @click="loadMore"
                                >
                                    <span class="dark:text-white text-black">{{
                                        store.loadingMore
                                            ? "Loading..."
                                            : "Load More"
                                    }}</span>
                                </button>
                            </div>

                            <!-- Pagination -->
                            <div
                                v-if="
                                    !isLoadMoreMode &&
                                    store.pagination.lastPage > 1 &&
                                    !loadingMore
                                "
                                class="flex justify-center py-2 border-t border-gray-300 dark:border-gray-600"
                            >
                                <paginate
                                    :page="store.pagination.page"
                                    :last-page="store.pagination.lastPage"
                                    @change="(p) => loadTasks(p)"
                                />
                            </div>
                        </div>
                    </transition>
                </div>
            </transition>
        </div>
        <!------------------- Hints ------------------->
        <!-- on Collapsed Task List Hint -->
        <div
            v-if="!layout.sections.tasklist.open"
            class="px-4 py-2 text-xs mt-2"
        >
            <span class="text-gray-500 dark:text-white"
                >You have total of {{ store.pagination.total }} tasks -
            </span>
            <span :class="isLoadMoreMode ? 'text-green-500' : 'text-gray-500'">
                {{ isLoadMoreMode ? "Sort mode" : "Page mode" }}
            </span>
        </div>

        <!-- on Reorder Active Hint -->
        <div
            v-if="isLoadMoreMode"
            class="px-3 py-2 text-xs text-gray-500 dark:text-white"
        >
            Drag to reorder tasks · Changes are saved automatically.
            <button
                @click.stop="toggleMode"
                class="text-red-500 underline px-2 cursor-pointer"
            >
                Disable!
            </button>
        </div>
    </div>
</template>

<style>
#lightBorder {
    border-bottom: rgb(99, 99, 99) solid 1px;
}
#darkBorder {
    border: rgb(142, 142, 142) solid 1px;
}

.filters-collapse-enter-active,
.filters-collapse-leave-active {
    transition: all 0.2s ease-out;
}
.filters-collapse-enter-from,
.filters-collapse-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}

.draggable-handle:active {
    cursor: grabbing;
}

.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.15s ease,
        transform 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.25s ease-out;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

#sortable-list::-webkit-scrollbar-track {
    background: transparent;
}
#sortable-list::-webkit-scrollbar {
    width: 10px;
}
#sortable-list::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.3);
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: content-box;
}
#sortable-list::-webkit-scrollbar-thumb:hover {
    background-color: rgba(100, 100, 100, 0.5);
}
</style>
