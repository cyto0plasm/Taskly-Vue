document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("total_cards");
    const leftBtn = document.getElementById("scrollLeft");
    const rightBtn = document.getElementById("scrollRight");

    if (!container || !leftBtn || !rightBtn) {
        console.warn("Scroll controls missing");
        return;
    }

    const DISABLE_CLASSES = ["opacity-40", "pointer-events-none"];

    // -----------------------------
    // Get width of one card + its gap
    // -----------------------------
    function getCardWidth() {
        const firstCard = container.querySelector(":scope > *");
        if (!firstCard) return 300;

        // Use column-gap explicitly to avoid "16px 16px" parsing issues
        const gap =
            parseFloat(getComputedStyle(container).columnGap) || 0;
        return firstCard.offsetWidth + gap;
    }

    // -----------------------------
    // Enable/disable buttons at scroll edges
    // -----------------------------
    function updateScrollButtons() {
        const atStart = container.scrollLeft <= 1;
        const atEnd =
            container.scrollLeft >=
            container.scrollWidth - container.clientWidth - 1;

        DISABLE_CLASSES.forEach((cls) => {
            leftBtn.classList.toggle(cls, atStart);
            rightBtn.classList.toggle(cls, atEnd);
        });
    }

    // -----------------------------
    // Scroll by one card width using native smooth scroll
    // (works with CSS scroll-smooth, no rAF conflict)
    // -----------------------------
    leftBtn.addEventListener("click", () => {
        container.scrollBy({ left: -getCardWidth(), behavior: "smooth" });
    });

    rightBtn.addEventListener("click", () => {
        container.scrollBy({ left: getCardWidth(), behavior: "smooth" });
    });

    container.addEventListener("scroll", updateScrollButtons);
    window.addEventListener("resize", updateScrollButtons);

    // Init
    updateScrollButtons();
});
