import { Menu } from "./Menu.js";

// Profile Menu
const profilePanel = document.getElementById("ProfileMenu");
const profileTrigger = document.getElementById("profileDropdown");
const arrow = document.getElementById("arrow");
if (profilePanel && profileTrigger && arrow) {
    new Menu({
        name: "profile",
        trigger: profileTrigger,
        panel: profilePanel,
        openClass: ["opacity-100", "visible"],
        closeClass: ["opacity-0", "invisible"],
        onOpen: () => (arrow.style.transform = "rotate(0deg)"),
        onClose: () => (arrow.style.transform = "rotate(180deg)"),
    });
}

// Mobile Nav Menu
const mobilePanel = document.getElementById("mobileMenu");
const mobileTrigger = document.getElementById("mobileNavBtn");
new Menu({
    name: "mobileNav",
    trigger: mobileTrigger,
    panel: mobilePanel,
    openClass: ["block"],
    closeClass: ["hidden"],
});

// Mobile Nav inner Dropdowns
function toggleMobileDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const icon = document.getElementById(dropdownId + "Icon");

    if (dropdown.classList.contains("hidden")) {
        dropdown.classList.remove("hidden");
        icon.style.transform = "rotate(180deg)";
    } else {
        dropdown.classList.add("hidden");
        icon.style.transform = "rotate(0deg)";
    }
}
window.toggleMobileDropdown = toggleMobileDropdown;

// Mobile Search
const mobileSearchTrigger = document.getElementById("mobileSearchBtn");
const mobileSearchPanel = document.getElementById("mobileSearchContainer");
const searchOverlay = document.querySelector(".searchOverlay");
const mobileSearchInput = document.getElementById("mobileSearchInput");

if (mobileSearchTrigger && mobileSearchPanel) {
    const mobileSearchMenu = new Menu({
        name: "mobileSearch",
        trigger: mobileSearchTrigger,
        panel: mobileSearchPanel,
        openClasses: ["flex"],
        closeClasses: ["hidden"],
        onOpen: () => {
            searchOverlay?.classList.remove("hidden");
            setTimeout(() => searchOverlay?.classList.add("overlay-open"), 10);
            mobileSearchInput?.focus();
            setTimeout(
                () => mobileSearchPanel.classList.add("search-open"),
                20
            );
        },
        onClose: () => {
            mobileSearchPanel.classList.remove("search-open");
            searchOverlay?.classList.remove("overlay-open");
            setTimeout(() => {
                mobileSearchPanel.classList.remove("flex");
                mobileSearchPanel.classList.add("hidden");
                mobileSearchInput.value = "";
                clearSearchBtn.classList.add("hidden");
            }, 300);
        },
    });
}
