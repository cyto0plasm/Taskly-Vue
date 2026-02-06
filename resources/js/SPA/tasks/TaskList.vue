<script setup>
/* =========================
   Imports
========================= */
import Sortable from "sortablejs";
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from "vue";
import { useTaskStore } from "../store/taskStore.js";

import ListHeader from "./components/ListHeader.vue";
import ListLi from "./components/ListLi.vue";
import FlashMessage from "../components/FlashMessage.vue";
import paginate from "../components/paginate.vue";

/* =========================
   Store & Refs
========================= */
const store = useTaskStore();
const tasks = computed(() => store.tasks);
const selectedTaskId = computed(() => store.selectedTaskId);
const IsLoadMoreMode = ref(false);
const containerRef = ref(null);
let sortableInstance = null;

/* =========================
   Computed
========================= */
const loading = computed(() => store.loading);
const loadingMore = computed(() => store.loadingMore);

/* =========================
   Task Loading
========================= */
async function loadTasks(page = 1, perPage = IsLoadMoreMode.value ? 100 : 20, replace = true) {
    await store.loadTasks(page, perPage, replace);
}

async function loadMore() {
    if (store.loading || store.loadingMore || !store.pagination.hasMore) return;
    const nextPage = store.pagination.page + 1;
    await loadTasks(nextPage, 100, false);
}

/* =========================
   Sortable (Drag & Drop)
========================= */
function initSortable() {
    if (!containerRef.value) return;
    if (sortableInstance) sortableInstance.destroy();

    sortableInstance = new Sortable(containerRef.value, {
        animation: 200,
        ghostClass: "bg-yellow-100",
        onEnd: async ({ oldIndex, newIndex }) => {
            if (oldIndex == null || newIndex == null) return;

            const moved = store.tasks.splice(oldIndex, 1)[0];
            store.tasks.splice(newIndex, 0, moved);

            const payload = store.tasks.map((t, idx) => ({ id: t.id, position: idx + 1 }));
            await store.reorderTasks(payload);
        },
    });
}

/* =========================
   Mode Toggle
========================= */
function toggleMode() {
    IsLoadMoreMode.value = !IsLoadMoreMode.value;
    store.pagination.page = 1;
    store.tasks = [];
    loadTasks(1, IsLoadMoreMode.value ? 100 : 20, true);
}

/* =========================
   Scroll to Task
========================= */
function scrollToTask(taskId) {
    nextTick(() => {
        if (!containerRef.value) return;
        const el = document.getElementById(`task-${taskId}`);
        if (!el) return;

        const scrollTop = el.offsetTop - containerRef.value.clientHeight / 2 + el.offsetHeight / 2;
        containerRef.value.scrollTo({ top: scrollTop, behavior: "smooth" });
    });
}

/* =========================
   Lifecycle Hooks
========================= */
onMounted(async () => {
    await loadTasks();

    if (!selectedTaskId.value && tasks.value.length) {
        store.selectTask(tasks.value[0].id);
    }

    initSortable();
});

watch(selectedTaskId, (id) => {
    if (id != null) scrollToTask(id);
});

watch([IsLoadMoreMode, tasks], async ([mode, list]) => {
    if (mode && list.length) await nextTick(), initSortable();
});

onBeforeUnmount(() => {
    if (sortableInstance) sortableInstance.destroy();
});
</script>

<template>
  <FlashMessage />

  <div class="w-full lg:min-w-[320px] lg:w-[28rem] lg:max-w-[32rem] bg-white dark:bg-[#222321] rounded-lg shadow-md ">

    <!-- Header -->
    <ListHeader
      :tasks="tasks"
      :IsLoadMoreMode="IsLoadMoreMode"
      :statusCounts="store.pagination.statusCounts"
      :allStatusCounts="store.allStatusCounts"
      @toggle-show-all="toggleMode"
    />

    <div class="relative">

      <!-- Loading Skeleton -->
      <div v-if="loading" class="p-4 space-y-3 bg-transparent">
        <div v-for="i in 3" :key="i" class="flex items-center space-x-3 p-4 bg-gray-transparent dark:bg-gray-800/50 rounded-lg animate-pulse">
          <div class="w-5 h-5 bg-gray-300 dark:bg-gray-700 rounded mr-2"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700/50 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <ul v-else-if="!tasks || tasks.length === 0" class="p-6 text-center text-gray-500">
        <li><p class="text-sm sm:text-base">No tasks yet</p></li>
      </ul>

      <!-- Task List -->
      <ul
        v-else
        ref="containerRef"
        id="sortable-list"
        :data-sortable="IsLoadMoreMode"
        class="p-0 max-h-[50vh] sm:max-h-[60vh] lg:max-h-[65vh] overflow-y-auto"
      >
        <ListLi
          v-for="task in tasks"
          :key="task.id"
          :task="task"
          :is-selected="task.id === selectedTaskId"
          @delete-task="async (id) => await store.deleteTask(id)"
          @select-task="(id) => { store.selectTask(id); scrollToTask(id); }"
        />
      </ul>

      <!-- Load More Button -->
      <div v-if="IsLoadMoreMode && store.pagination.hasMore" class="flex justify-center py-4">
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
          :disabled="store.loadingMore"
          @click="loadMore"
        >
          <span v-if="store.loadingMore">Loading...</span>
          <span v-else>Load More</span>
        </button>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center py-4 border-t border-gray-200">
        <paginate
          v-if="!IsLoadMoreMode && store.pagination.lastPage > 1"
          :page="store.pagination.page"
          :last-page="store.pagination.lastPage"
          @change="(p) => loadTasks(p)"
        />
      </div>

    </div>
  </div>
</template>

<style>
#sortable-list::-webkit-scrollbar-track { background: transparent; }
#sortable-list::-webkit-scrollbar { width: 10px; }
#sortable-list::-webkit-scrollbar-thumb {
  background-color: rgba(100, 100, 100, 0.3);
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: content-box;
}
#sortable-list::-webkit-scrollbar-thumb:hover { background-color: rgba(100, 100, 100, 0.5); }

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #444;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
