<script setup>
import { computed } from 'vue'
import { useFlash } from './useFlash.js'

const { flash } = useFlash()

// Map flash types to Tailwind classes
const bgColor = computed(() => ({
  success: 'bg-green-500',
  error: 'bg-red-500',
  info: 'bg-blue-500'
}[flash.type] || 'bg-gray-500'))

const textColor = computed(() => ({
  success: 'text-white',
  error: 'text-white',
  info: 'text-white'
}[flash.type] || 'text-white'))

// Optional: simple icon per type (replace later with SVG if desired)
const icon = computed(() => ({
  success: '✓',
  error: '⚠',
  info: 'ℹ'
}[flash.type] || ''))

// Close flash manually
function closeFlash() {
  flash.visible = false
}
</script>

<template>
  <transition
    name="fade"
    enter-active-class="transition-all duration-300 ease-out"
    leave-active-class="transition-all duration-300 ease-in"
    enter-from-class="opacity-0 translate-y-[-10px]"
    enter-to-class="opacity-100 translate-y-0"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-[-10px]"
  >
    <div
      v-if="flash.visible"
      :class="[bgColor, textColor, 'fixed top-16 left-1/2 -translate-x-1/2 px-4 py-2 rounded-lg shadow-lg z-[1000] flex items-center gap-3 min-w-[200px] max-w-sm']"
    >
      <!-- Icon -->
      <span v-if="icon" class="font-bold text-lg">{{ icon }}</span>

      <!-- Message -->
      <span class="flex-1 truncate">{{ flash.message }}</span>

      <!-- Count -->
      <span v-if="flash.count > 1" class="ml-2 font-semibold">({{ flash.count }})</span>

      <!-- Close Button -->
      <button @click="closeFlash" class="ml-2 text-white/70 hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
