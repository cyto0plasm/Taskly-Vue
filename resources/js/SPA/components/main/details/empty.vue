 <script setup>
import { computed } from 'vue';

 const props = defineProps({
    type: {
        type: String,
        required: true
    }
 });
 const lable =computed(() => {
    switch (props.type) {
        case 'task':
            return "Task";
        case 'project':
            return "Project";
        default:
            return "Item";
    }
 });
 const computedText = computed(() => {
    switch (props.type) {
        case 'task':
            return "No task selected";
        case 'project':
            return "No project selected";
        default:
            return "No item selected";
    }
 });
</script>
<template>
    <div  id="taskDetailEmpty"
    class="w-full bg-white dark:bg-[#222321] rounded-lg shadow-md p-6 min-h-[18rem] sm:min-h-[20rem] flex flex-col items-center justify-center text-center">
    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">{{ computedText }}</h2>
    <p class="text-gray-500 dark:text-gray-400 mt-2">Create a {{ lable }} or select one from the list</p>
</div>
</template>
