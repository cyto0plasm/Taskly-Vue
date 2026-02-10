<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useTaskStore } from '../store/taskStore.js'
import { storeToRefs } from 'pinia'
import CheckIcon from '../svg/CheckIcon.vue'
import ProgressIcon from '../svg/progressIcon.vue'
import PendingIcon from '../svg/pendingIcon.vue'
import styledButton from '../components/styledButton.vue'
import { formatDate } from '../../utils/formatData.js'
import { useModalStack } from '../composables/useModalStack.js'
import DetailSkeleton from "./components/details/skeleton.vue"
import DetailEmpty from "./components/details/empty.vue"
const { openModal } = useModalStack();


// ===== Store =====
const store = useTaskStore()


const selectedTask = computed(() => store.selectedTask)
const selectedTaskId = computed(() => store.selectedTaskId)
const loadingTask = computed(() => store.loadingSelectedTask)

// ===== Refs =====
const descriptionExpanded = ref(false)
const descriptionRef = ref(null)
const isTextClamped = ref(false)
const showSkeleton = ref(false)
const isUpdating = ref(false)

// ===== Computed Properties =====
const statusClass = (task) => {
  if (!task) return ''
  return {
    done: 'bg-emerald-100 text-emerald-800',
    pending: 'bg-red-100 text-red-600',
    in_progress: 'bg-yellow-100 text-yellow-800'
  }[task.status] || ''
}

const isOverdue = computed(() => {
  if (!selectedTask.value?.due_date) return false
  const due = new Date(selectedTask.value.due_date)
  const now = new Date()
  return due < now && selectedTask.value.status !== 'done'
})

const isDueSoon = computed(() => {
  if (!selectedTask.value?.due_date) return false
  const due = new Date(selectedTask.value.due_date)
  const now = new Date()
  const threeDaysFromNow = new Date(now.getTime() + (3 * 24 * 60 * 60 * 1000))
  return due >= now && due <= threeDaysFromNow && selectedTask.value.status !== 'done'
})

// ===== Functions =====
function editTask(task) {
  store.setSelectedTaskForModal(task)
//   openModal("task")
}

const checkIfClamped = () => {
  if (!descriptionRef.value) return
  isTextClamped.value = descriptionRef.value.scrollHeight > descriptionRef.value.clientHeight
}
const updateStatus = async () => {
  if (isUpdating.value || !selectedTask.value) return
  isUpdating.value = true

  try {
    await store.updateTaskStatus(selectedTask.value.id, 'done')
  } catch (err) {
    console.error(err)
  } finally {
    isUpdating.value = false
  }
}


// ===== Watchers =====
// Loading skeleton with delay
const skeletonDelay = 150
let skeletonTimer = null

watch(loadingTask, (isLoading) => {
  if (isLoading) {
    skeletonTimer = setTimeout(() => (showSkeleton.value = true), skeletonDelay)
  } else {
    clearTimeout(skeletonTimer)
    showSkeleton.value = false
  }
}, { immediate: true });

// Reset description state when task changes
watch(selectedTaskId, (id) => {
  if (!id) return;  // Guard undefined
  descriptionExpanded.value = false;
});

watch(selectedTask, (task) => {
  if (!task) return; // Guard undefined
  descriptionExpanded.value = false;
  nextTick(checkIfClamped);
}, { deep: true });

</script>

<template>
    <!-- Loading Skeleton -->

<DetailSkeleton  v-if="showSkeleton"></DetailSkeleton>

 <!-- Empty State (No Task Selected)  -->
<DetailEmpty v-else-if="!selectedTask"></DetailEmpty>

<div v-else id="taskDetailContent"
    class="w-full bg-[#ffffff] dark:bg-[#222321] rounded-lg shadow-md p-4 sm:p-6  h-auto min-h-[18rem] sm:min-h-[20rem] flex flex-col gap-4 overflow-hidden">

     <!-- Status Badge  -->
    <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
        <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center flex-shrink-0">

            <span v-if="selectedTask.status === 'done'"  class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                <CheckIcon :size="20" color="white" />
            </span>
            <span v-else-if="selectedTask.status === 'in_progress'">
                <ProgressIcon :size="24" />
            </span>
            <span v-else-if="selectedTask.status === 'pending'">
                <PendingIcon :size="24" />
            </span>

        </div>
        <span id="status"
            class="task-state px-2.5 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full whitespace-nowrap"
           :class="statusClass(selectedTask)">
            <!-- {{ ucfirst(str_replace('_', ' ', $firstTask?->status ?? 'In Progress')) }} -->
            {{ selectedTask.status === 'in_progress'
                ? 'In Progress'
                : selectedTask.status?.charAt(0).toUpperCase() + selectedTask.status?.slice(1)
            }}
        </span>
    </div>

     <!-- Task Title  -->
    <h1 class="task-title text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white  break-words overflow-wrap-anywhere">
        <!-- {{ $firstTask?->title ?? 'Task Title' }} -->
        {{ selectedTask.title ?? '—' }}
    </h1>

    <!-- Task Description -->
   <div v-if="selectedTask.description" class="relative">
      <div class="prose prose-sm sm:prose-base dark:prose-invert max-w-none overflow-hidden">
       <p
  ref="descriptionRef"
  id="task-description"
  class="text-gray-700 dark:text-gray-300 leading-relaxed break-words overflow-wrap-anywhere"
 :class="{
  'line-clamp-3': !descriptionExpanded
}"
>
  {{ selectedTask.description }}
