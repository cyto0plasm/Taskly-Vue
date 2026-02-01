/* =====================================================
   Unified Tooltip – drop-in, reusable, global
   Usage:
   <div data-tooltip="Hello" data-position="top">Hover</div>
   Positions: top | bottom | left | right
===================================================== */

(() => {
    // Inject styles once
    if (!document.getElementById("unified-tooltip-style")) {
        const style = document.createElement("style");
        style.id = "unified-tooltip-style";
        style.textContent = `
      .u-tooltip {
        position: fixed;
        background: #111;
        color: #fff;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 6px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.15s ease, transform 0.15s ease;
        z-index: 99999;
      }
      .u-tooltip.show {
        opacity: 1;
        transform: scale(1);
      }
    `;
        document.head.appendChild(style);
    }

    // Create tooltip once
    const tooltip = document.createElement("div");
    tooltip.className = "u-tooltip";
    document.body.appendChild(tooltip);

    let activeEl = null;

    function showTooltip(el) {
        const text = el.getAttribute("data-tooltip");
        if (!text) return;

        activeEl = el;
        tooltip.textContent = text;
        tooltip.classList.add("show");

        positionTooltip();
    }

    function hideTooltip() {
        tooltip.classList.remove("show");
        activeEl = null;
    }

    function positionTooltip() {
        if (!activeEl) return;

        const rect = activeEl.getBoundingClientRect();
        const pos = activeEl.getAttribute("data-position") || "top";

        let x, y;

        switch (pos) {
            case "bottom":
                x = rect.left + rect.width / 2;
                y = rect.bottom + 8;
                tooltip.style.transform = "translate(-50%, 0)";
                break;

            case "left":
                x = rect.left - 8;
                y = rect.top + rect.height / 2;
                tooltip.style.transform = "translate(-100%, -50%)";
                break;

            case "right":
                x = rect.right + 8;
                y = rect.top + rect.height / 2;
                tooltip.style.transform = "translate(0, -50%)";
                break;

            default: // top
                x = rect.left + rect.width / 2;
                y = rect.top - 8;
                tooltip.style.transform = "translate(-50%, -100%)";
        }

        tooltip.style.left = `${x}px`;
        tooltip.style.top = `${y}px`;
    }

    // Events (delegated – works for dynamic elements)
    document.addEventListener("mouseover", (e) => {
        const el = e.target.closest("[data-tooltip]");
        if (!el) return;
        showTooltip(el);
    });

    document.addEventListener("mouseout", (e) => {
        if (!e.target.closest("[data-tooltip]")) return;
        hideTooltip();
    });

    window.addEventListener("scroll", positionTooltip);
    window.addEventListener("resize", positionTooltip);
})();
