<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
    <defs>
        <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#10B981" />
            <stop offset="50%" stop-color="#22C55E" />
            <stop offset="100%" stop-color="#4ADE80" />
        </linearGradient>
    </defs>
    <path d="M20 6L9 17L4.5 12.5" fill="none" stroke="rgba(0,0,0,0.1)" stroke-width="4.2" stroke-linecap="round"
        stroke-linejoin="round" transform="translate(0.5,0.5)" />
    <path d="M4.5 12.5L9 17L20 6" fill="none" stroke="url(#g)" stroke-width="3.2" stroke-linecap="round"
        stroke-linejoin="round" stroke-dasharray="30" stroke-dashoffset="30">
        <animate attributeName="stroke-dashoffset" values="30;30;0;0" keyTimes="0;0.1;0.2;1" dur="5s"
            repeatCount="indefinite" />
        <animate attributeName="opacity" values="0;0;1;1;0" keyTimes="0;0.1;0.2;0.9;1" dur="5s"
            repeatCount="indefinite" />
    </path>
</svg>
