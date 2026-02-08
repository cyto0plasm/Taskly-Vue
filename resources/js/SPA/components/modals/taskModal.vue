<script setup>
/* =========================
   Imports
========================= */
import { ref, watch } from "vue";
import { useModalStack } from "../../composables/useModalStack.js";
import { useTaskStore } from "../../store/taskStore.js";

import BaseModal from "./baseModal.vue";
import Input from "../../components/input.vue";
import TextArea from "../../components/textArea.vue";
import Select from "../../components/select.vue";

/* =========================
   Props
========================= */
const props = defineProps({
  projects: { type: Array, default: () => [] },
});

/* =========================
   Store & Modal Stack
========================= */
const store = useTaskStore();
const { openModal, activeModal, closeModal } = useModalStack();

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
  taskColor.value = task.color || "#10B981";
}

function resetForm() {
  taskName.value = "";
  taskDescription.value = "";
  taskDueDate.value = "";
  taskPriority.value = "low";
  taskStatus.value = "pending";
  taskProject.value = "";
  taskColor.value = "#10B981";
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

watch(
  () => activeModal.value,
  (modal) => {
    if (modal !== "task") return;

    // If no task selected → create mode
    if (!store.selectedTaskForModal) {
      mode.value = "create";
      currentTask.value = null;
      resetForm();
    }
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

// EDIT flow (optional direct call)
function openEditTaskModal(task) {
  mode.value = "edit";
  currentTask.value = task;
  fillFormFromTask(task);
  openModal("task");
}

async function handleSave() {
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
    let result = null;

    if (mode.value === "create") result = await store.createTask(payload);
    else result = await store.editTask(currentTask.value.id, payload);

    if (result) closeModal();
  } catch (err) {
    // Show error in modal or console
    alert(err.message); // temporary, or replace with working flash
  }
}

</script>

<template>
    <!------ TASK MODAL ------>
    <BaseModal
        :show="activeModal === 'task'"
        :title="mode === 'create' ? 'Create Task' : 'Edit Task'"
        description="Add a new task to your project"
        @close="closeModal"
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
            class=" bg-gray-50 border border-gray-200 rounded-xl overflow-hidden mt-4"
        >
            <summary
                class="font-semibold text-sm px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors"
            >
                Add to <span class="text-[#5c2fd1]">Project</span>
            </summary>

            <div class="divide-y divide-gray-200 max-h-48 overflow-y-auto">
                <template v-if="projects.length">
                    <label
                        v-for="project in projects"
                        :key="project.id"
                        class="flex items-center px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors"
                    >
                        <input
                            type="radio"
                            name="project_id"
                            :value="project.id"
                            v-model="taskProject"
                            class="mr-3 text-[#5c2fd1] focus:ring-[#5c2fd1]"
                        />
                        <span class="truncate text-sm">{{ project.name }}</span>
                    </label>
                </template>
                <div
                    v-else
                    class="px-4 py-3 text-gray-500 text-sm italic text-center"
                >
                    No projects yet!
                </div>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 bg-white">
                <button
                    type="button"
                    @click="openModal('project')"
                    class="w-full px-4 py-2 bg-white border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium"
                >
                    + New Project
                </button>
            </div>
        </details>

        <template #footer>
            <div class="flex gap-2">
                <button
                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all"
                    @click="closeModal"
                >
                    Cancel
                </button>
                <button
                    :style="{ backgroundColor: taskColor }"
                    @mouseover="taskColor = '#0ea472'"
                    @mouseleave="taskColor = '#10B981'"
                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 text-white rounded-lg font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    @click="handleSave"
                >
                    {{ mode === "create" ? "Create" : "Update" }}
                </button>
            </div>
        </template>
    </BaseModal>
</template>
