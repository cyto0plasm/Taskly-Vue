  <script setup>
  import {computed, defineAsyncComponent, onMounted, ref, watch} from "vue"
//   import Demo from './demo.vue'

import {useLayoutStore} from'./store/layout-store.js'

const TaskList =defineAsyncComponent(()=>import('./tasks/TaskList.vue'));
const TaskDetails =defineAsyncComponent(()=>import('./tasks/TaskDetails.vue'));
const Settings =defineAsyncComponent(()=>import('./tasks/TaskSettings.vue'));
const DrawerCanvas =defineAsyncComponent(()=>import('./components/canvas/DrawerCanvas.vue'));

const layout = useLayoutStore()
const detailsVisible = computed(() => layout.layouts.tasks.detailsSections.details.visible);
const canvasVisibile = computed(() => layout.layouts.tasks.detailsSections.canvas.visible);

watch(detailsVisible,()=>console.log(detailsVisible)
)
onMounted(() => {
  layout.setActive("tasks");
});
  </script>
<template>

    <section
    id="mainSection"
    class="relative flex flex-col gap-1
    sm:p-3 md:p-2
    lg:flex-row lg:items-start lg:gap-6 py-2"
    >
<h1 class="sr-only">Task Management Dashboard</h1>




    <!-- Settings -->
     <div class="lg:py-2 mx-2 ">
      <Settings
      />
    </div>
    <!-- Main content -->
      <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 w-full">

  <!-- Task List -->
  <div class="shrink-0 w-full lg:w-md lg:min-w-md overflow-hidden  ">
    <TaskList ref="taskListRef"  />
  </div>

    <!-- Task Details -->
    <div v-if="detailsVisible" class="flex-1 w-full px-2 ">
        <TaskDetails />
    </div>
    <div v-else-if="canvasVisibile" class="flex-1 w-full px-2">

        <DrawerCanvas type="task" />
    </div>
</div>
  </section>
<!-- <Demo :active="true"/> -->

</template>


