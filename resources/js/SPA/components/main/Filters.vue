<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useTaskStore } from "../../store/task-store.js";
import { useProjectStore } from "../../store/project-store.js";
import { useLayoutStore } from "../../store/layout-store.js";
import FilterButtons from "../../components/main/FilterButtons.vue";
import SectionHeader from "../../components/main//SectionHeader.vue";

const layout = useLayoutStore();

const props = defineProps({ open: Boolean , context: { type: String, default: 'task' }});
const emit = defineEmits(["update:open"]);

const taskStore = useTaskStore();
const projectStore = useProjectStore();
const store = computed(() => props.context === 'project' ? projectStore : taskStore);

const searchQuery = ref(store.value.filters.search || "");

const filtersOpen = computed({
  get: () => props.open,
  set: (v) => emit("update:open", v),
});

const showAdvancedFilters = ref(false);

const statusOptions = [
  { value: null, label: "All" },
  { value: "pending", label: "Pending" },
  { value: "in_progress", label: "In Progress" },
  { value: "done", label: "Done" },
];

const projectOptions = [
  { value: null, label: "All" },
  { value: true, label: "With Project" },
  { value: false, label: "No Project" },
];

const dueOptions = [
  { value: null, label: "Any Date" },
  { value: "today", label: "Today" },
  { value: "overdue", label: "Overdue" },
  { value: "upcoming", label: "Upcoming" },
];

const priorityOptions = [
  { value: null, label: "All" },
  { value: "high", label: "High" },
  { value: "medium", label: "Medium" },
  { value: "low", label: "Low" },
];

const activeFiltersCount = computed(() => {
  const f = store.value.filters;
  return [f.search, f.status, f.has_project, f.due, f.priority].filter(
    (v) => v != null && v !== ""
  ).length;
});

let searchTimeout = null;

// Trigger search on Enter or after debounce
function triggerSearch() {
  store.value.setFilters({ search: searchQuery.value || null });
}

function onSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    triggerSearch();
  }, 300); // debounce 300ms
}

function onSearchEnter(e) {
  if (e.key === "Enter") {
    clearTimeout(searchTimeout);
    triggerSearch();
  }
}

function toggleStatus(v) {
  store.value.setFilters({ status: v });
}
function toggleProjectFilter(v) {
  store.value.setFilters({ has_project: v });
}
function toggleDueFilter(v) {
  store.value.setFilters({ due: v });
}
function togglePriority(v) {
  store.value.setFilters({ priority: v });
}
function getPriorityClass(p) {
  return (
    { high: "bg-red-500", medium: "bg-yellow-500", low: "bg-green-500" }[p] ||
    "bg-blue-500"
  );
}

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

// Sync internal state -> parent
function toggleFilters() {
  filtersOpen.value = !filtersOpen.value;
}

watch(
  () => store.value.filters.search,
  (v) => {
    if (v !== searchQuery.value) searchQuery.value = v || "";
  },
  { immediate: true }
);

onUnmounted(() => clearTimeout(searchTimeout));
</script>

<template>
  <SectionHeader
    v-if="layout.sections.filters.showHeaderBar"
    title="Filters"
    :activeCount="activeFiltersCount"
    :loading="store.loading"
    :collapsed="filtersOpen"
    :showClear="true"
    @toggle="toggleFilters"
    @clear="store.clearFilters()"
  />

  <div :class="filtersOpen ? 'py-2' : 'py-0'" class="px-4 dark:bg-[#1F1F1F] rounded-lg">
    <transition
      name="filters-collapse"
      @enter="onEnter"
      @after-enter="onAfterEnter"
      @leave="onLeave"
    >
      <div v-show="filtersOpen" class="mt-3 space-y-4">
        <!-- Search -->
        <div class="relative w-full">
          <input
            type="text"
            v-model="searchQuery"
            @input="onSearchInput"
            @keyup.enter="onSearchEnter"
            placeholder="Search tasks..."
            class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border-2 border-gray-300     dark:bg-[#2A2A2A] dark:text-white text-black focus:outline-none focus:ring-0 focus:border-2 focus:border-gray-500 transition"
          />

          <!-- Left Search Icon -->
          <svg
            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500 pointer-events-none"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"
            />
          </svg>

          <!-- Clear Button -->
          <button
            v-if="searchQuery"
            @click="() => { searchQuery = ''; store.setFilters({ search: null }); }"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Filters Grid -->
        <div class="flex flex-col gap-3">

          <FilterButtons
            label="Status"
            :options="statusOptions"
            :value="store.filters.status"
            color="blue"
            @change="toggleStatus"
          />
          <FilterButtons
            label="Project"
            :options="projectOptions"
            :value="store.filters.has_project"
            color="purple"
            @change="toggleProjectFilter"
          />
          <FilterButtons
            class=""
            label="Due Date"
            :options="dueOptions"
            :value="store.filters.due"
            color="green"
            @change="toggleDueFilter"
          />
        </div>





              <FilterButtons
              class="mb-2"
                label="Priority"
                :options="priorityOptions"
                :value="store.filters.priority"
                @change="togglePriority"

              />



      </div>
    </transition>
  </div>
</template>

<style scoped>

</style>
