<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import SettingsIcon from "../svg/settingsIcon.vue";
import { useLayoutStore } from "../store/layoutStore.js";
import { storeToRefs } from "pinia";
import { useTaskStore } from "../store/taskStore.js";

//Manage Feilds Visible (reduce payload)
const taskStore = useTaskStore();
const allTaskFields = [
  "title",
  "status",
  "created_at",
];

const selectedFields = ref([...taskStore.taskFields]); // start with current store fields

// Watch for changes and update store automatically
watch(selectedFields, (newFields) => {
  taskStore.setTaskFields(newFields);
  console.log(newFields);
  // Optionally reload tasks to apply new fields
  taskStore.loadTasks(1);
}, { deep: true });

const layout = useLayoutStore();
const sectionKeys = ["header", "filters", "tasklist"];
const { sections: safeSections, detailsSections } = storeToRefs(layout);



const isSettingsOpen = ref(false);
//control vis/col link behavior
const linkCollapse = ref(false);
// Toggle functions
function toggleVisibility(section) {
    layout.toggleVisibility(section);
}
function toggleCollapse(section) {
     layout.toggleSection(section);
       console.log(`${section} open:`, safeSections.value[section]?.open); // Add this


}

function toggleHeaderBar(section) {
    if (safeSections.value[section]?.visible) {
        layout.toggleHeaderBar(section);
    }
}

function toggleTaskDetails(section) {

layout.toggleDetailsVisibility(section);
}
//link visibility and collapse
async function toggleVisibilityWithCollapse(section) {
    const wasVisible = safeSections.value[section]?.visible;

    layout.toggleVisibility(section);
    await nextTick();

    if (linkCollapse.value) {
        const nowVisible = safeSections.value[section]?.visible;

        if (!wasVisible && nowVisible && !safeSections.value[section]?.open) {
            layout.toggleSection(section);
        } else if (wasVisible && !nowVisible && safeSections.value[section]?.open) {
            layout.toggleSection(section);
        }
    }
}
function handleSectionClick(section) {
    if (linkCollapse.value) {
        toggleVisibilityWithCollapse(section);
    } else {
        toggleVisibility(section);
    }
}

// Dialog
function closeDialog() {
    isSettingsOpen.value = false;
}
function handleKeydown(e) {
    if (e.key === "Escape" && isSettingsOpen.value) closeDialog();
}

onMounted(() => document.addEventListener("keydown", handleKeydown));
onUnmounted(() => document.removeEventListener("keydown", handleKeydown));
</script>

