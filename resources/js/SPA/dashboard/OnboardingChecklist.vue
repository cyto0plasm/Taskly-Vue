<!-- OnboardingChecklist.vue -->
<script setup>
import { ref, computed, watch } from "vue";

const STORAGE_KEY = "onboarding_v1";

const steps = [
  { id: "create_task",  title: "Create your first task", desc: "Add a task from the task list" },
  { id: "set_deadline", title: "Set a deadline",          desc: "Pick a due date for any task"  },
  { id: "mark_done",    title: "Mark a task done",        desc: "Complete your first task"      },
];

function loadState() {
  try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) ?? {}; }
  catch { return {}; }
}

const completed = ref(loadState());
const dismissed = ref(loadState().dismissed ?? false);
const expanded  = ref(true);

watch(completed, val => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify({ ...val, dismissed: dismissed.value }));
}, { deep: true });

const doneCount = computed(() => steps.filter(s => completed.value[s.id]).length);
const allDone   = computed(() => doneCount.value === steps.length);
const progress  = computed(() => Math.round((doneCount.value / steps.length) * 100));

function toggle(id) {
  completed.value = { ...completed.value, [id]: !completed.value[id] };
}
function dismiss() {
  dismissed.value = true;
  localStorage.setItem(STORAGE_KEY, JSON.stringify({ ...completed.value, dismissed: true }));
}
function markStep(id) {
  if (!completed.value[id]) completed.value = { ...completed.value, [id]: true };
}

defineExpose({ markStep });
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4"
    leave-active-class="transition duration-200 ease-in"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div
  v-if="!dismissed"
  class="fixed bottom-1 right-18 md:bottom-3 md:right-25 z-50 w-[calc(100vw-1.5rem)] max-w-[18rem] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#232422] shadow-xl overflow-hidden"
>
      <!-- All done -->
      <div v-if="allDone" class="flex items-center gap-2 px-3 py-2.5">
        <span class="flex items-center justify-center w-4 h-4 rounded-full bg-green-500 shrink-0">
          <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="white" stroke-width="2">
            <polyline points="2 6 5 9 10 3"/>
          </svg>
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400 flex-1">All done!</span>
        <button class="text-[11px] text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer" @click="dismiss">Dismiss</button>
      </div>

      <!-- Normal state -->
      <template v-else>
        <!-- Header -->
        <div class="flex items-center justify-between px-3 py-2.5 cursor-pointer select-none" @click="expanded = !expanded">
          <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-6 h-6 rounded-md bg-blue-50 dark:bg-blue-900/40 text-blue-500 shrink-0">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
              </svg>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 leading-none">Get started</p>
              <p class="text-[10px] text-gray-400 mt-0.5">{{ doneCount }}/{{ steps.length }} complete</p>
            </div>
          </div>
          <svg
            class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
            :class="{ 'rotate-180': expanded }"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          >
            <polyline points="18 15 12 9 6 15"/>
          </svg>
        </div>

        <!-- Progress bar -->
        <div class="h-0.5 bg-gray-100 dark:bg-gray-700">
          <div class="h-full bg-blue-500 transition-all duration-500" :style="{ width: progress + '%' }" />
        </div>

        <!-- Steps -->
        <Transition
          enter-active-class="transition-opacity duration-150"
          enter-from-class="opacity-0"
          leave-active-class="transition-opacity duration-100"
          leave-to-class="opacity-0"
        >
          <div v-if="expanded" class="px-3 pb-2 pt-1">
            <div
              v-for="(step, i) in steps" :key="step.id"
              class="flex items-center gap-2.5 py-1.5 cursor-pointer group"
              :class="{ 'border-b border-gray-100 dark:border-gray-700': i < steps.length - 1 }"
              @click="toggle(step.id)"
            >
              <div
                class="flex items-center justify-center w-4 h-4 rounded-full border shrink-0 transition-all duration-150"
                :class="completed[step.id]
                  ? 'bg-green-500 border-green-500'
                  : 'border-gray-300 dark:border-gray-600 group-hover:border-blue-400'"
              >
                <svg v-if="completed[step.id]" width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="white" stroke-width="2.5">
                  <polyline points="2 6 5 9 10 3"/>
                </svg>
              </div>

              <div class="flex-1 min-w-0">
                <p class="text-xs leading-none"
                   :class="completed[step.id] ? 'line-through text-gray-400' : 'text-gray-700 dark:text-gray-200'">
                  {{ step.title }}
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ step.desc }}</p>
              </div>

              <span
                v-if="!completed[step.id] && i === doneCount"
                class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-500 shrink-0"
              >Next</span>
            </div>
          </div>
        </Transition>
      </template>
    </div>
  </Transition>
</template>
