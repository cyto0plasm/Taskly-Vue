import { $ } from "../domHelpers.js";

let isInitialized = false;

export function initFAB() {
    if (isInitialized) return;
    isInitialized = true;

    const btn = $("[data-fab-main]");
    const menu = $("#fab-menu");

    if (!btn || !menu) return;

    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleFAB();
    });

    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            closeFAB();
        }
    });
}

// ----------------------------
// FAB functions
// ----------------------------
export function closeFAB() {
    const menu = $("#fab-menu");
    if (menu) menu.classList.add("scale-y-0", "opacity-0");
}

export function openFAB() {
    const menu = $("#fab-menu");
    if (menu) menu.classList.remove("scale-y-0", "opacity-0");
}

export function toggleFAB() {
    const menu = $("#fab-menu");
    if (!menu) return;
    const isOpen = !menu.classList.contains("scale-y-0");
    isOpen ? closeFAB() : openFAB();
}
