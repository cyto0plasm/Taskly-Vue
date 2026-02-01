<script setup>
const props = defineProps({
  text: { type: String, default: 'Button' },
  type: { type: String, default: 'button' },
  bgColor: { type: String, default: 'bg-[#6b3eea]' },
  hoverColor: { type: String, default: 'hover:bg-[#6935f7]' },
  activeColor: { type: String, default: 'active:bg-[#6335e0]' },
  textColor: { type: String, default: 'text-white' },
  hoverText: { type: String, default: 'hover:text-gray-800' },
  activeText: { type: String, default: 'active:text-gray-800' },
});

import { useAttrs } from 'vue';
const attrs = useAttrs(); // automatically collects all extra attributes
</script>

<template>
  <button
    v-bind="attrs"
    :type="type"
    :class="[
      'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 active:scale-[1.01] shadow-md active:shadow-lg',
      bgColor,
      hoverColor,
      activeColor,
      textColor,
      hoverText,
      activeText
    ]"
  >
    {{ text }}
  </button>
</template>
