import { closeModal, openModal } from "../../utils/modal/modal.js";
import * as TaskController from "../../controllers/tasks/TaskController.js";
import { populateTaskModal } from "../ui/populateTaskModal.js";
import taskState from "../../domain/tasks/TaskState.js";

import { setEditMode, resetCreateMode } from "./taskFormMode.js";
import { buildFormData } from "./taskFormSerializer.js";
import { hasTaskChanges } from "./taskFormDiff.js";

export function setupTaskCreateForm() {
    const form = document.getElementById("task-modal-form");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const taskId = form.dataset.taskId;
        if (taskId) setEditMode(form, taskId);

        const formData = buildFormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        toggleSubmit(submitBtn, true, taskId);

        try {
            if (taskId) {
                const task = taskState.get(taskId);

                if (!hasTaskChanges(task, formData)) {
                    closeModal("task-modal");
                    return;
                }

                const updated = await TaskController.updateTask(
                    taskId,
                    formData
                );
                taskState.addAndSelect(updated);
            } else {
                const created = await TaskController.createTask(formData);
                taskState.addAndSelect(created);
            }

            closeModal("task-modal");
            resetCreateMode(form);
        } catch (err) {
            console.error("Form submission failed:", err);
        } finally {
            toggleSubmit(submitBtn, false, taskId);
        }
    });
}

function toggleSubmit(btn, loading, taskId) {
    if (!btn) return;
    btn.disabled = loading;
    btn.textContent = loading
        ? taskId
            ? "Updating..."
            : "Creating..."
        : taskId
        ? "Update Task"
        : "Create Task";
}
