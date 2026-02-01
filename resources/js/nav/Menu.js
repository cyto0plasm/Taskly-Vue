export class Menu {
    // - `name` → identifier (e.g., "profileMenu")
    // - `trigger` → the button that opens/closes the menu
    // - `panel` → the dropdown panel to show/hide
    // - `openClass` → array of CSS classes to apply when opening
    // - `closeClass` → array of CSS classes to apply when closing
    // - `onOpen` → optional function to run after opening
    // - `onClose` → optional function to run after closing

    constructor({
        name,
        trigger,
        panel,
        openClass = [],
        closeClass = [],
        onOpen = null,
        onClose = null,
    }) {
        this.name = name;
        this.trigger = trigger;
        this.panel = panel;
        this.openClass = openClass;
        this.closeClass = closeClass;
        this.onOpen = onOpen;
        this.onClose = onClose;

        this.isOpen = false;
        this.init();
    }

    init() {
        this.trigger.addEventListener("click", (e) => {
            e.stopPropagation();
            this.toggle();
        });
        this.panel?.addEventListener("click", (e) => e.stopPropagation());
    }
    open() {
        MenuManager.closeAllExcept(this);

        this.panel?.classList.remove(...this.closeClass);
        this.panel?.classList.add(...this.openClass);
        this.isOpen = true;
        MenuManager.registerOpenMenu(this);
        this.onOpen?.();
    }
    close() {
        this.panel?.classList.add(...this.closeClass);
        this.panel?.classList.remove(...this.openClass);
        this.isOpen = false;
        this.onClose?.();
    }
    toggle() {
        this.isOpen ? this.close() : this.open();
    }
}
export class MenuManager {
    static openMenus = new Set();

    static registerOpenMenu(menu) {
        this.openMenus.add(menu);
    }

    static closeAll() {
        [...this.openMenus].forEach((menu) => menu.close());
        this.openMenus.clear();
    }
    static closeAllExcept(menuToKeep) {
        [...this.openMenus].forEach((menu) => {
            if (menu !== menuToKeep) menu.close();
        });
        this.openMenus = new Set(
            [...this.openMenus].filter((menu) => menu === menuToKeep)
        );
    }
}
// Close everything on click outside
document.addEventListener("click", () => MenuManager.closeAll());

// Escape key closes everything
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") MenuManager.closeAll();
});
