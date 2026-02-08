<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import SettingsIcon from "../svg/settingsIcon.vue";
import { useLayoutStore } from "../store/layoutStore.js";

const layout = useLayoutStore();
const sectionKeys = ['header', 'filters', 'tasklist'];

const safeSections = layout.sections;

const isOpen = ref(false);
const taskDetailsVisible = ref(true);

// Toggle functions
function toggleVisibility(section) {
    layout.toggleVisibility(section);
}
function toggleCollapse(section) {
    layout.toggleSection(section);
}
function toggleHeaderBar(section) {
    layout.toggleHeaderBar(section);
}
function toggleTaskDetails(show) {
    taskDetailsVisible.value = show;
}

// Dialog
function closeDialog() {
    isOpen.value = false;
}
function handleKeydown(e) {
    if (e.key === "Escape" && isOpen.value) closeDialog();
}

onMounted(() => document.addEventListener("keydown", handleKeydown));
onUnmounted(() => document.removeEventListener("keydown", handleKeydown));
</script>

<template>
    <!-- Button to open dialog -->
    <button @click="isOpen = true" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        aria-label="Open settings">
        <SettingsIcon />
    </button>

    <!-- Overlay + Dialog -->
    <transition name="fade">
        <div v-if="isOpen" @click="closeDialog"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
            <!-- Dialog content - stop propagation to prevent closing when clicking inside -->
            <div @click.stop
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
        <SettingsIcon />
    </div>
    <div class="flex flex-col  items-start justify-end">
    <h2 class="font-bold text-xl text-gray-900 dark:text-white">
        Settings
    </h2>
    <p class="text-gray-700 dark:text-gray-300 font-light ">
    settings are saved automatically.
</p>
</div>
</div>


                    <button @click="closeDialog"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all"
                        aria-label="Close settings">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M18 6L6 18M6 6L18 18" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable content -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Task List Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                            Task List Settings
                        </h3>

                        <!-- Visibility -->
                        <div class="space-y-3">
                            <div class="flex flex-col  lg:gap-4 sm:flex-row sm:items-start  ">
                                <h4
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Toggle Visibility
                                </h4>
                                <div class="flex gap-2 ">
                                <div class="flex gap-2 items-center pl-4 ">

                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div class="text-sm font-light text-gray-700 dark:text-gray-100 ">Visibile</div>
                                </div>
                                <div class="flex gap-2 items-center">

                                    <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                    <div class="text-sm font-light text-gray-700 dark:text-gray-100 ">Hidden</div>
                                </div></div>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <button v-for="section in sectionKeys" :key="section" @click="toggleVisibility(section)"
                                    :class="['px-4 py-2 rounded-lg font-medium transition-all duration-200',
                                        safeSections[section]?.visible

                                            ? 'bg-blue-500 text-white shadow-md hover:bg-blue-600'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    ]">
                                    {{ section === 'tasklist' ? 'Task List' : section.charAt(0).toUpperCase() +
                                        section.slice(1) }}
                                </button>
                            </div>
                        </div>

                        <!-- Collapse -->
                        <div class="space-y-3">
                            <div class="flex flex-col  lg:gap-4 sm:flex-row sm:items-start">
  <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
    Toggle Collapse
  </h4>

  <div class="flex gap-2">
    <div class="flex gap-2 items-center pl-4 ">
      <div class="w-2 h-2 rounded-full bg-purple-600"></div>
      <div class="text-sm font-light text-gray-700 dark:text-gray-100">Expanded</div>
    </div>

    <div class="flex gap-2 items-center">
      <div class="w-2 h-2 rounded-full bg-gray-300"></div>
      <div class="text-sm font-light text-gray-700 dark:text-gray-100">Collapsed</div>
    </div>
  </div>
</div>

                            <div class="flex gap-2 flex-wrap">
                                <button v-for="section in sectionKeys" :key="'collapse-' + section"
                                    @click="toggleCollapse(section)" :class="[
                                        'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                                        safeSections[section]?.open

                                            ? 'bg-purple-500 text-white shadow-md hover:bg-purple-600'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                    ]">
                                    {{ section === 'tasklist' ? 'Task List' : section.charAt(0).toUpperCase() +
                                        section.slice(1) }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Show/Hide Header Bars -->
                    <div class="space-y-3 mt-4">
                        <div class="flex flex-col  lg:gap-4 sm:flex-row sm:items-start">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Toggle Top Header
                            </h4>
                            <div class="flex gap-2 items-center pl-4 ">
                                  <div class="w-2 h-2 rounded-full bg-gray-300"></div>

                            <p class=" text-gray-700 dark:text-gray-100  font-light">Can arange sections</p>
                        </div>
                    </div>
                        <div class="flex gap-2 flex-wrap">
                            <button v-for="section in sectionKeys" :key="'headerbar-' + section"
                                @click="toggleHeaderBar(section)" :class="[
                                    'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                                    safeSections[section]?.showHeaderBar
                                        ? 'bg-indigo-500 text-white shadow-md hover:bg-indigo-600'
                                        : 'bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                ]">
                                {{ section === 'tasklist'
                                    ? 'Task List'
                                    : section.charAt(0).toUpperCase() + section.slice(1)
                                }}
                            </button>


                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>

                    <!-- Task Details Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                            Task Details Settings
                        </h3>
                        <div class="flex gap-3">
                            <button @click="toggleTaskDetails(true)" :class="[
                                'flex-1 px-4 py-3 rounded-lg font-medium transition-all duration-200',
                                taskDetailsVisible
                                    ? 'bg-green-500 text-white shadow-md hover:bg-green-600'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            ]">
                                Show
                            </button>
                            <button @click="toggleTaskDetails(false)" :class="[
                                'flex-1 px-4 py-3 rounded-lg font-medium transition-all duration-200',
                                !taskDetailsVisible
                                    ? 'bg-red-500 text-white shadow-md hover:bg-red-600'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            ]">
                                Hide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Press <kbd
                            class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 font-mono text-xs">ESC</kbd>
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
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.fade-enter-from>div,
.fade-leave-to>div {
    transform: scale(0.95);
    opacity: 0;
}
</style>
