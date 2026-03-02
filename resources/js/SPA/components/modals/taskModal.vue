<script setup>
/* =========================
   Imports
========================= */
import { onMounted, ref, watch } from "vue";
import { useModalStack } from "../../composables/useModalStack.js";
import { useTaskStore } from "../../store/task-store.js";
import { useProjectStore } from "../../store/project-store.js"

import BaseModal from "./baseModal.vue";
import Input from "../../components/input.vue";
import TextArea from "../../components/textArea.vue";
import Select from "../../components/select.vue";

/* =========================
   Props
========================= */
// const props = defineProps({
//   projects: { type: Array, default: () => [] },
// });

/* =========================
   Store & Modal Stack
========================= */
const store = useTaskStore();
const projectStore = useProjectStore()
const { openModal, activeModal, closeModal } = useModalStack();

const searchQuery = ref("");

let searchTimeout = null;

function onSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    projectStore.searchProjects(searchQuery.value);
  }, 300); // debounce 300ms
}

// Optional: clear results when query is empty
watch(searchQuery, (v) => {
  if (!v) projectStore.allProjects = [];
});
/* =========================
   Mode & UI State
========================= */
const mode = ref("create"); // create | edit
const currentTask = ref(null);
const taskColor = ref("#10B981");

/* =========================
   Form State
========================= */
const taskName = ref("");
const taskDescription = ref("");
const taskDueDate = ref("");
const taskPriority = ref("low");
const taskStatus = ref("pending");
const taskProject = ref("");

/* =========================
   Select Options
========================= */
const priorityOptions = [
  { value: "low", text: "Low" },
  { value: "medium", text: "Medium" },
  { value: "high", text: "High" },
];

const statusOptions = [
  { value: "pending", text: "Pending" },
  { value: "in_progress", text: "In Progress" },
  { value: "done", text: "Done" },
];

/* =========================
   Helpers
========================= */
function fillFormFromTask(task) {
  taskName.value = task.title || "";
  taskDescription.value = task.description || "";
  taskDueDate.value = task.due_date
    ? new Date(task.due_date).toISOString().split("T")[0]
    : "";
  taskPriority.value = task.priority || "";
  taskStatus.value = task.status || "pending";
  taskProject.value = task.project_id || "";
}

function resetForm() {
  taskName.value = "";
  taskDescription.value = "";
  taskDueDate.value = "";
  taskPriority.value = "low";
  taskStatus.value = "pending";
  taskProject.value = "";
}

/* =========================
   WATCHER (EDIT ONLY)
   store → modal (edit flow)
========================= */
watch(
  () => store.selectedTaskForModal,
  (task) => {
    if (!task) return;

    mode.value = "edit";
    currentTask.value = task;
    fillFormFromTask(task);
    openModal("task"); // modal opens automatically
  }
);



/* =========================
   ACTIONS
========================= */

// CREATE flow (manual open)
function openCreateTaskModal() {
  mode.value = "create";
  currentTask.value = null;
  resetForm();
  openModal("task");
}



async function handleSave() {

   if (mode.value === "edit" && !currentTask.value?.id) {
  show("error", "No task selected for editing");
  return;
}
  const payload = {
    title: taskName.value,
    description: taskDescription.value || "",
    due_date: taskDueDate.value || null,
    priority: taskPriority.value,
    status: taskStatus.value,
    project_id: taskProject.value || null,
    color: taskColor.value || "#10B981",
  };

  try {
   const result =
    mode.value === "create"
      ? await store.createTask(payload)
      : await store.editTask(currentTask.value.id, payload);

  if (result) {
closeTaskModal();
  }
  } catch (err) {
    // Show error in modal or console
show("error",err);  }
}
function closeTaskModal() {
  store.selectedTaskForModal = null;
  if(mode.value === "create") resetForm();
  closeModal();
}

</script>

