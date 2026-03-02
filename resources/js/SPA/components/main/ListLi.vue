<script setup>
import { ref, computed } from "vue";
import CheckIcon from "../../svg/CheckIcon.vue";
import progressIcon from "../../svg/progressIcon.vue";
import pendingIcon from "../../svg/pendingIcon.vue";
import { timeAgo } from "@/utils/timeAgo.js";

const showItem = ref(true);
const deleting = ref(false);

const props = defineProps({
    entity: { type: Object, required: true },
    isSelected: { type: Boolean, default: false },
    isLoadMore:{type:Boolean,default:false},
    type: { type: String, default: "task" },
});
const createdAt = computed(() => timeAgo(props.entity.created_at));
const emit = defineEmits({
    "select-task": (task) => !!task,
    "delete-task": (id) => typeof id === "number",
});

function selectTask() {
    if (!props.isSelected) emit("select-task", props.entity.id);
}

function handleDelete() {
  emit("delete-task", props.entity.id); // emit to parent
}
let emitted = false;

function afterLeave() {
  showItem.value = false;

}
</script>
<template>
    <transition name="fade" @after-leave="afterLeave">
        <li
            v-if="showItem"
            @click="selectTask"
            :id="`task-${props.entity.id}`"
            :data-id="props.entity.id"
            class="group task-item relative block border-b border-gray-200/50 dark:border-0 transition-colors  "
            :class="{
                'bg-blue-100  dark:bg-gray-800 dark:hover:bg-gray-700': props.isSelected,
                'bg-gray-50 dark:bg-[#232422] hover:bg-gray-200 dark:hover:bg-gray-800 ': !props.isSelected,
  'cursor-move': isLoadMore,
      'cursor-pointer': !isLoadMore

            }"

        >
            <span
                v-if="props.isSelected"
                class="absolute left-0 top-0 h-full w-1 "
                   :class="[
  props.isSelected
    ? props.type === 'task'
    ? 'bg-emerald-500'
      : 'bg-indigo-500'
    : 'bg-gray-50'
]"
 ></span>

            <div
                class="flex items-center gap-3 px-6 pt-2 pb-2 text-gray-900 dark:text-white"
            >
                <!-- Status Icon -->
                <div class="task-status-icon flex-shrink-0 self-start mt-2">
                    <div
                        v-if="props.entity.status === 'done'"
                        class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center"
                    >
                        <CheckIcon :size="16" color="white" />
                    </div>
                    <span v-else-if="props.entity.status === 'in_progress'">
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
                            props.entity.status === 'done'
                                ? 'text-gray-600 dark:text-gray-100'
                                : 'text-gray-800 dark:text-white font-medium'
                        "
                    >
                        {{ props.entity.title||props.entity.name }}
                    </p>

                    <p
                    v-if="props.entity.status"
                        class="text-sm font-medium mt-1"
                        :class="{
                            'text-green-600': props.entity.status === 'done',
                            'text-yellow-600': props.entity.status === 'in_progress',
                            'text-red-600': props.entity.status === 'pending',
                        }"
                    >
                        {{
                            props.entity.status === "in_progress"
                                ? "In Progress"
                                : props.entity.status.charAt(0).toUpperCase() +
                                  props.entity.status.slice(1)
                        }}
                    </p>
                    <p class="text-gray-500 font-light">{{ createdAt }}</p>
                </div>

                <!-- Delete Button -->
                <button
                    v-if="props.entity.id"
                     :aria-label="'Delete task:'+  props.entity.title "
                    class="group/delete z-10 opacity-0 group-hover:opacity-100 transition-all duration-150 p-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30  active:scale-95 text-gray-400 hover:text-red-600 dark:hover:text-red-400 cursor-pointer"
                    @click.stop="handleDelete"
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
