<script setup>
defineProps({
  size: { type: Number, default: 24 },
  color: { type: String, default: 'currentColor' },
})
</script>

<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="size"
    :height="size"
    viewBox="0 0 20 20"
    fill="currentColor"
    :class="color !== 'currentColor' && !color.startsWith('#') && !color.startsWith('rgb') ? `text-${color}` : ''"
    :style="color.startsWith('#') || color.startsWith('rgb') ? { color } : {}"
  >
    <path
      fill-rule="evenodd"
      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
      clip-rule="evenodd"
    />
  </svg>
</template>
