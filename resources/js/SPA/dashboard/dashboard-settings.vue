<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useDashboardSettingsStore } from '../store/dashboard-settings-store.js';
import SettingsIcon from '../svg/settingsIcon.vue';

const store = useDashboardSettingsStore();
const isOpen  = ref(false);
const active  = ref('projects'); // 'projects' | 'tasks'

defineExpose({ open: () => isOpen.value = true });
function close() { isOpen.value = false; }
function handleKey(e) { if (e.key === 'Escape' && isOpen.value) close(); }
onMounted(()  => document.addEventListener('keydown', handleKey));
onUnmounted(() => document.removeEventListener('keydown', handleKey));

// Eye icon path helpers
const EYE_ON  = 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z';
const EYE_DOT = 'M12 9a3 3 0 100 6 3 3 0 000-6z';
</script>

<template>
    <!-- ── Trigger (export this button anywhere you like) ── -->


    <!-- ── Backdrop ── -->
    <transition name="fade">
        <div
            v-if="isOpen"
            @click="close"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4"
        >
            <!-- ── Dialog ── -->
            <div
                @click.stop
                role="dialog"
                aria-modal="true"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg
                       flex flex-col max-h-[90vh] overflow-hidden"
            >
                <!-- Header -->
                <div class="flex items-center justify-between p-4 sm:p-6
                            border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <SettingsIcon />
                        </div>
                        <div>
                            <h2 class="font-bold text-lg text-gray-900 dark:text-white">
                                Dashboard Settings
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Saved automatically</p>
                        </div>
                    </div>
                    <button
                        @click="close"
                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-gray-100
                               dark:hover:bg-gray-700 rounded-lg transition-colors cursor-pointer"
                    >
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 6L6 18M6 6L18 18" />
                        </svg>
                    </button>
                </div>

                <!-- Tab bar -->
                <div class="flex gap-0.5 px-4 pt-3 border-b border-gray-100 dark:border-gray-800">
                    <button
                        v-for="tab in [{ key: 'projects', label: 'Projects' }, { key: 'tasks', label: 'Tasks' }]"
                        :key="tab.key"
                        @click="active = tab.key"
                        class="relative px-3 py-2 text-xs font-semibold rounded-t-lg transition-colors"
                        :class="active === tab.key
                            ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    >
                        {{ tab.label }}
                        <span
                            v-if="active === tab.key"
                            class="absolute bottom-0 left-0 right-0 h-0.5
                                   bg-indigo-500 dark:bg-indigo-400 rounded-t-full"
                        />
                    </button>
                </div>

                <!-- Scrollable body -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-2">
                    <!-- Column headers -->
                    <div class="flex items-center gap-3 px-2 mb-3">
                        <span class="flex-1 text-[10px] font-bold uppercase tracking-widest
                                     text-gray-400 dark:text-gray-500">Card</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest w-8 text-center
                                     text-gray-400 dark:text-gray-500">Show</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest w-8 text-center
                                     text-gray-400 dark:text-gray-500">Min</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest w-12 text-center
                                     text-gray-400 dark:text-gray-500">Order</span>
                    </div>

                    <!-- Card rows -->
                    <div
                        v-for="(card, idx) in store.ordered(active)"
                        :key="card.key"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                               bg-gray-50 dark:bg-gray-700/40 transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-700/70"
                    >
                        <!-- Label -->
                        <span
                            class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-200 truncate"
                            :class="{ 'opacity-40': !card.visible }"
                        >
                            {{ card.label }}
                        </span>

                        <!-- Visible toggle -->
                        <button
                            @click="store.toggleVisible(active, card.key)"
                            class="w-8 flex justify-center cursor-pointer transition-colors"
                            :class="card.visible
                                ? 'text-indigo-500 hover:text-indigo-600'
                                : 'text-gray-300 dark:text-gray-600 hover:text-gray-400'"
                            title="Toggle visibility"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path :d="EYE_ON" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>

                        <!-- Collapse toggle -->
                        <button
                            @click="store.toggleCollapse(active, card.key)"
                            :disabled="!card.visible"
                            class="w-8 flex justify-center transition-colors"
                            :class="[
                                !card.visible ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
                                card.collapsed && card.visible
                                    ? 'text-amber-500 hover:text-amber-600'
                                    : 'text-gray-300 dark:text-gray-600 hover:text-gray-400'
                            ]"
                            title="Toggle collapse"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Order up/down -->
                        <div class="flex flex-col gap-0.5 w-12 items-center">
                            <button
                                @click="store.move(active, card.key, 'up')"
                                :disabled="idx === 0"
                                class="p-0.5 rounded transition-colors"
                                :class="idx === 0
                                    ? 'text-gray-200 dark:text-gray-700 cursor-not-allowed'
                                    : 'text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 cursor-pointer'"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M18 15l-6-6-6 6" />
                                </svg>
                            </button>
                            <button
                                @click="store.move(active, card.key, 'down')"
                                :disabled="idx === store.ordered(active).length - 1"
                                class="p-0.5 rounded transition-colors"
                                :class="idx === store.ordered(active).length - 1
                                    ? 'text-gray-200 dark:text-gray-700 cursor-not-allowed'
                                    : 'text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 cursor-pointer'"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-3 sm:p-4 border-t border-gray-200 dark:border-gray-700
                            bg-gray-50 dark:bg-gray-800/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Press
                        <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded
                                    text-gray-700 dark:text-gray-300 font-mono text-xs">ESC</kbd>
                        or click outside to close
                    </p>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
.fade-enter-active > div,
.fade-leave-active > div               { transition: transform 0.3s ease, opacity 0.3s ease; }
.fade-enter-from > div,
.fade-leave-to > div                   { transform: scale(0.95); opacity: 0; }
</style>
