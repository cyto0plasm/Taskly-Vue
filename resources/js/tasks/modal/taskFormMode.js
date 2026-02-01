export function setEditMode(form, taskId) {
    form.dataset.taskId = taskId;

    ensureHidden(form, "_method", "PATCH");
    ensureHidden(
        form,
        "_token",
        document.querySelector('meta[name="csrf-token"]')?.content
    );
}

export function resetCreateMode(form) {
    form.reset();
    delete form.dataset.taskId;
    removeHidden(form, "_method");
}

function ensureHidden(form, name, value) {
    if (!form.querySelector(`[name="${name}"]`) && value) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
}

function removeHidden(form, name) {
    form.querySelector(`[name="${name}"]`)?.remove();
}
