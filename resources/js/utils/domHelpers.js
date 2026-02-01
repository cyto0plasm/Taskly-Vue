// utils/domHelpers.js

/**
 * Query selector shorthand
 */
export const $ = (selector) => document.querySelector(selector);
export const $$ = (selector) => document.querySelectorAll(selector);

/**
 * Set text content safely
 */
export const setText = (selector, text) => {
    const el = $(selector);
    if (el) el.textContent = text ?? "-";
};

/**
 * Toggle multiple elements' visibility
 */
export const toggleElements = (elementsMap) => {
    Object.entries(elementsMap).forEach(([selector, shouldShow]) => {
        const el = $(selector);
        if (el) el.classList.toggle("hidden", !shouldShow);
    });
};

/**
 * Remove active class from all, add to target
 */
export const setActiveItem = (
    containerSelector,
    itemSelector,
    targetElement
) => {
    $$(itemSelector).forEach((el) => el.classList.remove("active"));
    targetElement?.classList.add("active");
};

/**
 * Create element with properties and children
 */
export const create = (tag, props = {}) => {
    const el = document.createElement(tag);
    const { children, ...attributes } = props;

    // Set attributes
    Object.entries(attributes).forEach(([key, value]) => {
        if (key.startsWith("on") && typeof value === "function") {
            // Event listeners
            el.addEventListener(key.substring(2).toLowerCase(), value);
        } else if (key === "className") {
            el.className = value;
        } else if (key === "textContent" || key === "innerHTML") {
            el[key] = value;
        } else {
            el.setAttribute(key, value);
        }
    });

    // Append children
    if (children) {
        const childArray = Array.isArray(children) ? children : [children];
        childArray.forEach((child) => {
            if (child instanceof Node) {
                el.appendChild(child);
            } else if (child) {
                el.appendChild(document.createTextNode(String(child)));
            }
        });
    }

    return el;
};

/**
 * Add event listener shorthand
 */
export const on = (target, event, handler, options) => {
    target.addEventListener(event, handler, options);
    return () => target.removeEventListener(event, handler, options);
};
