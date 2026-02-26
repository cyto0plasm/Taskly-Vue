<script setup>
import { defineAsyncComponent } from 'vue';



const Fab =defineAsyncComponent(()=>import('./components/Fab.vue'));
const TaskModal =defineAsyncComponent(()=>import('./components/modals/taskModal.vue'));
const ProjectModal =defineAsyncComponent(()=>import('./components/modals/ProjectModal.vue'));
</script>

<template>

    <Fab />
     <TaskModal />
  <ProjectModal />
</template>
