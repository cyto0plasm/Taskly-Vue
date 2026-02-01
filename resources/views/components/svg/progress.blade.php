<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
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
