import { closeFAB } from "./Fab.js";
import { $, $$ } from "../domHelpers.js";
import {
    animateModalBorderOpen,
    animateModalBorderClose,
} from "../animatedColoredBorder.js";

let openModals = [];
let previousFocus;
let scrollY = 0;

function lockScroll() {
    scrollY = window.scrollY;
    document.body.style.position = "fixed";
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = "100%";
    document.body.classList.add("modal-open");
}

function unlockScroll() {
    document.body.style.position = "";
    document.body.style.top = "";
    document.body.style.width = "";
    document.body.classList.remove("modal-open");
    window.scrollTo(0, scrollY);
}

function trapFocus(modal) {
    const focusable = $$(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
        modal
    );
    if (!focusable.length) return;
    const first = focusable[0],
        last = focusable[focusable.length - 1];
    first.focus();
    modal.addEventListener("keydown", (e) => {
        if (e.key !== "Tab") return;
        if (e.shiftKey && document.activeElement === first) {
            last.focus();
            e.preventDefault();
        } else if (!e.shiftKey && document.activeElement === last) {
            first.focus();
            e.preventDefault();
        }
    });
}

export function openModal(id) {
    const modal = $(`#${id}`);
    if (!modal) return;
    previousFocus = document.activeElement;

    if (!openModals.includes(id)) openModals.push(id);

    closeFAB();
    animateModalBorderOpen(modal);

    modal.classList.remove("hidden");
    modal.classList.remove("pointer-events-none");
    modal.classList.add("opacity-0");

    if (openModals.length === 1) {
        $$("body > *").forEach((el) => {
            if (!el.contains(modal)) el.setAttribute("aria-hidden", "true");
        });
        lockScroll();
    }

    requestAnimationFrame(() => {
        modal.classList.replace("opacity-0", "opacity-100");
        trapFocus(modal);
    });
}

export function closeModal(id) {
    const modal = $(`#${id}`);
    if (!modal) return;
    openModals = openModals.filter((m) => m !== id);

    animateModalBorderClose(modal);
    modal.classList.replace("opacity-100", "opacity-0");
    modal.classList.add("pointer-events-none");
    modal.classList.add("hidden");

    if (!openModals.length) {
        $$("body > *").forEach((el) => el.removeAttribute("aria-hidden"));
        unlockScroll();
    }

    if (previousFocus && document.body.contains(previousFocus))
        previousFocus.focus();
    else {
        const fabBtn = $("[data-fab-main]");
        if (fabBtn) fabBtn.focus();
    }

    previousFocus = null;
}

export function initModals() {
    $$("[data-modal-open]").forEach((btn) =>
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            openModal(btn.dataset.modalOpen);
        })
    );

    $$("[data-modal-close]").forEach((btn) =>
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            closeModal(btn.dataset.modalClose);
        })
    );

    $$("[data-modal-backdrop]").forEach((backdrop) =>
        backdrop.addEventListener("click", () => {
            const modal = backdrop.closest(".modal");
            if (modal) closeModal(modal.id);
        })
    );

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && openModals.length)
            closeModal(openModals[openModals.length - 1]);
    });
}
