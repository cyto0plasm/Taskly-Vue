export const statusMap = {
    done: {
        label: "Done",
        badgeClasses: "bg-green-100 text-green-800",
        iconClasses: "bg-green-500",
        icon: (size = 3) =>
            `<svg class="w-${size} h-${size} text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>`,
    },
    in_progress: {
        label: "In Progress",
        badgeClasses: "bg-yellow-100 text-yellow-800",
        iconClasses: "bg-yellow-500",
        icon: (size = 2.5) =>
            `<div class="w-${size} h-${size} bg-white rounded-full animate-pulse"></div>`,
    },
    pending: {
        label: "Pending",
        badgeClasses: "bg-red-100 text-red-800",
        iconClasses: "bg-red-500",
        icon: (size = 2.5) =>
            `<div class="w-${size} h-${size} bg-white rounded-full"></div>`,
    },
};

export function getStatusConfig(status) {
    return statusMap[status] || statusMap.pending;
}
