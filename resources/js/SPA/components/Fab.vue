<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useModalStack } from "../composables/useModalStack.js";
import { useTaskStore } from "../store/task-store.js";
//modal stack controller
const { openModal } = useModalStack();
const store= useTaskStore();
const isOpen = ref(false);
const fabMenu = ref(null);
const fabBtn = ref(null);
const entityNames = ["project", "task"];
const entityStyles = {
    project: "active:text-purple-300",
    task: "active:text-green-500",
};
//toggle fab menu
function toggleFAB() {
    isOpen.value = !isOpen.value;
}
//close fab menu
function closeFAB() {
    isOpen.value = false;
}
//open modal and close fab
function handleClick(entity) {
    if (entity === "task") {
 store.clearSelectedTaskForModal();
    }
    openModal(entity, { mode: "create" });
    closeFAB();
}
//close fab when clicking outside
function handleClickOutside(e) {
    if (!isOpen.value) return;
    if (
        fabMenu.value &&
        fabBtn.value &&
        !fabMenu.value.contains(e.target) &&
        !fabBtn.value.contains(e.target)
    ) {
        closeFAB();
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
<template>
    <div class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-50 ">
        <!-- Expandable Options Menu -->
        <div
            ref="fabMenu"
            id="fab-menu"
            class="absolute bottom-16 sm:bottom-20 right-0 flex flex-col gap-2 sm:gap-3 mb-2 z-50 origin-bottom transform transition-all duration-300 "
            :class="isOpen ? 'scale-y-100 opacity-100' : 'scale-y-0 opacity-0'"
            aria-label="Create options menu"
        >
            <button
                v-for="entity in entityNames"
                :key="entity"
                @click="handleClick(entity)"
                :class="entityStyles[entity]"
                class="cursor-pointer flex items-center gap-2 sm:gap-3 bg-white px-4 sm:px-5 py-2.5 sm:py-3 text-sm sm:text-base rounded-full shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all whitespace-nowrap font-medium "
            >
                <!-- project svg -->
                <svg
                    v-if="entity === 'project'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-[#6b3eea]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                    />
                </svg>
                <!-- task svg -->
                <svg
                    v-else-if="entity === 'task'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-[#10B981]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                New {{ entity.charAt(0).toUpperCase() + entity.slice(1) }}
            </button>
        </div>
        <!-- Main FAB Button -->
        <button
            ref="fabBtn"
            @click.stop="toggleFAB"
            class="cursor-pointer bg-[#6b3eea] text-white p-3 sm:p-4 rounded-full shadow-xl hover:bg-[#5c2fd1] hover:shadow-2xl active:bg-[#6335e0] transition-all duration-200"
            aria-label="Create new item"
            :aria-expanded="isOpen"
            aria-controls="fab-menu"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 sm:h-7 sm:w-7"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>
        </button>
    </div>
</template>
