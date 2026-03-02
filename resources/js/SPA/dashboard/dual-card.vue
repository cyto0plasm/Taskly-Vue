<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: { type: String, default: 'title' },
    type:  { type: String, default: 'total' },
    color: { type: String, default: 'indigo' },
    left:  { type: Object, default: null },   // { label, value, route }
    right: { type: Object, default: null },   // { label, value, route }
});

// ── Icons ──────────────────────────────────────────────────────────────────
const ICONS = {
    total:    'M4 6h16M4 10h16M4 14h8',
    deadlines:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
    completed:'M5 13l4 4L19 7',
    progress: 'M4 19h16M8 17V9m4 8V5m4 12v-3',
    pending:  'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
};

const PROJECT_ICON = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
const TASK_ICON    = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';

// ── Colors ─────────────────────────────────────────────────────────────────
const COLOR_MAP = {
    yellow:    { bg: 'bg-amber-500',   ring: 'ring-amber-200 dark:ring-amber-800',     bar: 'bg-amber-400',    text: 'text-amber-500'   },
    green:     { bg: 'bg-emerald-500', ring: 'ring-emerald-200 dark:ring-emerald-800', bar: 'bg-emerald-400',  text: 'text-emerald-500' },
    teal:      { bg: 'bg-teal-500',    ring: 'ring-teal-200 dark:ring-teal-800',       bar: 'bg-teal-400',     text: 'text-teal-500'    },
    red:       { bg: 'bg-rose-500',    ring: 'ring-rose-200 dark:ring-rose-800',       bar: 'bg-rose-400',     text: 'text-rose-500'    },
    'red-dark':{ bg: 'bg-red-700',     ring: 'ring-red-200 dark:ring-red-800',         bar: 'bg-red-500',      text: 'text-red-600'     },
    indigo:    { bg: 'bg-indigo-500',  ring: 'ring-indigo-200 dark:ring-indigo-800',   bar: 'bg-indigo-400',   text: 'text-indigo-500'  },
};

// ── Computed ───────────────────────────────────────────────────────────────
const icon = computed(() => ICONS[props.type] ?? ICONS.total);
const c    = computed(() => COLOR_MAP[props.color] ?? COLOR_MAP.indigo);

const TYPE_HINTS = {
    total:     'Combined count',
    deadlines: 'Due within 7 days',
    completed: 'Finished items',
    progress:  'Currently active',
    pending:   'Not started',
};
const hint = computed(() => TYPE_HINTS[props.type] ?? '');

// Ratio bar: what % of total belongs to the left (projects) side
const leftRatio = computed(() => {
    const l = Number(props.left?.value  ?? 0);
    const r = Number(props.right?.value ?? 0);
    const total = l + r;
    if (total === 0) return 50;
    return Math.round((l / total) * 100);
});
</script>

<template>
    <div
        class="group relative shrink-0 w-[260px] h-[150px] rounded-2xl shadow-md overflow-hidden
               bg-white dark:bg-[#1a1b1a]
               ring-1 ring-gray-200 dark:ring-gray-700
               hover:shadow-lg hover:-translate-y-1 hover:ring-2
               transition-all duration-300 select-none"
        :class="c.ring"
    >
        <!-- Top colour accent -->
        <div :class="[c.bg, 'absolute top-0 left-0 right-0 h-[3px]']" />

        <div class="flex flex-col h-full px-4 pt-3 pb-3">

            <!-- Header ──────────────────────────────────────────────────── -->
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold tracking-wide uppercase" :class="c.text">
                        {{ title }}
                    </p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight mt-0.5">
                        {{ hint }}
                    </p>
                </div>
                <!-- Icon chip -->
                <div :class="[c.bg, 'w-7 h-7 rounded-lg flex items-center justify-center shrink-0 shadow-sm']">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="icon" />
                    </svg>
                </div>
            </div>

            <!-- Values ──────────────────────────────────────────────────── -->
            <div class="flex items-end justify-between mt-auto">

                <!-- Left (Projects) -->
                <a :href="left?.route ? `/${left.route}` : '#'"
                   class="flex flex-col items-start hover:opacity-75 transition-opacity cursor-pointer">
                    <div class="flex items-center gap-1 mb-0.5">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="PROJECT_ICON" />
                        </svg>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                            {{ left?.label }}
                        </span>
                    </div>
                    <span :class="[c.text, 'text-2xl font-bold leading-none']">
                        {{ left?.value ?? 0 }}
                    </span>
                </a>

                <!-- vs divider -->
                <span class="self-end mb-1 text-[9px] font-bold tracking-widest
                             text-gray-300 dark:text-gray-600">vs</span>

                <!-- Right (Tasks) -->
                <a :href="right?.route ? `/${right.route}` : '#'"
                   class="flex flex-col items-end hover:opacity-75 transition-opacity cursor-pointer">
                    <div class="flex items-center gap-1 mb-0.5">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                            {{ right?.label }}
                        </span>
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="TASK_ICON" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-800 dark:text-white">
                        {{ right?.value ?? 0 }}
                    </span>
                </a>
            </div>

            <!-- Ratio bar ───────────────────────────────────────────────── -->
            <div class="mt-2">
                <div class="relative h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                    <div
                        :class="[c.bar, 'absolute left-0 top-0 h-full rounded-full transition-all duration-700 ease-out']"
                        :style="{ width: leftRatio + '%' }"
                    />
                </div>
                <div class="flex justify-between mt-0.5">
                    <span class="text-[9px] text-gray-400 dark:text-gray-500">{{ leftRatio }}%</span>
                    <span class="text-[9px] text-gray-400 dark:text-gray-500">{{ 100 - leftRatio }}%</span>
                </div>
            </div>

        </div>
    </div>
</template>
