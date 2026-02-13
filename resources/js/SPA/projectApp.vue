<script setup>

import { computed, defineAsyncComponent, onMounted } from "vue";
// import Demo from "./demo.vue";
import {useLayoutStore} from "./store/layoutStore.js"

const ProjectList =defineAsyncComponent(()=>import('./projects/projectsList.vue'));
const ProjectDetails =defineAsyncComponent(()=>import('./projects/ProjectDetails.vue'));
const Settings =defineAsyncComponent(()=>import('./projects/ProjectSettings.vue'));
// const DrawerCanvas =defineAsyncComponent(()=>import('./components/DrawerCanvas.vue'));

const layout = useLayoutStore()
onMounted(() => {
  layout.setActive("projects");
});
const detailsVisible = computed(() => layout.layouts.projects.detailsSections.details.visible);

</script>

<template>
    <!-- <Demo></Demo> -->
    <section
        id="mainSection"
        class="relative flex flex-col gap-1 min-h-screen sm:p-3 md:p-2 lg:flex-row lg:items-start lg:gap-6 py-2"
    >
        <div class="lg:py-2 mx-2">
            <Settings />
        </div>
        <div class="shrink-0 w-full lg:w-md lg:min-w-md overflow-hidden">
            <ProjectList ref="projectListRef"></ProjectList>
        </div>

        <!-- Project Details -->
        <div v-if="detailsVisible" class="flex-1 w-full px-2">
            <ProjectDetails />
        </div>
        <div v-else></div>
    </section>
</template>
