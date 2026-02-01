import { getStatusConfig } from "./ui/statusConfig.js";
import TaskState from "../domain/tasks/TaskState.js";
import { $, $$, setText } from "../utils/domHelpers.js";
import { openModal, closeModal } from "../utils/modal/modal.js";

// -------------------------
// Reactive Task Detail Panel
// -------------------------
function populateTaskDetailPanel(task) {
    if (!task) return;

    setText(".task-title", task.title || "Untitled");
    setText(".task-description", task.description || "No description");
    setText(".task-priority", task.priority ?? "-");
    setText(".task-type", task.type ?? "-");
    setText(".project", task.project_name || "No project");

    if (task.due_date)
        setText(
            ".task-time",
            new Date(task.due_date).toISOString().split("T")[0]
        );
    if (task.created_at)
        setText(".task-Created-at", new Date(task.created_at).toLocaleString());
    if (task.updated_at)
        setText(".task-updated-at", new Date(task.updated_at).toLocaleString());

    const config = getStatusConfig(task.status);
    const badge = $(".task-state");
    const iconEl = badge?.previousElementSibling;

    if (badge) {
        badge.className = [
            "task-state px-3 py-1 text-sm font-medium rounded-full",
            config.badgeClasses,
        ].join(" ");
        badge.textContent = config.label;

        if (iconEl) {
            iconEl.className = [
                "w-6 h-6 rounded-full flex items-center justify-center",
                config.iconClasses,
            ].join(" ");
            iconEl.innerHTML = config.icon();
        }
    }
}

// -------------------------
// Populate Task Modal Helper
// -------------------------
function populateTaskModal(task, projects = []) {
    const form = $("#task-modal-form");
    const title = $("#task-title");
    const description = $("#task-description");
    const dueDate = $("#task-due-date");
    const priority = $("#task-priority");
    const status = $("#task-status");
    const modalTitle = $("#task-modal-title");
    const modalDescription = $("#task-modal-description");
    const submitBtn = form?.querySelector('[type="submit"]');

    if (!form) return;

    if (task) {
        // EDIT MODE
        form.action = `/tasks/update/${task.id}`;

        // Add PATCH method
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement("input");
            methodInput.type = "hidden";
            methodInput.name = "_method";
            form.appendChild(methodInput);
        }
        methodInput.value = "PATCH";

        // Fill form fields
        if (title) title.value = task.title || "";
        if (description) description.value = task.description || "";
        if (dueDate)
            dueDate.value = task.due_date
                ? new Date(task.due_date).toISOString().split("T")[0]
                : "";
        if (priority) priority.value = task.priority || "medium";
        if (status) status.value = task.status || "pending";

        // Update UI text
        if (modalTitle) modalTitle.textContent = "Edit Task";
        if (modalDescription)
            modalDescription.textContent = "Update your task details";
        if (submitBtn) submitBtn.textContent = "Update Task";

        // Select project if exists
        if (task.project_id) {
            const projectRadio = form.querySelector(
                `input[name="project_id"][value="${task.project_id}"]`
            );
            if (projectRadio) projectRadio.checked = true;
        }
    } else {
        // CREATE MODE (reset form)
        form.action = "/tasks/store";
        form.reset();

        // Remove PATCH method
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();

        // Update UI text
        if (modalTitle) modalTitle.textContent = "Create New Task";
        if (modalDescription)
            modalDescription.textContent = "Add a new task to your project";
        if (submitBtn) submitBtn.textContent = "Create Task";
    }
}
