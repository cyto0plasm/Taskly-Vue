<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const isOpen = ref(false);
let resolver = null;

function openConfirm() {
    isOpen.value = true;
    return new Promise((resolve) => {
        resolver = resolve;
    });
}

function confirm() {
    isOpen.value = false;
    resolver?.(true);
}

function cancel() {
    isOpen.value = false;
    resolver?.(false);
}

function handleKeydown(e) {
    if (e.key === "Escape" && isOpen.value) cancel();
}

onMounted(() => document.addEventListener("keydown", handleKeydown));
onUnmounted(() => document.removeEventListener("keydown", handleKeydown));

defineExpose({ openConfirm });
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
             @click="cancel">

            <div @click.stop
                 class="bg-white dark:bg-gray-800 border rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-6">

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Confirm delete
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-300">
                    This action cannot be undone. Are you sure?
                </p>

                <div class="flex justify-end gap-3">
                    <button @click="cancel"
                        class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700
                               text-gray-700 dark:text-gray-200 hover:opacity-90">
                        Cancel
                    </button>

                    <button @click="confirm"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white
                               hover:bg-red-600">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>
