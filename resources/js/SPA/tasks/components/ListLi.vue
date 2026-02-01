<script setup>
import { defineEmits } from "vue";
import { ref, computed } from "vue";
import { deleteTask } from "@/domain/tasks/taskAPI";
import CheckIcon from "../../svg/CheckIcon.vue";
import progressIcon from "../../svg/progressIcon.vue";
import pendingIcon from "../../svg/pendingIcon.vue";
import { useFlash } from "../../components/useFlash.js";
import FlashMessage from "../../components/FlashMessage.vue";
import { timeAgo } from "@/utils/timeAgo.js";

const showItem = ref(true);
const deleting = ref(false);

const props = defineProps({
    task: { type: Object, required: true },
    isSelected: { type: Boolean, default: false },
});
const createdAt = computed(() => timeAgo(props.task.created_at));
const emit = defineEmits({
    "select-task": (task) => !!task,
    "delete-task": (id) => typeof id === "number",
});

function selectTask() {
    if (!props.isSelected) emit("select-task", props.task.id);
}

async function requestDelete() {
    if (deleting.value) return;
    deleting.value = true;
    showItem.value = false;
}
</script>
<template>
    <transition name="fade" @after-leave="emit('delete-task', props.task.id)">
        <li
            v-if="showItem"
            @click="selectTask"
            :id="`task-${props.task.id}`"
            :data-id="props.task.id"
            class="group task-item relative block border-b border-gray-100 dark:border-0 transition-colors cursor-move"
            :class="{
                'bg-blue-50 dark:bg-gray-700': props.isSelected,
                'hover:bg-gray-100 dark:hover:bg-gray-800': !props.isSelected,
            }"
        >
            <span
                v-if="props.isSelected"
                class="absolute left-0 top-0 h-full w-1 bg-emerald-500"
            ></span>

            <div
                class="flex items-center gap-3 px-6 pt-4 pb-2 text-gray-900 dark:text-white"
            >
                <!-- Status Icon -->
                <div class="task-status-icon flex-shrink-0 self-start mt-2">
                    <div
                        v-if="task.status === 'done'"
                        class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center"
                    >
                        <CheckIcon :size="16" color="white" />
                    </div>
                    <span v-else-if="task.status === 'in_progress'">
                        <progressIcon :size="20" />
                    </span>
                    <span v-else>
                        <pendingIcon :size="20" />
                    </span>
                </div>

                <!-- Task Info -->
                <div class="flex-1 min-w-0">
                    <p
                        class="text-lg truncate"
                        :class="
                            task.status === 'done'
                                ? 'text-gray-600 dark:text-gray-100'
                                : 'text-gray-800 dark:text-white font-medium'
                        "
                    >
                        {{ task.title }}
                    </p>

                    <p
                        class="text-sm font-medium mt-1"
                        :class="{
                            'text-green-600': task.status === 'done',
                            'text-yellow-600': task.status === 'in_progress',
                            'text-red-600': task.status === 'pending',
                        }"
                    >
                        {{
                            task.status === "in_progress"
                                ? "In Progress"
                                : task.status.charAt(0).toUpperCase() +
                                  task.status.slice(1)
                        }}
                    </p>
                    <p class="text-gray-500 font-light">{{ createdAt }}</p>
                </div>

                <!-- Delete Button -->
                <button
                    v-if="task.id"
                    class="group/delete z-10 opacity-0 group-hover:opacity-100 transition-all duration-150 px-2 bt-2 pb-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 hover:scale-110 active:scale-95 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                    @click.stop="requestDelete"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        width="20"
                        height="20"
                        class="pointer-events-none transition-colors"
                    >
                        <path
                            d="M18 6L6 18M6 6L18 18"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </button>
            </div>
        </li>
    </transition>
</template>

<style>
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease-in-out;
}
</style>
