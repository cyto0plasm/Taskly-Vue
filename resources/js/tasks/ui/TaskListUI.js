import { statusIcon, statusColor, statusLabel } from "./TaskStatusUI.js";

let currentActiveEl = null; // tracks the currently selected <li>

export function renderTaskItem(task, selectedId, options = {}) {
    const li = document.createElement("li");
    li.dataset.id = task.id;
    li.dataset.taskId = task.id;
    li.dataset.taskUrl = task.url || "#";
    li.className =
        "group task-item block border-b dark:border-0 border-gray-100 cursor-move transition-all duration-300 ease-out";

    // Fade-in animation for new items
    if (options.isNew) {
        li.style.opacity = "0";
        li.style.transform = "translateX(-10px)";
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                li.style.opacity = "1";
                li.style.transform = "translateX(0)";
            });
        });
    }

    // Apply active class if selected
    if (task.id === selectedId) {
        li.classList.add("active");
        currentActiveEl = li;
    }

    li.innerHTML = `
        <div class="flex items-center gap-3 px-6 py-4 dark:bg-[#222321] dark:text-white dark:hover:bg-slate-800 w-full transition-colors duration-200">
            <div class="task-status-icon flex-shrink-0">${statusIcon(
                task.status
            )}</div>
            <div class="flex-1 min-w-0">
                <p class="text-lg truncate ${
                    task.status === "done"
                        ? "text-gray-600 dark:text-gray-100"
                        : "text-gray-800 dark:text-white font-medium"
                }">${task.title}</p>
                <p class="task-status-label text-sm font-medium ${statusColor(
                    task.status
                )}">${statusLabel(task.status)}</p>
            </div>
            ${
                task.id
                    ? `<button type="button" class="delete-task-btn z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-150 p-2 bg-transparent" data-task-id="${task.id}" aria-label="Delete task">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
                                <path d="M18 6L6 18M6 6L18 18" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>`
                    : ""
            }
        </div>
    `;

    return li;
}

export function renderTaskEmpty() {
    const li = document.createElement("li");
    li.id = "emptyTasksList";
    li.className = "p-6 text-center text-gray-500 flex flex-col items-center";
    li.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            class="w-12 h-12 text-gray-300 mb-3">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-sm sm:text-base">No tasks yet</p>
    `;
    return li;
}

export function renderTaskSkeleton(count = 5) {
    const fragment = document.createDocumentFragment();
    for (let i = 0; i < count; i++) {
        const li = document.createElement("li");
        li.className =
            "border-b border-gray-100 p-4 animate-pulse flex items-center gap-3";
        li.innerHTML = `
            <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
        `;
        fragment.appendChild(li);
    }
    return fragment;
}
