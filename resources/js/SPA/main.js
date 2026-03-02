import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { useProjectStore } from './store/project-store.js'
import { useAuthStore } from './store/user-store.js'

import TaskApp from './taskApp.vue'
import ProjectApp from './projectApp.vue'
import ModalApp from './ModalApp.vue'
import FlashApp from './FlashApp.vue'
import DashboardApp from './dashboardApp.vue'
import Auth from './auth/login-register.vue'
import "../utils/toolTip.js"

// ============= Load user ============

// 1️ Create ONE Pinia instance
const pinia = createPinia()


const tempApp = createApp({})
tempApp.use(pinia)
const projectStore = useProjectStore()
projectStore.loadAllProjects()

// 2️ Mount main apps FIRST
const taskDiv = document.getElementById('taskApp')
if (taskDiv) {
  createApp(TaskApp).use(pinia).mount('#taskApp')
}

const projectDiv = document.getElementById('projectApp')
if (projectDiv) {
  createApp(ProjectApp).use(pinia).mount('#projectApp')
}
const authStore = useAuthStore()
authStore.fetchUser()
// 3️ Mount modals
const authDev = document.getElementById('auth')
if (authDev) {
  createApp(Auth).use(pinia).mount('#auth')
}
const dashboardDiv = document.getElementById('dashboard')
if (dashboardDiv) {
  createApp(DashboardApp).use(pinia).mount('#dashboard')
}
const modalDiv = document.getElementById('ModalApp')
if (modalDiv) {
  createApp(ModalApp).use(pinia).mount('#ModalApp')
}

// 4️ Mount flash LAST (IMPORTANT)
const flashDiv = document.getElementById('flashApp')
if (flashDiv) {
  createApp(FlashApp).use(pinia).mount('#flashApp')
}