<template>
    <!-- Button to open dialog -->
    <button @click="isSettingsOpen = true" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors cursor-pointer"
        aria-label="Open settings">
        <SettingsIcon />
    </button>

    <!-- Settings Dialog -->
    <transition name="fade">
        <div v-if="isSettingsOpen" @click="closeDialog"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
            <!-- Dialog content -->
            <div @click.stop
              role="dialog"
     aria-modal="true"
     aria-labelledby="tasks-settings"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <SettingsIcon />
                        </div>
                        <div>
                            <h2 class="font-bold text-lg sm:text-xl text-gray-900 dark:text-white">
                                Settings
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                Saved automatically
                            </p>
                        </div>
                    </div>

                    <button @click="closeDialog"
                        class="p-2 text-gray-400 hover:text-red-600  hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors cursor-pointer"
                        aria-label="Close settings">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M18 6L6 18M6 6L18 18" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <!-- Task List Settings -->
                    <div class="space-y-4">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                            Task List Settings
                        </h3>

                        <div class="overflow-x-auto">
                            <!-- Table -->
                            <div class="inline-block min-w-full">
                                <!-- Top header -->
                                <div class="flex gap-4 sm:gap-6 items-center bg-slate-100 dark:bg-gray-700 px-3 sm:px-4 py-2 rounded-t-lg shadow-sm">
                                    <div class="flex gap-2 items-center min-w-20 ml-1.5">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <div class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-100">Visible</div>
                                    </div>
                                    <h4 class="ml-4 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide min-w-[60px]">
                                        Header
                                    </h4>
                                    <h4 class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide min-w-[60px]">
                                        Filters
                                    </h4>
                                    <h4 class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide min-w-[70px]">
                                        TaskList
                                    </h4>
                                </div>

                                <!-- Table body -->
                                <div class="flex gap-4 sm:gap-6 bg-slate-50 dark:bg-gray-700/30 rounded-b-lg">
                                    <!-- Left header with link button -->
                                    <div class="flex gap-1 bg-slate-100 dark:bg-gray-700 py-3 px-3 sm:px-4 rounded-bl-lg">
                                        <div class="grid grid-rows-2 gap-2 my-4">
                                            <button
                                                @click="linkCollapse = !linkCollapse"
                                                class="flex items-center justify-center px-1.5 py-1 hover:bg-slate-200 dark:hover:bg-gray-600 rounded transition-colors cursor-pointer"
                                                :class="[
                                                    linkCollapse
                                                        ? 'text-green-500 hover:text-green-600'
                                                        : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-green-500'
                                                ]"
                                                title="Toggle link between visibility and collapse"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="1 1 22 22" fill="none">
                                                    <path
                                                        d="M15.197 3.35462C16.8703 1.67483 19.4476 1.53865 20.9536 3.05046C22.4596 4.56228 22.3239 7.14956 20.6506 8.82935L18.2268 11.2626M10.0464 14C8.54044 12.4882 8.67609 9.90087 10.3494 8.22108L12.5 6.06212"
                                                        stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                    <path
                                                        d="M13.9536 10C15.4596 11.5118 15.3239 14.0991 13.6506 15.7789L11.2268 18.2121L8.80299 20.6454C7.12969 22.3252 4.55237 22.4613 3.0464 20.9495C1.54043 19.4377 1.67609 16.8504 3.34939 15.1706L5.77323 12.7373"
                                                        stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex flex-col gap-3 py-2">
                                            <div class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                                Visibility
                                            </div>
                                            <div class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                                Collapse
                                            </div>
                                            <div class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                                Header
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table content (icons) -->
                                    <div class="flex flex-col gap-3 py-5 ml-3">
                                        <!-- Visibility row -->
                                        <div class="flex gap-17 lg:gap-18.75">
                                            <svg v-for="section in sectionKeys" :key="section"
                                                @click="handleSectionClick(section)"
                                                class="cursor-pointer transition-colors duration-200"
                                                :class="[
                                                    safeSections[section]?.visible
                                                        ? 'text-green-500 hover:text-green-600'
                                                        : 'text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400'
                                                ]"
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </div>

                                        <!-- Collapse row -->
                                        <div class="flex gap-17 lg:gap-18.75">
                                            <svg v-for="section in sectionKeys" :key="section"
                                                @click="toggleCollapse(section)"
                                                :class="[
                                                    safeSections[section]?.open
                                                        ? 'text-green-500 hover:text-green-600'
                                                        : 'text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400',
                                                    !safeSections[section]?.visible
                                                        ? 'opacity-50 cursor-not-allowed'
                                                        : 'cursor-pointer'
                                                ]"
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </div>

                                        <!-- Header row -->
                                        <div class="flex gap-17 lg:gap-18.75">
                                            <svg v-for="section in sectionKeys" :key="section"
                                                @click="toggleHeaderBar(section)"
                                                :class="[
                                                    safeSections[section]?.showHeaderBar
                                                        ? 'text-green-500 hover:text-green-600'
                                                        : 'text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400',
                                                    !safeSections[section]?.visible
                                                        ? 'opacity-50 cursor-not-allowed'
                                                        : 'cursor-pointer'
                                                ]"
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Fields Selection -->
<div class="space-y-2 mt-6">
  <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
    <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
    Task Fields
  </h3>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
    <label
      v-for="field in allTaskFields"
      :key="field"
      class="flex items-center gap-2 cursor-pointer text-sm sm:text-base"
    >
      <input
        type="checkbox"
        :value="field"
        v-model="selectedFields"
        class="accent-blue-500"
      />
      <span class="capitalize">{{ field.replace("_", " ") }}</span>
    </label>
  </div>
</div>

                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                    <!-- Task Details Settings -->
                    <div class="space-y-4">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                            Task Details Settings
                        </h3>

                        <div class="flex gap-3">
                            <button
                                @click="toggleTaskDetails('details')"
                                :class="[
                                    'flex-1 px-4 py-3 rounded-lg font-medium transition-color duration-200 cursor-pointer text-sm sm:text-base',
                                    detailsSections.details.visible
                                        ? 'bg-white dark:bg-gray-900 text-black dark:text-white shadow-md ring-2 ring-blue-500 dark:ring-blue-400'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-none hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Details
                            </button>

                            <button
                                @click="toggleTaskDetails('canvas')"
                                :class="[
                                    'flex-1 px-4 py-3 rounded-lg font-medium transition-color duration-200 cursor-pointer text-sm sm:text-base',
                                    detailsSections.canvas.visible
                                        ? 'bg-white dark:bg-gray-900 text-black dark:text-white shadow-md ring-2 ring-blue-500 dark:ring-blue-400'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-none hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Canvas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-3 sm:p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Press
                        <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 font-mono text-xs">ESC</kbd>
                        or click outside to close
                    </p>
                </div>
            </div>
        </div>
    </transition>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-active>div,
.fade-leave-active>div {
    transition:
        transform 0.3s ease,
        opacity 0.3s ease;
}

.fade-enter-from>div,
.fade-leave-to>div {
    transform: scale(0.95);
    opacity: 0;
}

h2,
h3,
h4 {
    line-height: 1.4;
}

.space-y-4>* {
    margin-bottom: 1rem;
}
</style>
