<script setup>
defineProps({
  size: {
    type: Number,
    default: 24,
  },
  color: {
    type: String,
    default: '',
  },
})
</script>

<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    :class="color"
  >
    <defs>
      <linearGradient id="redGradient" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#DC2626" />
        <stop offset="50%" stop-color="#EF4444" />
        <stop offset="100%" stop-color="#F87171" />
      </linearGradient>
    </defs>

    <!-- Shadow circle -->
    <circle
      cx="12"
      cy="12"
      r="10"
      stroke="rgba(0,0,0,0.1)"
      stroke-width="2.5"
    />

    <!-- Main red circle with pulsing fade -->
    <circle
      cx="12"
      cy="12"
      r="10"
      stroke="url(#redGradient)"
      stroke-width="2.5"
      fill="none"
    >
      <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite" />
    </circle>

    <!-- Inner red fill with opposing fade -->
    <circle
      cx="12"
      cy="12"
      r="6"
      fill="url(#redGradient)"
    >
      <animate attributeName="opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite" />
    </circle>
  </svg>
</template>
