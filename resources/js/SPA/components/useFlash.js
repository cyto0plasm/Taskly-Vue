import { reactive } from 'vue'

const flash = reactive({
  message: '',
  type: '',
  visible: false,
  count: 1
})

// Track deleting tasks globally (non-reactive Set for performance)
const deletingTasks = new Set()

let hideTimeout = null
let fadeTimeout = null

function show(type, message, duration = 3000, forceNew = false) {
  const isSameMessage = flash.type === type && flash.message === message;

  clearTimeout(hideTimeout);
  clearTimeout(fadeTimeout);

  if (!isSameMessage || forceNew) {
    // always reset if new or forced
    flash.message = message;
    flash.type = type;
    flash.count = 1;
    flash.visible = true;
  } else {
    // same message → increment count
    flash.count++;
    flash.visible = true; // ensure still visible
  }

  hideTimeout = setTimeout(() => {
    flash.visible = false;
    fadeTimeout = setTimeout(() => {
      flash.count = 1;
    }, 500);
  }, duration);
}



function startDeleting(taskId) {
  if (deletingTasks.has(taskId)) return false
  deletingTasks.add(taskId)
  return true
}

function finishDeleting(taskId) {
  deletingTasks.delete(taskId)
}

export function useFlash() {
  return {
    flash,
    show,
    startDeleting,
    finishDeleting
  }
}