</p>

      </div>
<button v-if="isTextClamped || descriptionExpanded"
      @click="descriptionExpanded = !descriptionExpanded"
        id="toggleDescription"
        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium mt-2 focus:outline-none focus:underline">
        {{ descriptionExpanded ? '← Show less' : 'Show more →' }}
      </button>
    </div>
    <div v-else class="text-gray-400 dark:text-gray-600 italic text-sm">
      No description provided
    </div>

    <!-- Info Grid -->
     <div class="flex flex-col sm:flex-row gap-3 mb-2 sm:mb-4 flex-wrap">
  <!-- Task Details Card -->
  <div
  class="rounded-xl p-3 sm:p-4 flex-1 min-w-[220px] max-w-full
         bg-gradient-to-br from-gray-50 to-gray-100
         dark:from-gray-800 dark:to-gray-900
         border border-gray-200 dark:border-gray-600
         transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800
         overflow-hidden"><h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
      <span class="w-1 h-5 bg-emerald-500 rounded-full flex-shrink-0"></span>
      Details
    </h3>
<ul class="space-y-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">
      <li class="flex items-center gap-2 min-w-0">
        <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0 mt-1.5"></span>
        <span class="min-w-0 break-words">Priority: <span
          class="font-medium"
          :class="{
            'text-red-600 dark:text-red-400': selectedTask.priority === 'high',
            'text-yellow-600 dark:text-yellow-400': selectedTask.priority === 'medium'
          }">
          {{ selectedTask.priority ? selectedTask.priority.charAt(0).toUpperCase() + selectedTask.priority.slice(1) : 'No priority' }}
        </span></span>
      </li>
      <li class="flex items-center gap-2 min-w-0">
        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
        <span class="min-w-0 break-words">Type: <span class="font-medium">
          {{ selectedTask.type ? selectedTask.type.charAt(0).toUpperCase() + selectedTask.type.slice(1) : 'No type' }}
        </span></span>
      </li>
      <li class="flex items-center gap-2 min-w-0">
        <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-1.5"></span>
        <span class="min-w-0 break-words">Project: <span class="font-medium break-words" :title="selectedTask.project?.name">
          {{ selectedTask.project?.name ?? 'No project' }}
        </span></span>
      </li>
    </ul>
  </div>

  <!-- Timeline Card -->
  <div
  class="rounded-xl p-3 sm:p-4 flex-1 min-w-[220px] max-w-full
         bg-gradient-to-br from-gray-50 to-gray-100
         dark:from-gray-800 dark:to-gray-900
         border border-gray-200 dark:border-gray-600
         transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800
         overflow-hidden"><h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
      <span class="w-1 h-5 bg-blue-500 rounded-full flex-shrink-0"></span>
      Timeline
    </h3>
<ul class="space-y-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">
      <li class="flex items-start gap-2 min-w-0">
        <span class="min-w-0 break-words">Due date: <span class="font-medium"
          :class="{
            'text-red-600 dark:text-red-400': isOverdue,
            'text-yellow-600 dark:text-yellow-400': isDueSoon,
            'text-gray-900 dark:text-gray-100': !isOverdue && !isDueSoon
          }">
          {{ formatDate(selectedTask.due_date) ?? 'No due date' }}
        </span></span>
      </li>
      <li class="flex items-start gap-2 min-w-0">
        <span class="min-w-0 break-words">Created: <span class="font-medium">{{ formatDate(selectedTask.created_at) ?? 'No created date' }}</span></span>
      </li>
      <li class="flex items-start gap-2 min-w-0">
        <span class="min-w-0 break-words">Updated: <span class="font-medium">{{ formatDate(selectedTask.updated_at) ?? 'No updated date' }}</span></span>
      </li>
    </ul>
  </div>
</div>


    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-wrap">
        <!-- Mark Complete -->

            <styledButton
             v-if="selectedTask && selectedTask.status !== 'done'"
              :disabled="isUpdating"
                @click="updateStatus"
                type="submit" bgColor="bg-[#10b981]" hoverColor="hover:bg-[#04bd7f]"
                activeColor="active:bg-[#36bd90]" textColor="text-white" text="✓ Mark as Complete"
                class="w-full sm:w-auto" />


        <!-- <x-mark-status-button :taskId="$firstTask?->id" /> -->

        <!-- Edit Task -->
        <styledButton
  @click="editTask(selectedTask)"
  id="task-edit-btn"
  bgColor="bg-gray-200"
  hoverColor="hover:bg-gray-100"
  activeColor="active:bg-gray-300"
  textColor="text-[#0c8059]"
  text="Edit Task"
  class="w-full sm:w-auto"
  type="button"
/>




    </div>
</div>
</template>
