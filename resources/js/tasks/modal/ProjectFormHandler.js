import { closeModal } from "../../utils/modal/modal.js";

export function setupProjectCreateForm() {
    const form = document.querySelector("#project-modal form");
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();
        clearErrors(form);

        try {
            // TODO: Wire to ProjectController
            const response = await fetch(form.action, {
                method: "POST",
                body: new FormData(form),
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            if (!response.ok) {
                const data = await response.json();
                if (data.errors) throw data;
            }

            form.reset();
            closeModal("project-modal");
            window.location.reload(); // Remove when ProjectController exists
        } catch (err) {
            if (err?.errors) showErrors(form, err.errors);
        }
    };
}

function clearErrors(form) {
    form.querySelectorAll(".error-message").forEach((e) => e.remove());
}

function showErrors(form, errors) {
    clearErrors(form);
    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;

        const span = document.createElement("span");
        span.className = "error-message text-red-600 text-sm block mt-1";
        span.textContent = messages[0];
        input.after(span);
    });
}
