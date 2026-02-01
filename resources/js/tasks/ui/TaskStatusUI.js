// Status icon HTML
export function statusIcon(status) {
    if (status === "done") {
        return `<div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>`;
    } else if (status === "in_progress") {
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
    <defs>
        <linearGradient id="yellowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#F59E0B" />
            <stop offset="50%" stop-color="#EAB308" />
            <stop offset="100%" stop-color="#FBBF24" />
        </linearGradient>
    </defs>
    <!-- Outer yellow circle with fade -->
    <circle cx="12" cy="12" r="10" fill="url(#yellowGradient)">
        <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite" />
    </circle>
    <!-- Inner white pulsing circle with opposing fade -->
    <circle cx="12" cy="12" r="5" fill="white">
        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite" />
    </circle>
</svg>
`; // placeholder for <x-svg.progress>
    } else {
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
    <defs>
        <linearGradient id="redGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#DC2626" />
            <stop offset="50%" stop-color="#EF4444" />
            <stop offset="100%" stop-color="#F87171" />
        </linearGradient>
    </defs>
    <!-- Shadow circle -->
    <circle cx="12" cy="12" r="9" fill="none" stroke="rgba(0,0,0,0.1)" stroke-width="2.5"
        transform="translate(0.3,0.3)" />

    <!-- Main red circle with pulsing fade -->
    <circle cx="12" cy="12" r="9" fill="none" stroke="url(#redGradient)" stroke-width="2.5">
        <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite" />
    </circle>

    <!-- Inner red fill with opposing fade -->
    <circle cx="12" cy="12" r="6" fill="url(#redGradient)">
        <animate attributeName="opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite" />
    </circle>
</svg>
`; // placeholder for <x-svg.pending>
    }
}

// Status label text color
export function statusColor(status) {
    return status === "done"
        ? "text-green-600"
        : status === "in_progress"
        ? "text-yellow-600"
        : "text-red-600";
}

// Status label text
export function statusLabel(status) {
    return status === "in_progress"
        ? "In Progress"
        : status.charAt(0).toUpperCase() + status.slice(1);
}
