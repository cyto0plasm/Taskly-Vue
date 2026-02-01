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

import ListHeader from "./components/ListHeader.vue";
import ListLi from "./components/ListLi.vue";
import FlashMessage from "../components/FlashMessage.vue";
import paginate from "../components/paginate.vue";
import { useFlash } from "../components/useFlash.js";

const { show } = useFlash();

// --------------------
// Store & State
// --------------------
const store = useTaskStore();
const tasks = computed(() => store.tasks);
const selectedTaskId = computed(() => store.selectedTaskId);
const loading = computed(() => store.loading);
const showAll = ref(false);

// Sortable instance
let sortableInstance = null;

// --------------------
// Functions
// --------------------

// Load tasks and init sortable if needed
async function loadTasks(page = 1) {
    await store.loadTasks({ page, showAll: showAll.value });
    await nextTick();
    initSortable();
}

// Initialize Sortable only in showAll mode
function initSortable() {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
    if (!showAll.value) return;

    const listEl = document.getElementById("sortable-list");
    if (!listEl) return;

    sortableInstance = new Sortable(listEl, {
        animation: 200,
        ghostClass: "bg-yellow-100",
        scroll: true,
        scrollSensitivity: 40,
        scrollSpeed: 30,
        onEnd: async (evt) => {
            const { oldIndex, newIndex } = evt;
            if (oldIndex == null || newIndex == null) return;

            // Reorder locally
            const moveItem = store.tasks.splice(oldIndex, 1)[0];
            store.tasks.splice(newIndex, 0, moveItem);

            // Prepare payload for API
            const start = Math.min(oldIndex, newIndex);
            const end = Math.max(oldIndex, newIndex);
            const payload = store.tasks
                .slice(start, end + 1)
                .map((task, index) => ({
                    id: task.id,
                    position: start + index + 1,
                }));

            try {
                await store.reorderTasks(payload);
                show("success", "Task order saved");
            } catch {
                show("error", "Failed to save task order");
            }
        },
    });
}

// Toggle showAll mode
function toggleShowAll() {
    showAll.value = !showAll.value;
    loadTasks();
}

// Scroll selected task into view
function scrollToTask(taskId) {
    nextTick(() => {
        const container = document.getElementById("sortable-list");
        const el = document.getElementById(`task-${taskId}`);
        if (!el || !container) return;

        const scrollTop =
            el.offsetTop - container.clientHeight / 2 + el.offsetHeight / 2;
        container.scrollTo({ top: scrollTop, behavior: "smooth" });
    });
}

// --------------------
// Lifecycle
// --------------------
onMounted(() => loadTasks());

onBeforeUnmount(() => {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
});

// Watchers
watch(showAll, () => nextTick(initSortable));
watch(selectedTaskId, (id) => {
    if (id != null) scrollToTask(id);
});

// Select first task after loading
onMounted(async () => {
    await loadTasks();
    if (!selectedTaskId.value && tasks.value.length) {
        store.selectTask(tasks.value[0].id);
    }
});
</script>

<template>
    <FlashMessage />

    <div
        class="w-full lg:min-w-[320px] lg:w-[28rem] lg:max-w-[32rem] bg-white dark:bg-[#222321] rounded-lg shadow-md lg:ml-[5rem]"
    >
        <!-- Header -->
        <ListHeader
            :tasks="tasks"
            :showAll="showAll"
            :statusCounts="store.pagination.statusCounts"
            :allStatusCounts="store.allStatusCounts"
            @toggle-show-all="toggleShowAll"
        />

        <div class="relative">
            <!-- Loading Skeleton -->
            <div v-if="loading" class="p-4 space-y-3 bg-transparent">
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

            <!-- Empty -->
            <ul
                v-else-if="tasks.length === 0"
                class="p-6 text-center text-gray-500"
            >
                <li><p class="text-sm sm:text-base">No tasks yet</p></li>
            </ul>

            <!-- Task List -->
            <ul
                v-else
                id="sortable-list"
                :data-sortable="showAll"
                class="p-0 max-h-[50vh] sm:max-h-[60vh] lg:max-h-[65vh] overflow-y-auto"
            >
                <ListLi
                    v-for="task in tasks"
                    :key="task.id"
                    :task="task"
                    :is-selected="task.id === selectedTaskId"
                    @delete-task="
                        async (id) => {
                            try {
                                await store.deleteTask(id);
                                show('success', 'Task deleted');
                            } catch {
                                show('error', 'Failed to delete task');
                            }
                        }
                    "
                    @select-task="
                        (id) => {
                            store.selectTask(id);
                            scrollToTask(id);
                        }
                    "
                />
            </ul>

            <!-- Pagination -->
            <div class="flex justify-center py-4 border-t border-gray-200">
                <paginate
                    v-if="!showAll && store.pagination.lastPage > 1"
                    :page="store.pagination.page"
                    :last-page="store.pagination.lastPage"
                    @change="(p) => loadTasks(p)"
                />
            </div>
        </div>
    </div>
</template>

<style>
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
