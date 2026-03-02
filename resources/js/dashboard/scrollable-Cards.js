document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("total_cards");
    const leftBtn   = document.getElementById("scrollLeft");
    const rightBtn  = document.getElementById("scrollRight");

    if (!container || !leftBtn || !rightBtn) return;

    function cardStep() {
        const first = container.querySelector(":scope > *");
        if (!first) return 266; // 250px card + 16px gap fallback
        const gap = parseFloat(getComputedStyle(container).columnGap) || 16;
        return first.offsetWidth + gap;
    }

    function sync() {
        const max     = container.scrollWidth - container.clientWidth;
        const atStart = container.scrollLeft <= 1;
        const atEnd   = container.scrollLeft >= max - 1;

        // Left: disabled at start
        leftBtn.classList.toggle("opacity-40",        atStart);
        leftBtn.classList.toggle("pointer-events-none", atStart);

        // Right: disabled at end (or nothing to scroll)
        rightBtn.classList.toggle("opacity-40",        max <= 1 || atEnd);
        rightBtn.classList.toggle("pointer-events-none", max <= 1 || atEnd);
    }

    leftBtn.addEventListener("click",  () => container.scrollBy({ left: -cardStep(), behavior: "smooth" }));
    rightBtn.addEventListener("click", () => container.scrollBy({ left:  cardStep(), behavior: "smooth" }));

    container.addEventListener("scroll",    sync);
    container.addEventListener("scrollend", sync);

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(sync, 100);
    });

    // Run sync after full paint (fonts, images, SPA hydration)
    window.addEventListener("load", () => {
        sync();
        setTimeout(sync, 300);
    });
});
