<script setup>
import { computed } from 'vue'
import { useFlash } from './useFlash.js'

const { flash } = useFlash()

const bgColor = computed(() => ({
  success: 'bg-green-500',
  error: 'bg-red-500',
  info: 'bg-blue-500'
}[flash.type] || 'bg-gray-500'))
</script>

<template>
  <transition name="fade">
    <div
      v-if="flash.visible"
      :class="[bgColor, 'fixed top-16 left-1/2 -translate-x-1/2 px-4 py-2 rounded shadow-md text-white z-50']"
    >
      {{ flash.message }}
      <span v-if="flash.count > 1" class="ml-2 font-bold">({{ flash.count }})</span>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
