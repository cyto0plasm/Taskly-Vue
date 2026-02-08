import { createApp } from 'vue'
import { createPinia } from 'pinia'
import TaskApp from './taskApp.vue'
import ProjectApp from './projectApp.vue'
import ModalApp from './ModalApp.vue'

// 1 Create a single Pinia instance
const pinia = createPinia()

//  Mount modals + FAB separately
const modalDiv = document.getElementById('ModalApp')
if (modalDiv) {
  const modalApp = createApp(ModalApp)
  modalApp.use(pinia)
  modalApp.mount('#ModalApp')
}
//  Mount main task app
const taskDiv = document.getElementById('taskApp')
if (taskDiv) {
  const tasAapp = createApp(TaskApp)
  tasAapp.use(pinia)
  tasAapp.mount('#taskApp')
}


const projectDiv = document.getElementById('projectApp')
if (projectDiv) {
  const projectApp = createApp(ProjectApp)
  projectApp.use(pinia)
  projectApp.mount('#projectApp')
}
