<script setup>
defineProps({
  size: {
    type: Number,
    default: 24,
  },
})
</script>

<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
  >
    <defs>
  <linearGradient id="blueGradient" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#6366F1" />
    <stop offset="50%" stop-color="#818CF8" />
    <stop offset="100%" stop-color="#4F46E5" />
  </linearGradient>
</defs>

    <!-- Outer yellow circle with fade -->
    <circle
      cx="12"
      cy="12"
      r="10"
      fill="url(#blueGradient)"
    >
      <animate
        attributeName="opacity"
        values="1;0.3;1"
        dur="2s"
        repeatCount="indefinite"
      />
    </circle>

    <!-- Inner white pulsing circle with opposing fade -->
    <circle
      cx="12"
      cy="12"
      r="5"
      fill="white"
    >
      <animate
        attributeName="opacity"
        values="0.4;1;0.4"
        dur="2s"
        repeatCount="indefinite"
      />
    </circle>
  </svg>
</template>
