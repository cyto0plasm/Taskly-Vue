import { resetCreateMode } from "./taskFormMode.js";

export function setupTaskModalLifecycle() {
    document.addEventListener("click", (e) => {
        const openBtn = e.target.closest('[data-modal-open="task-modal"]');
        if (!openBtn) return;

        if (
            !e.target.closest("[data-task-edit]") &&
            !e.target.closest("#task-edit-btn")
        ) {
            const form = document.getElementById("task-modal-form");
            if (form) resetCreateMode(form);
        }
    });
}
