  <script setup>
  import {ref} from "vue"
  import TaskList from './tasks/TaskList.vue'
  import TaskDetails from './tasks/TaskDetails.vue'
  import DrawerCanvas from './components/DrawerCanvas.vue'
  import Demo from './demo.vue'
  import Settings from "./tasks/TaskSettings.vue"



  </script>
<template>

    <section
    id="mainSection"
    class="relative flex flex-col gap-1 min-h-screen
    sm:p-3 md:p-2
    lg:flex-row lg:items-start lg:gap-6 py-2"
    >




    <!-- Settings -->
     <div class="lg:py-2 mx-2 ">
      <Settings

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

   <DrawerCanvas />
</template>


