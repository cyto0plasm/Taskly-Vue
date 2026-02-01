// Centralized state management
const menuState = {
    profile: false,
    mobileNav: false,
    mobileSearch: false,
};
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        closeAllMenus();
    }
});
// Function to close all menus
function closeAllMenus() {
    // Close profile dropdown
    if (menuState.profile) {
        document
            .getElementById("ProfileMenu")
            .classList.add("opacity-0", "invisible");

        menuState.profile = false;
    }

    // Close mobile nav
    if (menuState.mobileNav) {
        document.getElementById("mobileMenu").classList.add("hidden");
        menuState.mobileNav = false;
    }

    // Close mobile search
    if (menuState.mobileSearch) {
        document
            .getElementById("mobileSearchContainer")
            .classList.add("hidden");
        document
            .getElementById("mobileSearchContainer")
            .classList.remove("flex");
        document.getElementById("searchOverlay").classList.add("hidden");
        menuState.mobileSearch = false;
    }
}

// Profile Dropdown
const profileDropDown = document.getElementById("profileDropdown");
const ProfileMenu = document.getElementById("ProfileMenu");
const arrow = document.getElementById("arrow");

profileDropDown.addEventListener("click", (e) => {
    e.stopPropagation();

    // If profile is already open, just close it
    if (menuState.profile) {
        closeAllMenus();
        arrow.style.transform = "rotate(0deg)";
    } else {
        // Close other menus first, then open profile
        closeAllMenus();
        ProfileMenu.classList.remove("opacity-0", "invisible");
        arrow.style.transform = "rotate(180deg)";
        menuState.profile = true;
    }
});
ProfileMenu.addEventListener("click", (e) => {
    e.stopPropagation();
});

// Mobile Nav
const mobileNavBtn = document.getElementById("mobileNavBtn");
const navBtns = document.getElementById("mobileMenu");

mobileNavBtn.addEventListener("click", (e) => {
    e.stopPropagation();

    // If nav is already open, just close it
    if (menuState.mobileNav) {
        closeAllMenus();
    } else {
        // Close other menus first, then open nav
        closeAllMenus();
        navBtns.classList.remove("hidden");
        menuState.mobileNav = true;
    }
});
//desktop search
document.addEventListener("DOMContentLoaded", () => {
    const desktopSearchBtn = document.getElementById("desktopSearchBtn");
    const desktopSearchInput = document.getElementById("desktopSearchInput");

    desktopSearchBtn.addEventListener("click", (e) => {
        e.stopPropagation();

        // Open mobile search if not already open
        if (menuState.mobileSearch) {
            closeMobileSearch();
        } else {
            closeAllMenus();
            openMobileSearch();
        }

        // Focus input inside the wrapper
        desktopSearchInput.focus();
    });
});

// Mobile Search Toggle with Overlay
const mobileSearchBtn = document.getElementById("mobileSearchBtn");
const mobileSearchContainer = document.getElementById("mobileSearchContainer");
const searchOverlay = document.querySelector(".searchOverlay");

mobileSearchBtn.addEventListener("click", (e) => {
    e.stopPropagation();

    if (menuState.mobileSearch) {
        closeMobileSearch();
    } else {
        closeAllMenus();
        openMobileSearch();
    }
});

searchOverlay.addEventListener("click", () => {
    closeMobileSearch();
});

function openMobileSearch() {
    mobileSearchContainer.classList.remove("hidden");
    mobileSearchContainer.classList.add("flex");
    searchOverlay.classList.remove("hidden");

    setTimeout(() => {
        mobileSearchContainer.classList.add("search-open");
        searchOverlay.classList.add("overlay-open");
        mobileSearchInput.focus();
    }, 10);

    menuState.mobileSearch = true;
}

function closeMobileSearch() {
    mobileSearchContainer.classList.remove("search-open");
    searchOverlay.classList.remove("overlay-open");

    setTimeout(() => {
        mobileSearchContainer.classList.add("hidden");
        mobileSearchContainer.classList.remove("flex");
        searchOverlay.classList.add("hidden");
        mobileSearchInput.value = "";
        clearSearchBtn.classList.add("hidden");
    }, 300);

    menuState.mobileSearch = false;
}

// Show/hide clear button
mobileSearchInput.addEventListener("input", (e) => {
    if (e.target.value.length > 0) {
        clearSearchBtn.classList.remove("hidden");
    } else {
        clearSearchBtn.classList.add("hidden");
    }
});

// Clear button click
clearSearchBtn.addEventListener("click", () => {
    mobileSearchInput.value = "";
    clearSearchBtn.classList.add("hidden");
    mobileSearchInput.focus();
});

// Close search when clicking overlay
searchOverlay.addEventListener("click", () => {
    closeAllMenus();
});

// Click outside to close any open menu
document.addEventListener("click", (e) => {
    const clickedOutside =
        !profileDropDown.contains(e.target) &&
        !ProfileMenu.contains(e.target) &&
        !mobileNavBtn.contains(e.target) &&
        !navBtns.contains(e.target) &&
        !mobileSearchBtn.contains(e.target) &&
        !mobileSearchContainer.contains(e.target) &&
        !searchOverlay.contains(e.target);

    if (clickedOutside) {
        closeAllMenus();
    }
});
// Mobile Dropdowns within the Mobile Nav links
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
