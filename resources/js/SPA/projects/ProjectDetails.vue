<script setup>
import { ref, computed, watch, nextTick } from "vue";
import { useProjectStore } from "../store/project-store.js";
import CheckIcon from "../svg/CheckIcon.vue";
import ProgressIcon from "../svg/progressIcon.vue";
import PendingIcon from "../svg/pendingIcon.vue";
import styledButton from "../components/styledButton.vue";
import { formatDate } from "../../utils/formatData.js";
import { useModalStack } from "../composables/useModalStack.js";
import DetailSkeleton from "../components/main//details/skeleton.vue";
import DetailEmpty from "../components/main//details/empty.vue";

const { openModal } = useModalStack();

// ===== Store =====
const store = useProjectStore();

// ✅ CHANGED: Use the getter that includes tasks
const selectedProject = computed(() => store.selectedProjectWithTasks);
const selectedProjectId = computed(() => store.selectedProjectId);
const loadingProject = computed(() => store.loadingSelectedProject);

// ===== Refs =====
const descriptionExpanded = ref(false);
const descriptionRef = ref(null);
const isTextClamped = ref(false);
const showSkeleton = ref(false);
const isUpdating = ref(false);

// ===== Computed Properties =====
const statusClass = (project) => {
    if (!project) return "";
    return (
        {
            done: "bg-emerald-100 text-emerald-800",
            pending: "bg-red-100 text-red-600",
            in_progress: "bg-yellow-100 text-yellow-800",
        }[project.status] || ""
    );
};

const isOverdue = computed(() => {
    if (!selectedProject.value?.end_date) return false;
    const end = new Date(selectedProject.value.end_date);
    const now = new Date();
    return end < now && selectedProject.value.status !== "done";
});

const isDueSoon = computed(() => {
    if (!selectedProject.value?.end_date) return false;
    const end = new Date(selectedProject.value.end_date);
    const now = new Date();
    const threeDaysFromNow = new Date(now.getTime() + 3 * 24 * 60 * 60 * 1000);
    return (
        end >= now &&
        end <= threeDaysFromNow &&
        selectedProject.value.status !== "done"
    );
});

// ===== Functions =====
function editProject(project) {
    store.setSelectedProjectForModal(project);
    // openModal("project")
}

const checkIfClamped = () => {
    if (!descriptionRef.value) return;
    isTextClamped.value =
        descriptionRef.value.scrollHeight > descriptionRef.value.clientHeight;
};

const updateStatus = async () => {
    if (isUpdating.value || !selectedProject.value) return;
    isUpdating.value = true;

    try {
        await store.updateProjectStatus(selectedProject.value.id, "done");
    } catch (err) {
        console.error(err);
    } finally {
        isUpdating.value = false;
    }
};

// ===== Watchers =====
const skeletonDelay = 150;
let skeletonTimer = null;

watch(
    loadingProject,
    (isLoading) => {
        if (isLoading) {
            skeletonTimer = setTimeout(
                () => (showSkeleton.value = true),
                skeletonDelay,
            );
        } else {
            clearTimeout(skeletonTimer);
            showSkeleton.value = false;
        }
    },
    { immediate: true },
);

watch(selectedProjectId, (id) => {
    if (!id) return;
    descriptionExpanded.value = false;
});

watch(
    selectedProject,
    (project) => {
        if (!project) return;
        descriptionExpanded.value = false;
        nextTick(checkIfClamped);
    },
    { deep: true },
);
</script>

