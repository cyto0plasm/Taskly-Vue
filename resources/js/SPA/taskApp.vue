  <script setup>
  import {ref} from "vue"
  import TaskList from './tasks/TaskList.vue'
  import TaskDetails from './tasks/TaskDetails.vue'
  import FlashMessage from './components/FlashMessage.vue'

  import DrawerCanvas from './components/DrawerCanvas.vue'
  import Demo from './demo.vue'
import Settings from "./tasks/TaskSettings.vue"
  const taskListRef = ref(null)
const sidebarOpen = ref(false)


function toggleSection(section) {
  if (taskListRef.value) {
    taskListRef.value.toggleSection(section)
  }


}
function hideSection(section) {
  taskListRef.value?.hideSection(section);
}
function toggleHeaderBar(section) {
  if (taskListRef.value) {
    taskListRef.value.toggleHeaderBar(section);
  }
}

  </script>
<template>

    <section
    id="mainSection"
    class="relative flex flex-col gap-1 min-h-screen
    sm:p-3 md:p-2
    lg:flex-row lg:items-start lg:gap-6 py-2"
    >

    <FlashMessage />



    <!-- Settings -->
     <div class="lg:py-2 mx-2 ">
      <Settings
        @toggle-section="toggleSection"
        @hide-section="hideSection"
        @toggle-header-bar="toggleHeaderBar"
        :sections="taskListRef?.sections"
      />
    </div>
    <!-- Main content -->
      <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 w-full">

  <!-- Task List -->
  <div class="flex-shrink-0 w-full lg:w-[28rem] lg:min-w-[28rem] overflow-hidden  ">
    <TaskList ref="taskListRef"  />
  </div>

  <!-- Task Details -->
  <div class="flex-1 w-full px-2 ">
    <TaskDetails />
  </div>

</div>
  </section>
<!-- <Demo :active="true"/> -->

  <!-- <DrawerCanvas /> -->
</template>


