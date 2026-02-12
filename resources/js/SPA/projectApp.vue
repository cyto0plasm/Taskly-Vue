<script setup>
  import Demo from './demo.vue'
    import ProjectList from "./projects/projectsList.vue"
      import Settings from "./projects/ProjectSettings.vue"

</script>

<template>
<!-- <Demo></Demo> -->
  <section
    id="mainSection"
    class="relative flex flex-col gap-1 min-h-screen
    sm:p-3 md:p-2
    lg:flex-row lg:items-start lg:gap-6 py-2"
    >

 <div class="lg:py-2 mx-2 ">
      <Settings
      />
    </div>
   <div class="shrink-0 w-full lg:w-md lg:min-w-md overflow-hidden  ">
<ProjectList ref="projectListRef"></ProjectList>
   </div>


   </section>
</template>
