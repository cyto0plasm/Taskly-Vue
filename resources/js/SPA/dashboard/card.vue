<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'title',
    },
    description: {
        type: String,
        default: 'description',
    },
    number: {
        type: [String, Number],
        default: 0,
    },
        dual: {
    type: Boolean,
    default: false,
    },
    left: {
    type: Object,
    default: null,
    },
    right: {
    type: Object,
    default: null,
    },
    href: {
        type: String,
        default: '#',
    },
    type: {
        type: String,
        default: 'tasks',
    },
    color: {
        type: String,
        default: 'green',
    },
});

const ICONS = {
        'projects'          : 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        'tasks'             : 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'progress'          : 'M4 19h16M8 17V9m4 8V5m4 12v-3',
        'pending'           : 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'upcoming-deadlines': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'completed'   : 'M5 13l4 4L19 7',
};

const COLOR_MAP = {
    'yellow': { bg: 'bg-yellow-500', light: 'bg-yellow-400', hover: 'group-hover:text-yellow-500' },
    'green' : { bg: 'bg-green-500',  light: 'bg-green-400',  hover: 'group-hover:text-green-500'  },
    'teal'  : { bg: 'bg-teal-600',   light: 'bg-teal-600',   hover: 'group-hover:text-teal-600'   },
    'red'   : { bg: 'bg-red-500',    light: 'bg-red-400',    hover: 'group-hover:text-red-500'    },
    'red-dark'  : { bg: 'bg-red-700',   light: 'bg-red-500',   hover: 'group-hover:text-red-500'   },
    'indigo': { bg: 'bg-indigo-500', light: 'bg-indigo-400', hover: 'group-hover:text-indigo-500' },
};

const icon = computed(() => ICONS[props.type] ?? ICONS['tasks']);
const c    = computed(() => COLOR_MAP[props.color] ?? COLOR_MAP['green']);
</script>

<template>
    <a :href="props.href"
        class="block shrink-0 group relative w-[240px] h-[150px] px-6 py-4 rounded-2xl shadow-md
               bg-white/30 dark:bg-[#1e1f1e] backdrop-blur-sm
               hover:bg-[#efefef] dark:hover:bg-[#272927] hover:shadow-lg hover:-translate-y-1
               transition-all duration-300 cursor-pointer">

        <!-- Status badge -->
        <div :class="[c.bg,
                      'absolute top-0 left-0 rounded-tl-2xl rounded-br-2xl h-9 px-3',
                      'w-[120px] group-hover:w-[150px] transition-[width] duration-300 ease-in-out',
                      'shadow-md flex items-center justify-center gap-1 overflow-hidden']">
            <svg class="w-3 h-3 text-white shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-white text-xs font-semibold truncate">{{ props.title }}</span>
        </div>

        <!-- Content -->
        <div class="flex flex-col justify-between h-full pt-9">

            <!-- Icon + description -->
            <div class="flex items-center gap-2">
                <div :class="[c.light, 'w-8 h-8 rounded-lg flex items-center justify-center shrink-0']">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500 dark:text-[#c5c5c5] font-medium leading-tight">{{ props.description }}</span>
            </div>

            <!-- Number -->
            <p :class="[c.hover, 'text-4xl font-bold text-gray-800 dark:text-white transition-colors duration-300']">
                {{ props.number }}
            </p>
        </div>

        <!-- Hover arrow -->
        <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </a>
</template>
