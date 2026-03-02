<script setup>
import { computed } from 'vue'
import { useFlash } from './useFlash.js'

const { flash } = useFlash()

// Border and icon colors based on message type
const borderColor = computed(() => ({
  success: 'border-l-green-500',
  error: 'border-l-red-500',
  info: 'border-l-blue-500'
}[flash.type] || 'border-l-gray-500'))

const iconColor = computed(() => ({
  success: 'text-green-500',
  error: 'text-red-500',
  info: 'text-blue-500'
}[flash.type] || 'text-gray-500'))

// Professional descriptive icons
const icon = computed(() => ({
  success: `
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  `,
  error: `
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
  `,
  info: `
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
    </svg>
  `
}[flash.type] || ''))

// Close flash manually
function closeFlash() {
  flash.visible = false
}
</script>

<template>
     <Teleport to="body">
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
      :class="[
        borderColor,
        'fixed top-16 left-1/2 -translate-x-1/2',
        'bg-gray-100 dark:bg-[#e1e0e0] border-l-4',
        'px-4 py-3 rounded-lg shadow-lg',
        'from-[#eeeeee] to-[#fffbfb] flex items-center gap-3',
        'min-w-70 max-w-md z-9999'
      ]"
      style="top: 70px; left: 50%;"
    >
      <!-- Icon -->
      <span
        v-if="icon"
        :class="[iconColor, 'shrink-0']"
        v-html="icon"
      ></span>

      <!-- Message -->
      <span class="flex-1 text-gray-800 font-medium">
        {{ flash.message }}
      </span>

      <!-- Count Badge -->
      <span
        v-if="flash.count > 1"
        :class="[
          iconColor,
          'px-2 py-0.5 rounded-full text-xs font-bold bg-opacity-10 text-white',
          flash.type === 'success' ? 'bg-green-500' : '',
          flash.type === 'error' ? 'bg-red-500' : '',
          flash.type === 'info' ? 'bg-blue-500' : ''
        ]"
      >
        {{ flash.count }}
      </span>

      <!-- Close Button -->
      <button
        @click="closeFlash"
        class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
        aria-label="Close notification"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </transition>
  </Teleport>
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
