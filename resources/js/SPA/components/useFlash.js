// useFlash.js
import { reactive } from 'vue'

// SINGLETON
const flash = reactive({
  message: '',
  type: '',
  visible: false,
  count: 1
})

const deletingTasks = new Set()
let hideTimeout = null
let fadeTimeout = null

function show(type, message, duration = 3000, forceNew = false) {
  const isSameMessage = flash.type === type && flash.message === message;

  clearTimeout(hideTimeout)
  clearTimeout(fadeTimeout)

  if (!isSameMessage || forceNew) {
    flash.message = message
    flash.type = type
    flash.count = 1
    flash.visible = true
  } else {
    flash.count++
    flash.visible = true
  }

  hideTimeout = setTimeout(() => {
    flash.visible = false
    fadeTimeout = setTimeout(() => {
      flash.count = 1
    }, 500)
  }, duration)
}

function startDeleting(taskId) {
  if (deletingTasks.has(taskId)) return false
  deletingTasks.add(taskId)
  return true
}

function finishDeleting(taskId) {
  deletingTasks.delete(taskId)
}

// EXPORT SINGLETON
export const flashSingleton = { flash, show, startDeleting, finishDeleting }

export function useFlash() {
  return flashSingleton
}
