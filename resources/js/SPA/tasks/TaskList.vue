<script setup>
import Sortable from "sortablejs";
import {
    ref,
    computed,
    onMounted,
    watch,
    nextTick,
    onBeforeUnmount,
} from "vue";
import { useTaskStore } from "../store/taskStore.js";
import SectionHeader from "./components/SectionHeader.vue";
import {useLayoutStore}  from "../store/layoutStore.js"
import ListHeader from "./components/ListHeader.vue";
import Filters from "./components/Filters.vue";
import ListLi from "./components/ListLi.vue";
import paginate from "../components/paginate.vue";

const store = useTaskStore();
const layout = useLayoutStore();

const tasks = computed(() => store.tasks);
const selectedTaskId = computed(() => store.selectedTaskId);
const loading = computed(() => store.loading);
const softLoading = computed(() => store.softLoading);
const loadingMore = computed(() => store.loadingMore);

const isLoadMoreMode = ref(false);
const taskListRef = ref(null);
//----Sorting----
//task list sorting instance
let taskSortable = null;
//layout sorting instance
let layoutSortable = null;
//control sections




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

async function loadMore() {
    if (store.loading || store.loadingMore || !store.pagination.hasMore) return;
    await loadTasks(store.pagination.page + 1, 100, false);
}

function initTaskSortable() {
    if (!taskListRef.value || softLoading.value) return;

    if (!isLoadMoreMode.value) {
        if (taskSortable) {
            taskSortable.destroy();
            taskSortable = null;
        }
        return;
    }

    if (taskSortable) taskSortable.destroy();

    taskSortable = new Sortable(taskListRef.value, {
        animation: 200,
        ghostClass: "bg-yellow-100",
        onEnd: async ({ oldIndex, newIndex }) => {
            if (oldIndex == null || newIndex == null || oldIndex === newIndex)
                return;

            const moved = store.tasks.splice(oldIndex, 1)[0];
            store.tasks.splice(newIndex, 0, moved);

            const payload = store.tasks.map((t, idx) => ({
                id: t.id,
                position: idx + 1,
            }));
            await store.reorderTasks(payload);
        },
    });
}

function initLayoutSortable() {
    const wrapper = document.getElementById("sortableSectionsWrapper");
    if (!wrapper) return;

    if (layoutSortable) layoutSortable.destroy();

    layoutSortable = new Sortable(wrapper, {
        animation: 200,
        ghostClass: "bg-yellow-100",
        handle: ".draggable-handle",
        onEnd: () => {
            const layoutOrder = Array.from(wrapper.children).map(
                (el) => el.dataset.layoutId,
            );
            localStorage.setItem("layoutOrder", JSON.stringify(layoutOrder));
        },
    });

    const savedOrder = localStorage.getItem("layoutOrder");
    if (savedOrder) {
        const order = JSON.parse(savedOrder);
        order.forEach((id) => {
            const el = Array.from(wrapper.children).find(
                (child) => child.dataset.layoutId === id,
            );
            if (el) wrapper.appendChild(el);
        });
    }
}

async function toggleMode() {
    isLoadMoreMode.value = !isLoadMoreMode.value;
    store.pagination.page = 1;
    await store.loadTasks(1, isLoadMoreMode.value ? 100 : 20, true, {
        useSoftLoading: true,
    });
    await nextTick();
    initTaskSortable();
}

function scrollToTask(taskId) {
    nextTick(() => {
        if (!taskListRef.value) return;
        const el = document.getElementById(`task-${taskId}`);
        if (!el) return;
        const scrollTop =
            el.offsetTop -
            taskListRef.value.clientHeight / 2 +
            el.offsetHeight / 2;
        taskListRef.value.scrollTo({ top: scrollTop, behavior: "smooth" });
    });
}

onMounted(async () => {

    await loadTasks();
    if (!selectedTaskId.value && tasks.value.length) {
        store.selectTask(tasks.value[0].id);
    }

    initTaskSortable();
    initLayoutSortable();
});

watch(selectedTaskId, (id) => {
    if (id != null) scrollToTask(id);
});

watch(isLoadMoreMode, async () => {
    await nextTick();
    initTaskSortable();
});



onBeforeUnmount(() => {
    if (taskSortable) taskSortable.destroy();
    if (layoutSortable) layoutSortable.destroy();
});


</script>

<template>
    <div
        ref="layoutRef"
  class=" w-full lg:min-w-[320px] lg:w-[28rem] lg:max-w-[32rem] bg-[#FAFAFA] dark:bg-[#222321] rounded-t-lg shadow-md flex flex-col"
    >
        <!---------------- Main Sections ---------------->
        <div id="sortableSectionsWrapper" class="flex flex-col gap-2 ">
            <!------------------- Header Section ------------------->
            <transition name="slide-fade">
                <div
                    v-if="layout.sections.header.visible"
                    class="border rounded-lg border-[#575757] dark:border-[#000000] "
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
                                :isLoadMoreMode="isLoadMoreMode"
                                :statusCounts="store.pagination.statusCounts"
                                :allStatusCounts="store.allStatusCounts"
                                :total-tasks-count="store.pagination.total"
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
                    :class="layout.sections.filters.open?'border rounded-t-lg rounded-lg':'border  rounded-t-lg rounded-lg'"
                    data-layout-id="filters"
                >
                    <Filters v-model:open="layout.sections.filters.open" />
                </div>
            </transition>

            <!------------------- Task List Section ------------------->
            <transition name="slide-fade">
                <div
                    v-if="layout.sections.tasklist.visible"
                    :class="[
                        'border',
                        layout.sections.tasklist.open ? 'rounded-lg' : 'rounded-lg',
                    ]"
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
                        <div v-if="layout.sections.tasklist.open" class="relative">
                            <div
                                class="relative  overflow-y-auto"
                               :style="{
    maxHeight: isLoadMoreMode
      ? 'calc(80vh - 10rem)'
      : 'calc(85vh - 10rem)'
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
                                        @delete-task="
                                            async (id) =>
                                                await store.deleteTask(id)
                                        "
                                        @select-task="
                                            (id) => {
                                                store.selectTask(id);
                                                scrollToTask(id);
                                            }
                                        "
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
                                class="flex justify-center py-2 border-t border-gray-200"
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
            class="px-4 py-2 text-xs border-t mt-2"
        >
            You have total of {{ store.pagination.total }} tasks ·
            <span :class="isLoadMoreMode ? 'text-green-500' : 'text-gray-500'">
                {{ isLoadMoreMode ? "Sort mode" : "Page mode" }}
            </span>
        </div>

        <!-- on Reorder Active Hint -->
        <div
            v-if="isLoadMoreMode"
            class="px-3 py-2 text-xs bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300"
        >
            Drag to reorder tasks · Changes are saved automatically.
            <button @click.stop="toggleMode" class="text-red-500 underline">
                Disable!
            </button>
        </div>
    </div>
</template>

<style>
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
