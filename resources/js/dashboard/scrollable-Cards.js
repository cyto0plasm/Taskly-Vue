document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("total_cards");
    const leftBtn = document.getElementById("scrollLeft");
    const rightBtn = document.getElementById("scrollRight");

    if (!container || !leftBtn || !rightBtn) {
        console.warn("Scroll controls missing");
        return;
    }

    const SCROLL_DURATION = 400; // milliseconds
    const DISABLE_CLASSES = ["opacity-40", "pointer-events-none"];
    let isScrolling = false;
    let animationFrameId;

    // -----------------------------
    // Helper: Get total width of one card including gap
    // -----------------------------
    function getCardWidth() {
        const firstCard = container.querySelector(":scope > *");
        if (!firstCard) return 0;

        const gap = parseFloat(getComputedStyle(container).gap) || 0;
        return firstCard.offsetWidth + gap;
    }

    // -----------------------------
    // Smoothly scroll container by a distance
    // -----------------------------
    function smoothScroll(distance) {
        if (isScrolling) cancelAnimationFrame(animationFrameId);
        isScrolling = true;

        const startScroll = container.scrollLeft;
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / SCROLL_DURATION, 1);
            const ease = 0.5 - Math.cos(progress * Math.PI) / 2; // easeInOut
            container.scrollLeft = startScroll + distance * ease;

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(step);
            } else {
                isScrolling = false;
                updateScrollButtons();
            }
        }

        animationFrameId = requestAnimationFrame(step);
    }

    // -----------------------------
    // Enable/disable scroll buttons at edges
    // -----------------------------
    function updateScrollButtons() {
        const atStart = container.scrollLeft <= 0;
        const atEnd =
            container.scrollLeft >=
            container.scrollWidth - container.clientWidth - 1;

        DISABLE_CLASSES.forEach((cls) => {
            leftBtn.classList.toggle(cls, atStart);
            rightBtn.classList.toggle(cls, atEnd);
        });
    }

    // -----------------------------
    // Event handlers
    // -----------------------------
    leftBtn.addEventListener("click", () => smoothScroll(-getCardWidth()));
    rightBtn.addEventListener("click", () => smoothScroll(getCardWidth()));
    container.addEventListener("scroll", updateScrollButtons);
    window.addEventListener("resize", updateScrollButtons);

    // -----------------------------
    // Initialize buttons on load
    // -----------------------------
    updateScrollButtons();
});