<template>
    <!------ TASK MODAL ------>
    <BaseModal
        :show="activeModal === 'task'"
        :title="mode === 'create' ? 'Create Task' : 'Edit Task'"
        description="Add a new task to your project"
        @close="closeTaskModal"
        :color="taskColor"
    >

        <Input
            v-model="taskName"
            label="Task Name"
            name="name"
            type="text"
            placeholder="Enter task name"
            required
            :colorHex="taskColor"
        />
        <TextArea
            v-model="taskDescription"
            label="Description"
            name="description"
            :rows="3"
            placeholder="Enter your description..."
            :colorHex="taskColor"
        ></TextArea>
        <Input
            v-model="taskDueDate"
            label="Due Date"
            name="due_date"
            type="date"
            :colorHex="taskColor"
        />
        <Select
            v-model="taskPriority"
            label="Priority"
            name="priority"
            :options="priorityOptions"
            :colorHex="taskColor"
        ></Select>
        <Select
            v-model="taskStatus"
            label="Status"
            name="status"
            :options="statusOptions"
            :colorHex="taskColor"
        ></Select>

      <details
    class=" shrink-0 dark:bg-[#2A2A2A] dark:text-white border border-gray-300 dark:border-gray-500 rounded-xl overflow-hidden mt-4"
>
    <summary
        class="font-semibold text-sm px-4 py-3 cursor-pointer "
    >
        Add to <span class="text-[#5c2fd1]">Project</span>
    </summary>

    <div class="p-2  border-b border-gray-200 dark:border-b-gray-500">
        <input
            type="text"
            v-model="searchQuery"
            @input="onSearchInput"
            placeholder="Search projects..."
            class="dark:text-white w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#2A2A2A] text-black focus:outline-none focus:ring-2 focus:ring-[#5c2fd1] focus:border-transparent transition"
        />
    </div>

    <div class="divide-y divide-gray-200 dark:divide-gray-500 max-h-48 overflow-y-auto">
        <!-- Show projects if available -->
        <template v-if="projectStore.allProjects.length">
            <label
                v-for="project in projectStore.allProjects"
                :key="project.id"
                class="flex items-center px-4 py-3 cursor-pointer hover:bg-gray-200 dark:hover:dark:bg-[#3a3a3a]  transition-colors"
            >
                <input
                    type="radio"
                    name="project_id"
                    :value="project.id"
                    v-model="taskProject"
                    class="mr-3 text-[#5c2fd1] focus:ring-[#5c2fd1] "
                />
                <span class="truncate text-sm">{{ project.name }}</span>
            </label>
        </template>

        <!-- Show "no results" if search is active but empty -->
        <div
            v-else-if="searchQuery"
            class="px-4 py-8 text-gray-500 text-sm text-center"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <p class="font-medium">No projects found</p>
            <p class="text-xs mt-1">Try a different search term</p>
        </div>

        <!-- Show "no projects" if no search and no projects -->
        <div
            v-else
            class="px-4 py-8 text-gray-500 text-sm text-center"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium">No projects yet</p>
            <p class="text-xs mt-1">Create your first project below</p>
        </div>
    </div>

    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-500 dark:bg-[#2A2A2A] dark:text-white">
        <button
            type="button"
            @click="openModal('project')"
            class=" cursor-pointer w-full px-4 py-2 bg-white dark:bg-[#2A2A2A] border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium"
        >
            + New Project
        </button>
    </div>
</details>

        <template #footer>
            <div class="flex gap-2 justify-end">
                <button
                    class="cursor-pointer w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all"
                    @click="closeTaskModal"
                >
                    Cancel
                </button>
                <button

                    class="cursor-pointer w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 text-white rounded-lg font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all bg-emerald-500 hover:bg-emerald-600"
                    @click="handleSave"
                >
                    {{ mode === "create" ? "Create" : "Update" }}
                </button>
            </div>
        </template>
    </BaseModal>
</template>
