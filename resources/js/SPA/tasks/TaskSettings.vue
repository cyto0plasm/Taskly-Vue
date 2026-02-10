<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import SettingsIcon from "../svg/settingsIcon.vue";
import { useLayoutStore } from "../store/layoutStore.js";

const layout = useLayoutStore();
const sectionKeys = ["header", "filters", "tasklist"];

const safeSections = layout.sections;

const isSettingsOpen = ref(false);
//control vis/col link behavior
const linkCollapse = ref(true);
// Toggle functions
function toggleVisibility(section) {
    layout.toggleVisibility(section);
}
function toggleCollapse(section) {
    if (safeSections[section]?.visible) {
        layout.toggleSection(section);
    }
}

function toggleHeaderBar(section) {
    if (safeSections[section]?.visible) {
        layout.toggleHeaderBar(section);
    }
}

function toggleTaskDetails(section) {

layout.toggleDetailsVisibility(section);
}
//link visibility and collapse
async function toggleVisibilityWithCollapse(section) {
    const wasVisible = safeSections[section]?.visible;

    // Toggle visibility first
    layout.toggleVisibility(section);
    await nextTick(); // wait for reactive update

    // If linkCollapse is enabled, sync the collapse state
    if (linkCollapse.value) {
        const nowVisible = safeSections[section]?.visible;

        // If section just became visible, ensure it's expanded
        if (!wasVisible && nowVisible && !safeSections[section]?.open) {
            layout.toggleSection(section);
        }
        // If section just became hidden, ensure it's collapsed
        else if (wasVisible && !nowVisible && safeSections[section]?.open) {
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
    <button @click="isSettingsOpen = true" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        aria-label="Open settings">
        <SettingsIcon />
    </button>

    <!-- Settings Dialog -->
    <transition name="fade">
        <div v-if="isSettingsOpen" @click="closeDialog"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
            <!-- Dialog content -->
            <div @click.stop
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <SettingsIcon />
                        </div>
                        <div class="flex flex-col items-start justify-end">
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white">
                                Settings
                            </h2>
                            <p class="text-gray-700 dark:text-gray-300 font-light">
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
                <div class="flex-1 overflow-y-auto p-6 ">
                    <!-- Task List Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                            Task List Settings
                        </h3>

                        <div class="flex flex-col  p-4">
                            <!-- top header -->
                            <div  style="box-shadow: 0px 5px 10px -2px rgba(0, 0, 0, 0.1); height: 35px;" class="flex gap-10  w-full justify-start items-center bg-gray-50 dark:bg-gray-800   ">
                                 <div class="flex gap-2 items-center pl-4 ">

                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-100 ">Visibile</div>
                                </div>
                                <h4
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Header
                                </h4>
                                <h4
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Filters
                                </h4>
                                <h4
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    TaskList
                                </h4>
                            </div>

                            <!-- table body -->
                            <div style="height: 110px;" class="flex gap-10   ">
                                <!-- left header -->
                                <div style="box-shadow: 6px 0px 10px -2px rgba(0, 0, 0, 0.1); padding-right: 28px;" class="flex gap-2 bg-gray-50 dark:bg-gray-800 py-2">
                                    <div class="flex flex-col gap-4 ">
                                        <div class="relative flex flex-col gap-4 py-2">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                                Visibility
                                            </h4>

                                            <button @click="
                                                linkCollapse = !linkCollapse
                                                " style="
                                                    position: absolute;
                                                    top: 27px;
                                                    right: 30px;
                                                " :class="[
                                                    ' transition-colors',
                                                    linkCollapse
                                                        ? 'text-green-500 hover:text-green-600'
                                                        : 'text-gray-600 hover:text-gray-800',
                                                ]" title="Toggle link between visibility and collapse">
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

                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                                Collapse
                                            </h4>
                                        </div>
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                            Header
                                        </h4>
                                    </div>
                                </div>
                                <!-- table content -->
                                <div style="
                                        display: flex;
                                        flex-direction: column;
                                        gap: 0.8rem;
                                        margin-top: 15px;
                                    ">
                                    <div style="display: flex; gap: 4.6rem">
                                        <svg v-for="section in sectionKeys" :key="section"
                                            @click="handleSectionClick(section)"
                                            class="cursor-pointer transition-colors duration-200 hover:text-gray-500"
                                            :class="[
                                                safeSections[section]?.visible
                                                    ? 'text-green-500 '
                                                    : 'text-black dark:text-white dark:hover:text-gray-400',
                                                'cursor-pointer',
                                            ]" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </div>
                                    <div style="display: flex; gap: 4.6rem">
                                        <svg v-for="section in sectionKeys" :key="section"
                                            @click="toggleCollapse(section)" :class="[
                                                safeSections[section]?.open
                                                    ? 'text-green-500'
                                                    : 'text-black dark:text-white dark:hover:text-gray-400',
                                                !safeSections[section]?.visible
                                                    ? 'opacity-50 cursor-not-allowed dark:text-white'
                                                    : 'cursor-pointer hover:text-gray-400 ',
                                            ]" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </div>
                                    <div style="display: flex; gap: 4.6rem">
                                        <svg v-for="section in sectionKeys" :key="section"
                                            @click="toggleHeaderBar(section)" :class="[
                                                safeSections[section]
                                                    ?.showHeaderBar
                                                    ? 'text-green-500'
                                                    : 'text-black dark:text-white dark:hover:text-gray-400',
                                                !safeSections[section]?.visible
                                                    ? 'opacity-50 cursor-not-allowed dark:text-white'
                                                    : 'cursor-pointer hover:text-gray-400 ',
                                            ]" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

                    <!-- Task Details Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                            Task Details Settings
                        </h3>
 <div class="flex gap-3">
  <!-- Details Button -->
  <button
    @click="toggleTaskDetails('details')"
    :class="[
      'flex-1 px-4 py-3 rounded-lg font-medium transition-all duration-200',
      layout.detailsSections.details.visible
        ? 'bg-white dark:bg-gray-900 text-black dark:text-white shadow-md'
        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-none hover:bg-gray-200 dark:hover:bg-gray-300 dark:hover:text-black'
    ]"
  >
    Details
  </button>

  <!-- Canvas Button -->
  <button
    @click="toggleTaskDetails('canvas')"
    :class="[
      'flex-1 px-4 py-3 rounded-lg font-medium transition-all duration-200',
      layout.detailsSections.canvas.visible
        ? 'bg-white dark:bg-gray-900 text-black dark:text-white shadow-md  '
        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-none hover:bg-gray-200 dark:hover:bg-gray-300 dark:hover:text-black'
    ]"
  >
    Canvas
  </button>
</div>


                    </div>
                </div>

                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Press
                        <kbd
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
