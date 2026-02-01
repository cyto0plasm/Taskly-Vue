/**
 * Populate task modal form with existing task data for editing
 * @param {Object} task - Task object to populate
 */
export function populateTaskModal(task) {
    if (!task) return;

    const form = document.getElementById("task-modal-form");
    if (!form) return;

    // Set form to EDIT mode
    form.dataset.taskId = task.id;

    // Populate form fields
    const titleInput = document.getElementById("task-title");
    const descriptionInput = document.getElementById("task-description");
    const dueDateInput = document.getElementById("task-due-date");
    const prioritySelect = document.getElementById("task-priority");
    const statusSelect = document.getElementById("task-status");

    if (titleInput) titleInput.value = task.title || "";
    if (descriptionInput) descriptionInput.value = task.description || "";
    if (dueDateInput) dueDateInput.value = task.due_date || "";
    if (prioritySelect) prioritySelect.value = task.priority || "medium";
    if (statusSelect) statusSelect.value = task.status || "pending";

    // Select project if exists
    if (task.project_id) {
        const projectRadio = form.querySelector(
            `input[name="project_id"][value="${task.project_id}"]`
        );
        if (projectRadio) projectRadio.checked = true;
    }

    // Update modal UI for edit mode
    const title = document.getElementById("task-modal-title");
    const description = document.getElementById("task-modal-description");
    const submitBtn = form.querySelector('button[type="submit"]');

    if (title) title.textContent = "Edit Task";
    if (description) description.textContent = "Update your task details";
    if (submitBtn) submitBtn.textContent = "Update Task";

    // Add Laravel method override for PATCH
    let methodField = form.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement("input");
        methodField.type = "hidden";
        methodField.name = "_method";
        form.appendChild(methodField);
    }
    methodField.value = "PATCH";
}
