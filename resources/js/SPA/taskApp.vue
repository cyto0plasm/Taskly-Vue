<!-- App.vue -->
<template>
  <!-- Global UI -->
  <FlashMessage />
  <TaskModal />
  <ProjectModal />

  <!-- Main content -->
  <div class="app-container">
    <TaskList />
    <TaskDetails />
  </div>
</template>

<script setup>
import TaskList from './tasks/TaskList.vue'
import TaskDetails from './tasks/TaskDetails.vue'
import FlashMessage from './components/FlashMessage.vue'
import TaskModal from './components/modals/TaskModal.vue'
import ProjectModal from './components/modals/ProjectModal.vue'
</script>
