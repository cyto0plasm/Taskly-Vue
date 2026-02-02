<script setup>
import BaseModal from "./baseModal.vue";
import { useModalStack } from "../../composables/useModalStack.js";
import { ref } from "vue";

const { activeModal, closeModal } = useModalStack();
const projectColor = ref("#6B3EEA");
</script>

<template>
    <BaseModal
        :show="activeModal === 'project'"
        title="Create Project"
        description="Organize your work"
        :color="projectColor"
        @close="closeModal"
    >
        <!-- content -->
        <div>Project form goes here</div>

        <template #footer>
            <div class="flex gap-2">
                <button
                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all"
                    @click="closeModal"
                >
                    Cancel
                </button>
                <button
                    :style="{ backgroundColor: projectColor }"
                    @mouseover="projectColor = '#5a33d1'"
                    @mouseleave="projectColor = '#6B3EEA'"
                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 text-white rounded-lg font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                    Save
                </button>
            </div>
        </template>
    </BaseModal>
</template>
