<script setup>
import { computed } from 'vue';
import Card from './card.vue';
import { useDashboardSettingsStore } from '../store/dashboard-settings-store.js';

const props = defineProps({
    cardKey:     { type: String,  required: true },
    type:        { type: String,  required: true },
    title:       { type: String,  required: true },
    description: { type: String,  default: '' },
    number:      { type: [String, Number], default: 0 },
    color:       { type: String,  default: 'indigo' },
    href:        { type: String,  default: '#' },
});

const COLOR_MAP = {
    yellow:     'bg-yellow-500',
    green:      'bg-green-500',
    teal:       'bg-teal-600',
    red:        'bg-red-500',
    'red-dark': 'bg-red-700',
    indigo:     'bg-indigo-500',
};

const store = useDashboardSettingsStore();

const state = computed(() => {
    const map = props.type === 'projects' ? store.projectCards : store.taskCards;
    return map[props.cardKey] ?? { visible: true, collapsed: false };
});
</script>

<template>
    <transition name="card-fade">
        <div v-if="state.visible" class="w-full h-full">

            <!-- Collapsed strip -->
            <div v-if="state.collapsed"
                :class="[COLOR_MAP[color] ?? 'bg-indigo-500',
                         'h-10 flex items-center px-4 rounded-xl text-white text-xs font-semibold shadow-md select-none']">
                {{ title }}
            </div>

            <!-- Full card -->
            <Card v-else
                :type="cardKey"
                :title="title"
                :description="description"
                :number="number"
                :color="color"
                :href="href"
                class="!w-full !h-full"
            />
        </div>
    </transition>
</template>

<style scoped>
.card-fade-enter-active,
.card-fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.card-fade-enter-from,
.card-fade-leave-to     { opacity: 0; transform: scale(0.97); }
</style>
