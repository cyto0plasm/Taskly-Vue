// resources/js/modal/modalCore.js
import { $, $$ } from "../domHelpers.js";

let openModalStack = [];
let previousFocus = null;
let scrollY = 0;

// ----------------------------
// Scroll lock
// ----------------------------
export function lockScroll() {
    scrollY = window.scrollY;
    document.body.style.position = "fixed";
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = "100%";
    document.body.classList.add("modal-open");
}

export function unlockScroll() {
    document.body.classList.remove("modal-open");
    document.body.style.position = "";
    document.body.style.top = "";
    document.body.style.width = "";
    window.scrollTo(0, scrollY);
}

// ----------------------------
// Open modal
// ----------------------------
export function openModal(modalId) {
    const modal = $(`#${modalId}`);
    if (!modal) return;

    previousFocus = document.activeElement;
    if (!openModalStack.includes(modalId)) openModalStack.push(modalId);

    modal.classList.remove("hidden", "pointer-events-none");
    modal.classList.add("opacity-0");

    requestAnimationFrame(() => {
        modal.classList.replace("opacity-0", "opacity-100");
        trapFocus(modal);
    });
}

// ----------------------------
// Close modal
// ----------------------------
export function closeModal(modalId) {
    const modal = $(`#${modalId}`);
    if (!modal) return;

    openModalStack = openModalStack.filter((id) => id !== modalId);

    if (openModalStack.length === 0) unlockScroll();

    modal.classList.replace("opacity-100", "opacity-0");
    modal.classList.add("pointer-events-none");

    const handler = (e) => {
        if (e.target !== modal) return;
        modal.classList.add("hidden");
        modal.classList.remove("pointer-events-none");

        if (previousFocus && document.body.contains(previousFocus)) {
            previousFocus.focus();
        }
        previousFocus = null;

        modal.removeEventListener("transitionend", handler);
    };

    modal.addEventListener("transitionend", handler);
}

// ----------------------------
// Focus trap
// ----------------------------
export function trapFocus(modal) {
    const focusable = $$(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
        modal
    );
    if (!focusable.length) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    first.focus();

    const handleTab = (e) => {
        if (e.key !== "Tab") return;
        if (e.shiftKey && document.activeElement === first) {
            last.focus();
            e.preventDefault();
        } else if (!e.shiftKey && document.activeElement === last) {
            first.focus();
            e.preventDefault();
        }
    };

    modal.addEventListener("keydown", handleTab);
}