<template>
    <!-- Loading Skeleton -->
    <DetailSkeleton v-if="showSkeleton"></DetailSkeleton>

    <!-- Empty State -->
    <DetailEmpty type="project" v-else-if="!selectedProject"></DetailEmpty>

    <div v-else id="projectDetailContent"
        class="w-full bg-[#ffffff] dark:bg-[#222321] rounded-lg shadow-md p-4 sm:p-6 h-auto min-h-72 sm:min-h-80 flex flex-col gap-4 overflow-hidden">

        <!-- Status Badge -->
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center shrink-0">
                <span v-if="selectedProject.status === 'done'"
                    class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                    <CheckIcon :size="20" color="white" />
                </span>
                <span v-else-if="selectedProject.status === 'in_progress'">
                    <ProgressIcon :size="24" />
                </span>
                <span v-else-if="selectedProject.status === 'pending'">
                    <PendingIcon :size="24" />
                </span>
            </div>
            <span id="status"
                class="project-state px-2.5 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full whitespace-nowrap"
                :class="statusClass(selectedProject)">
                {{
                    selectedProject.status === "in_progress"
                        ? "In Progress"
                        : selectedProject.status?.charAt(0).toUpperCase() +
                        selectedProject.status?.slice(1)
                }}
            </span>
        </div>

        <!-- Project Title -->
        <h1
            class="project-name text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white wrap-break-word overflow-wrap-anywhere">
            {{ selectedProject.name ?? "—" }}
        </h1>

        <!-- Project Description -->
        <div v-if="selectedProject.description" class="relative">
            <div class="prose prose-sm sm:prose-base dark:prose-invert max-w-none overflow-hidden">
                <p ref="descriptionRef" id="project-description"
                    class="text-gray-700 dark:text-gray-300 leading-relaxed wrap-break-word overflow-wrap-anywhere"
                    :class="{ 'line-clamp-3': !descriptionExpanded }">
                    {{ selectedProject.description }}
                </p>
            </div>
            <button v-if="isTextClamped || descriptionExpanded"
                @click="descriptionExpanded = !descriptionExpanded"
                id="toggleDescription"
                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium mt-2 focus:outline-none focus:underline">
                {{ descriptionExpanded ? "← Show less" : "Show more →" }}
            </button>
        </div>
        <div v-else class="text-gray-400 dark:text-gray-600 italic text-sm">
            No description provided
        </div>

        <!-- Info Grid -->
        <div class="flex flex-col sm:flex-row gap-3 mb-2 sm:mb-4 flex-wrap">
            <!-- Project Details Card -->
            <div
                class="rounded-xl p-3 sm:p-4 flex-1 min-w-55 max-w-full bg-linear-to-br from-[#eeeeee] to-[#fffbfb] dark:from-[#232422] dark:to-[#20232a] border border-gray-200 dark:border-gray-600 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800 overflow-hidden flex flex-col max-h-50">

                <!-- Fixed Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3
                        class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 shrink-0">
                        <span class="w-1 h-5 bg-indigo-500 rounded-full shrink-0"></span>
                        Tasks assigned
                    </h3>
                    <!-- ✅ This works because selectedProject now has tasks from the getter -->
                    <span class="bg-indigo-500 rounded-full px-1 py-0.5 text-center text-white font-medium text-sm">
                        {{ selectedProject.tasks?.length || 0 }}
                    </span>
                </div>

                <!-- Scrollable Tasks Area -->
                <!-- ✅ This works because selectedProject.tasks comes from the getter -->
                <div class="overflow-y-auto flex-1">
                    <ul v-if="selectedProject.tasks?.length > 0" class="space-y-2">
                        <li v-for="task in selectedProject.tasks" :key="task.id"
                            class="flex items-center gap-2 text-sm sm:text-base shadow-sm bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-lg p-1 text-gray-600 dark:text-gray-300">
                            <div>
                                <CheckIcon v-if="task.status == 'done'" :size="15" color="white" />
                                <ProgressIcon v-else-if="task.status == 'in_progress'" :size="15" color="white" />
                                <PendingIcon v-else :size="15" color="white" />
                            </div>
                            <span>{{ task.title }}</span>
                        </li>
                    </ul>

                    <p v-else class="text-gray-400 dark:text-gray-600 italic text-sm">
                        No tasks assigned to this project
                    </p>
                </div>
            </div>

            <!-- Timeline Card -->
            <div
                class="rounded-xl p-3 sm:p-4 flex-1 min-w-55 max-w-full bg-linear-to-br from-[#eeeeee] to-[#fffbfb] dark:from-[#232422] dark:to-[#20232a] border border-gray-200 dark:border-gray-600 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800 overflow-hidden">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full shrink-0"></span>
                    Timeline
                </h3>
                <ul class="space-y-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">
                    <li class="flex items-start gap-2 min-w-0">
                        <span class="min-w-0 wrap-break-word">Start date:
                            <span class="font-medium" :class="{
                                'text-red-600 dark:text-red-400': isOverdue,
                                'text-yellow-600 dark:text-yellow-400': isDueSoon,
                                'text-gray-900 dark:text-gray-100': !isOverdue && !isDueSoon,
                            }">
                                {{ formatDate(selectedProject.start_date) ?? "No start date" }}
                            </span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2 min-w-0">
                        <span class="min-w-0 wrap-break-word">End date:
                            <span class="font-medium" :class="{
                                'text-red-600 dark:text-red-400': isOverdue,
                                'text-yellow-600 dark:text-yellow-400': isDueSoon,
                                'text-gray-900 dark:text-gray-100': !isOverdue && !isDueSoon,
                            }">
                                {{ formatDate(selectedProject.end_date) ?? "No end date" }}
                            </span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2 min-w-0 pt-2">
                        <span class="min-w-0 wrap-break-word">Created:
                            <span class="font-medium">
                                {{ formatDate(selectedProject.created_at) ?? "No created date" }}
                            </span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2 min-w-0">
                        <span class="min-w-0 wrap-break-word">Updated:
                            <span class="font-medium">
                                {{ formatDate(selectedProject.updated_at) ?? "No updated date" }}
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-wrap">
            <styledButton v-if="selectedProject && selectedProject.status !== 'done'"
                :disabled="isUpdating"
                @click="updateStatus"
                type="submit"
                bgColor="bg-[#10b981]"
                hoverColor="hover:bg-[#04bd7f]"
                activeColor="active:bg-[#36bd90]"
                textColor="text-white"
                text="✓ Mark as Complete"
                class="w-full sm:w-auto" />

            <styledButton @click="editProject(selectedProject)"
                id="project-edit-btn"
                bgColor="bg-gray-200"
                hoverColor="hover:bg-gray-100"
                activeColor="active:bg-gray-300"
                textColor="text-[#0c8059]"
                text="Edit Project"
                class="w-full sm:w-auto"
                type="button" />
        </div>
    </div>
</template>
