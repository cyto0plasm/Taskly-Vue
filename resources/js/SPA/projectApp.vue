<script setup>

import { computed, onMounted } from "vue";
import Demo from "./demo.vue";
import ProjectList from "./projects/projectsList.vue";
import ProjectDetails from "./projects/ProjectDetails.vue";
import Settings from "./projects/ProjectSettings.vue";
import {useLayoutStore} from "./store/layoutStore.js"

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
