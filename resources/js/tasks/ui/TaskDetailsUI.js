// ui/tasks/TaskDetailsUI.js
import { $, setText } from "../../utils/domHelpers.js";
const skeletonEl = document.querySelector("#taskDetailSkeleton");
const emptyEl = document.querySelector("#taskDetailEmpty");
const contentEl = document.querySelector("#taskDetailContent");

// Inline SVGs matching your Blade components
const SVGs = {
    done: `<svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd" />
           </svg>`,
    pending: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
                <defs><linearGradient id="redGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#DC2626" />
                    <stop offset="50%" stop-color="#EF4444" />
                    <stop offset="100%" stop-color="#F87171" />
                </linearGradient></defs>
                <circle cx="12" cy="12" r="9" fill="none" stroke="rgba(0,0,0,0.1)" stroke-width="2.5"/>
                <circle cx="12" cy="12" r="9" fill="none" stroke="url(#redGradient)" stroke-width="2.5">
                    <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite"/>
                </circle>
                <circle cx="12" cy="12" r="6" fill="url(#redGradient)">
                    <animate attributeName="opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite"/>
                </circle>
              </svg>`,
    in_progress: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
                    <defs><linearGradient id="yellowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#F59E0B"/>
                        <stop offset="50%" stop-color="#EAB308"/>
                        <stop offset="100%" stop-color="#FBBF24"/>
                    </linearGradient></defs>
                    <circle cx="12" cy="12" r="10" fill="url(#yellowGradient)">
                        <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="12" cy="12" r="5" fill="white">
                        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite"/>
                    </circle>
                  </svg>`,
};
export function showSkeleton() {
    skeletonEl?.classList.remove("hidden");
    emptyEl?.classList.add("hidden");
    contentEl?.classList.add("hidden");
}

export function showEmpty() {
    skeletonEl?.classList.add("hidden");
    emptyEl?.classList.remove("hidden");
    contentEl?.classList.add("hidden");
}

export function showContent() {
    skeletonEl?.classList.add("hidden");
    emptyEl?.classList.add("hidden");
    contentEl?.classList.remove("hidden");
}

export function renderTaskDetails({ task, loading }) {
    if (loading) return showSkeleton();
    if (!task) return showEmpty();
    populateTaskDetails(task);
    showContent();
}

function populateTaskDetails(task) {
    if (!contentEl) return;

    // Text fields
    setText(".task-title", task.title || "Untitled Task");
    setText(".task-description", task.description || "No description");
    setText(".task-type", task.type ? capitalize(task.type) : "-");
    setText(".project", task.project?.name || "-");
    setText(".task-time", task.due_date || "-");
    setText(
        ".task-Created-at",
        task.created_at ? formatDate(task.created_at) : "-"
    );
    setText(
        ".task-updated-at",
        task.updated_at ? formatDate(task.updated_at) : "-"
    );

    // Priority
    setText(".task-priority", capitalize(task.priority || "medium"));

    // Status badge + icon
    const statusBadge = $("#status");
    const iconContainer = statusBadge?.parentElement?.querySelector("div");

    if (statusBadge && iconContainer) {
        const status = task.status || "in_progress";

        iconContainer.innerHTML = SVGs[status] || SVGs.in_progress;
        statusBadge.textContent = capitalize(status.replace("_", " "));
        statusBadge.className =
            "task-state px-2.5 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full";

        const statusClasses = {
            done: ["bg-emerald-100", "text-emerald-800"],
            pending: ["bg-red-100", "text-red-600"],
            in_progress: ["bg-yellow-100", "text-yellow-800"],
        };

        statusBadge.classList.add(
            ...(statusClasses[status] || statusClasses.in_progress)
        );
    }

    updateFormActions(task.id);
}

function updateFormActions(taskId) {
    const updateForm = contentEl?.querySelector("#update-status-form");
    if (updateForm) {
        updateForm.dataset.taskId = taskId;
        updateForm.action = `/tasks/${taskId}/status-update`;
    }

    const editForm = contentEl?.querySelector("#task-edit-form");
    if (editForm) editForm.action = `/tasks/${taskId}/edit`;
}

function capitalize(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateStr) {
    if (!dateStr) return "-";
    try {
        return new Date(dateStr).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    } catch {
        return dateStr;
    }
}
