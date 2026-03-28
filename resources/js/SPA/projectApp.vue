<script setup>

import { computed, defineAsyncComponent, onMounted, ref } from "vue";
// import Demo from "./demo.vue";
import {useLayoutStore} from "./store/layout-store.js"
import { useSettingsStore } from "./store/useSettingsStore.js";

const ProjectList =defineAsyncComponent(()=>import('./projects/projectsList.vue'));
const ProjectDetails =defineAsyncComponent(()=>import('./projects/ProjectDetails.vue'));
const Settings =defineAsyncComponent(()=>import('./projects/ProjectSettings.vue'));
const DrawerCanvas =defineAsyncComponent(()=>import('./components/canvas/DrawerCanvas.vue'));

const layout = useLayoutStore()
const settings = useSettingsStore()
const settingsRef = ref(null)

onMounted(() => {
  layout.setActive("projects");
  settings.register(() => settingsRef.value?.open())
});
const detailsVisible = computed(() => layout.layouts.projects.detailsSections.details.visible);
const canvasVisible = computed(() => layout.layouts.projects.detailsSections.canvas.visible);

</script>

<template>
    <!-- <Demo></Demo> -->
    <section
        id="mainSection"
        class="relative flex flex-col gap-1  sm:p-3 md:p-2 lg:flex-row lg:items-start lg:gap-6 py-2   "
    >
        <div class="lg:py-2 mx-2">
            <Settings ref="settingsRef"/>
        </div>
       <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 w-full">
    <div class="shrink-0 w-full lg:w-md lg:min-w-md overflow-hidden">
        <ProjectList ref="projectListRef" />
    </div>
    <div v-if="detailsVisible" class="flex-1 w-full px-2">
        <ProjectDetails />
    </div>
    <div v-else-if="canvasVisible" class="flex-1 w-full px-2">
        <DrawerCanvas type="project" />
    </div>
</div>
    </section>
</template>
