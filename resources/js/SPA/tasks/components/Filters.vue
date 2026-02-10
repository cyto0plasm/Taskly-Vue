<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useTaskStore } from "../../store/taskStore.js";
import FilterButtons from "../components/FilterButtons.vue";
import SectionHeader from "../components/SectionHeader.vue";
import { useLayoutStore } from "../../store/layoutStore.js";

const layout = useLayoutStore();
const store = useTaskStore();

const searchQuery = ref(store.filters.search || "");
const props = defineProps({ open: Boolean });
const emit = defineEmits(["update:open"]);

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
  { value: "this_week", label: "This Week" },
];

const priorityOptions = [
  { value: null, label: "All" },
  { value: "high", label: "High" },
  { value: "medium", label: "Medium" },
  { value: "low", label: "Low" },
];

const activeFiltersCount = computed(() => {
  const f = store.filters;
  return [f.search, f.status, f.has_project, f.due, f.priority].filter(
    (v) => v != null && v !== ""
  ).length;
});

let searchTimeout = null;

// Trigger search on Enter or after debounce
function triggerSearch() {
  store.setFilters({ search: searchQuery.value || null });
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
  store.setFilters({ status: v });
}
function toggleProjectFilter(v) {
  store.setFilters({ has_project: v });
}
function toggleDueFilter(v) {
  store.setFilters({ due: v });
}
function togglePriority(v) {
  store.setFilters({ priority: v });
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
  () => store.filters.search,
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

  <div :class="filtersOpen ? 'py-2' : 'py-0'" class="px-4 dark:bg-[#1F1F1F]">
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
            class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#2A2A2A] text-black focus:outline-none focus:ring-0 focus:border-2 transition"
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
        <div class="grid grid-cols-2 gap-3">
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
            class="col-span-2"
            label="Due Date"
            :options="dueOptions"
            :value="store.filters.due"
            color="green"
            @change="toggleDueFilter"
          />
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="pt-2 border-t border-gray-100 dark:border-gray-700/50">
          <button
            @click="showAdvancedFilters = !showAdvancedFilters"
            class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition"
          >
            <svg
              :class="{ 'rotate-90': showAdvancedFilters }"
              class="w-4 h-4 transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            Advanced filters
          </button>

          <transition name="fade-slide">
            <div v-if="showAdvancedFilters" class="mt-3">
              <FilterButtons
                label="Priority"
                :options="priorityOptions"
                :value="store.filters.priority"
                @change="togglePriority"
                :color-fn="getPriorityClass"
              />
            </div>
          </transition>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.filters-collapse-enter-active,
.filters-collapse-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}
.filters-collapse-enter-from,
.filters-collapse-leave-to {
  opacity: 0;
  height: 0;
  margin-top: 0;
}
.filters-collapse-enter-to,
.filters-collapse-leave-from {
  opacity: 1;
}
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease-out;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
