    <script setup>
import { ref ,watch ,computed} from "vue";
import BaseModal from "./baseModal.vue";
import { useModalStack } from "../../composables/useModalStack.js";
import { useProjectStore } from "../../store/project-store.js";


import Input from "../../components/input.vue";
import TextArea from "../../components/textArea.vue";
import Select from "../../components/select.vue";
const store = useProjectStore();
const {  activeModal, closeModal,openModal } = useModalStack();
/* =========================
   Mode & UI State
========================= */
const mode = ref("create"); // create | edit
const currentProject = ref(null);
const projectColor = ref("#6B3EEA");


/* =========================
   Form State
========================= */
const projectName = ref("");
const projectDescription = ref("");
const projectStartDate = ref();
const projectEndDate = ref();
const projectStatus = ref("pending");

const statusOptions = [
  { value: "pending", text: "Pending" },
  { value: "in_progress", text: "In Progress" },
  { value: "done", text: "Done" },
];
/* =========================
   WATCHER (EDIT ONLY)
   store → modal (edit flow)
========================= */
watch(
  () => store.selectedProjectForModal,
  (project) => {
    if (!project) return;

    mode.value = "edit";
    currentProject.value = project;
    fillFromProject(project);
    openModal("project"); // modal opens automatically
  }
);

watch(
  () => activeModal.value,
  (modal) => {
    if (modal !== "project") return;

    // If no project selected → create mode
    if (!store.selectedProjectForModal) {
      mode.value = "create";
      currentProject.value = null;
      resetForm();
    }
  }
);

/* =========================
   ACTIONS
========================= */

async function handleSave() {

   if (mode.value === "edit" && !currentProject.value?.id) {
  show("error", "No project selected for editing");
  return;
}
  const payload = {
    name: projectName.value,
    description: projectDescription.value || "",
    start_date: projectStartDate.value || null,
    end_date: projectEndDate.value,
    status: projectStatus.value,
  };

  try {
   const result =
    mode.value === "create"
      ? await store.createProject(payload)
      : await store.editProject(currentProject.value.id, payload);

  if (result) {
closeProjectModal();
  }
  } catch (err) {
    // Show error in modal or console
show("error",err);  }
}
function closeProjectModal() {
  store.selectedProjectForModal = null;
  closeModal();
}

/* =========================
   Helpers
========================= */
function fillFromProject(project) {
  projectName.value = project.name || "";
  projectDescription.value = project.description || "";
  projectStartDate.value = project.start_date
    ? new Date(project.start_date).toISOString().split("T")[0]
    : "";
  projectEndDate.value = project.end_date
    ? new Date(project.end_date).toISOString().split("T")[0]
    : "";
  projectStatus.value = project.status || "pending";
}

function resetForm() {
  projectName.value = "";
  projectDescription.value = "";
  projectStartDate.value = "";
  projectEndDate.value = "";
  projectStatus.value = "pending";
}

</script>

<template>
    <BaseModal
        :show="activeModal === 'project'"
        title="Create Project"
        description="Organize your work"
        :color="projectColor"
        @close="closeModal"
    >
        <!-- content -->
        <Input
            v-model="projectName"
            label="Project Name"
            name="name"
            type="text"
            placeholder="Enter project name"
            required
            :colorHex="projectColor"
        />
        <TextArea
            v-model="projectDescription"
            label="Description"
            name="description"
            :rows="3"
            placeholder="Enter your description..."
            :colorHex="projectColor"
        ></TextArea>

        <Input
            v-model="projectStartDate"
            label="Start Date"
            name="start_date"
            type="date"
            :colorHex="projectColor"
        />
        <Input
            v-model="projectEndDate"
            label="DeadLine"
            name="end_date"
            type="date"
            :colorHex="projectColor"
        />

        <Select
            v-model="projectStatus"
            label="Status"
            name="status"
            :options="statusOptions"
            :colorHex="projectColor"
        ></Select>
        <template #footer>
            <div class="flex gap-2 justify-end">
                <button
                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-color cursor-pointer"
                    @click="closeProjectModal"
                >
                    Cancel
                </button>
                <button


                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 text-white rounded-lg font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-color cursor-pointer bg-indigo-500 hover:bg-indigo-600"
                @click="handleSave"
                >
                    {{ mode === "create" ? "Create" : "Update" }}
                </button>
            </div>
        </template>
    </BaseModal>
</template>
