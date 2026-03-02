<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'tasks',
    },
});
</script>
<template>
<!-- Home Icon -->
<svg v-if="props.type === 'home'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none">
  <!-- Roof -->
  <polyline points="12,3 22,11 2,11 12,3" stroke="#1e293b" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/>
  <!-- Chimney -->
  <rect x="15" y="4.5" width="2.5" height="5" rx="0.8" fill="#1e293b"/>
  <!-- Walls -->
  <rect x="4" y="11" width="16" height="10" rx="1.5" stroke="#1e293b" stroke-width="1.8"/>
  <!-- Door -->
  <rect x="9.5" y="15" width="5" height="6" rx="1" fill="#1e293b"/>
  <!-- Window -->
  <rect x="5" y="13" width="4" height="3.5" rx="1" stroke="#1e293b" stroke-width="1.5"/>
</svg>

<svg v-else-if="props.type === 'tasks'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none">
  <!-- Clipboard body -->
  <rect x="4" y="5" width="16" height="17" rx="2.5" stroke="#16a34a" stroke-width="1.8"/>
  <!-- Clip tab -->
  <rect x="9" y="3" width="6" height="4" rx="1.5" fill="#16a34a"/>
  <!-- Checkmark -->
  <polyline points="8,13 10.5,15.5 14,11" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
  <!-- Lines -->
  <line x1="15.5" y1="12" x2="18" y2="12" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round"/>
  <line x1="15.5" y1="14.5" x2="18" y2="14.5" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round"/>
  <line x1="8" y1="17.5" x2="16" y2="17.5" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>
</svg>

<svg v-else-if="props.type === 'projects'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none">
  <!-- Folder -->
  <path d="M2,7 Q2,5 4,5 L9,5 L11,3 Q11.5,2 13,2 L20,2 Q22,2 22,4 L22,17 Q22,19 20,19 L4,19 Q2,19 2,17 Z" fill="#6366f1" opacity="0.15"/>
  <path d="M2,7 Q2,5 4,5 L9,5 L11,3 Q11.5,2 13,2 L20,2 Q22,2 22,4 L22,17 Q22,19 20,19 L4,19 Q2,19 2,17 Z" stroke="#4f46e5" stroke-width="1.8" stroke-linejoin="round"/>
  <!-- Bar chart -->
  <rect x="6" y="11" width="3" height="5" rx="1" fill="#4f46e5"/>
  <rect x="10.5" y="9" width="3" height="7" rx="1" fill="#4f46e5"/>
  <rect x="15" y="12" width="3" height="4" rx="1" fill="#4f46e5"/>
</svg>
</template>
