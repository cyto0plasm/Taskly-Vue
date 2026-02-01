// Animate border on OPEN
export function animateModalBorderOpen(modal) {
    const box = modal.querySelector(".gradient-border");
    if (!box) return;

    // Remove any existing animation classes
    box.classList.remove("animate-open", "animate-close");
    void box.offsetWidth; // Force reflow

    // Add opening animation
    box.classList.add("animate-open");

    // Remove class after animation completes
    box.addEventListener(
        "animationend",
        () => box.classList.remove("animate-open"),
        { once: true }
    );
}

// Animate border on CLOSE
export function animateModalBorderClose(modal) {
    const box = modal.querySelector(".gradient-border");
    if (!box) return;

    // Remove any existing animation classes
    box.classList.remove("animate-open", "animate-close");
    void box.offsetWidth; // Force reflow

    // Add closing animation
    box.classList.add("animate-close");

    // Remove class after animation completes
    box.addEventListener(
        "animationend",
        () => box.classList.remove("animate-close"),
        { once: true }
    );
}

// Or keep your existing function and add a direction parameter:
export function animateModalBorder(modal, direction = "open") {
    const box = modal.querySelector(".gradient-border");
    if (!box) return;

    const animationClass =
        direction === "open" ? "animate-open" : "animate-close";

    // Remove any existing animation classes
    box.classList.remove("animate-open", "animate-close");
    void box.offsetWidth; // Force reflow

    // Add the appropriate animation
    box.classList.add(animationClass);

    // Remove class after animation completes
    box.addEventListener(
        "animationend",
        () => box.classList.remove(animationClass),
        { once: true }
    );
}
