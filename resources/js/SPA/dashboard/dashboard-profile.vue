<script setup>
import { ref, computed, watch, watchEffect } from 'vue';
// import { usePage }       from '@inertiajs/vue3';
import { route }         from 'ziggy-js';
import { useDashboardStore } from '../store/dashboard-store';
import { useAuthStore } from '../store/user-store';
const auth = useAuthStore()
const store = useDashboardStore();
const props = defineProps({
    selectedSection:{type:Boolean,default:true}
})
const prefs = ref({})
watchEffect(() => {
console.log('Current user', auth.user);
})
watch(
    () => auth.preferences,
    (newPrefs) => {
        prefs.value = { ...newPrefs }
    },
    { immediate: true }
)
// ── User from Inertia shared data ─────────────────────────────────────────
const user = computed(() => auth.user)
// Initials for avatar fallback
const initials = computed(() => {
    if (!user.value?.name) return 'U'
    return user.value.name
        .split(' ')
        .map(w => w[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

// Avatar colour based on initials (stable per user)
const AVATAR_COLORS = [
    'from-indigo-400 to-indigo-600',
    'from-teal-400 to-teal-600',
    'from-emerald-400 to-emerald-600',
    'from-violet-400 to-violet-600',
    'from-rose-400 to-rose-600',
    'from-amber-400 to-amber-600',
];
const avatarColor = computed(() => {
    const name = user.value?.name ?? 'User'
    const code = name.charCodeAt(0)
    return AVATAR_COLORS[code % AVATAR_COLORS.length]
})

// ── Quick stats pulled from store ─────────────────────────────────────────
const stats = computed(() => [
    {
        label: 'Total',
        value: (store.projectsCount ?? 0) + (store.tasksCount ?? 0),
        sub:   'items',
        color: 'text-indigo-500',
        bg:    'bg-indigo-50 dark:bg-indigo-900/20',
    },
    {
        label: 'Done',
        value: (store.completedProjects ?? 0) + (store.completedTasks ?? 0),
        sub:   'completed',
        color: 'text-emerald-500',
        bg:    'bg-emerald-50 dark:bg-emerald-900/20',
    },
    {
        label: 'Active',
        value: (store.projectsProgress ?? 0) + (store.tasksProgress ?? 0),
        sub:   'in progress',
        color: 'text-amber-500',
        bg:    'bg-amber-50 dark:bg-amber-900/20',
    },
    {
        label: 'Overdue',
        value: store.overdueCount ?? 0,
        sub:   'past due',
        color: store.overdueCount > 0 ? 'text-red-500' : 'text-gray-400',
        bg:    store.overdueCount > 0
            ? 'bg-red-50 dark:bg-red-900/20'
            : 'bg-gray-50 dark:bg-gray-800/40',
    },
]);

// ── Preferences ───────────────────────────────────────────────────────────
// Loaded from user meta / auth-store as a starting point.
// Each change is debounced and sent to PATCH /user/preferences.

const PREF_KEY = 'dashboard_prefs';

function loadPrefs() {
    try {
        const saved = localStorage.getItem(PREF_KEY);
        return saved ? JSON.parse(saved) : {};
    } catch { return {}; }
}

const saved = loadPrefs();

// const prefs = ref({
//     defaultView:          saved.defaultView         ?? 'home',       // home | projects | tasks
//     compactCards:         saved.compactCards         ?? false,
//     showCompletionRing:   saved.showCompletionRing   ?? true,
//     weekStartsMonday:     saved.weekStartsMonday     ?? false,
//     deadlineWarningDays:  saved.deadlineWarningDays  ?? 3,           // 1 | 3 | 7
// });

const emit = defineEmits(['prefsChanged']);

let debounceTimer = null;
function savePrefs() {
    localStorage.setItem(PREF_KEY, JSON.stringify(prefs.value));
    emit('prefsChanged', { ...prefs.value });

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
        try {
await auth.savePreferences(prefs.value)
        } catch (e) {
           console.error('Failed to save preferences', e);
        }
    }, 800);
}

// ── UI state ──────────────────────────────────────────────────────────────
const showPrefs = ref(false);

const VIEW_OPTIONS = [
    { value: 'home',     label: 'Overview'  },
    { value: 'projects', label: 'Projects'  },
    { value: 'tasks',    label: 'Tasks'     },
];

const WARNING_OPTIONS = [
    { value: 1, label: '1 day'  },
    { value: 3, label: '3 days' },
    { value: 7, label: '1 week' },
];
</script>

<template>

    <div class="group bg-white dark:bg-[#1a1b1a]
                ring-1 ring-gray-200 dark:ring-gray-700
                rounded-2xl shadow-sm overflow-hidden">

        <!-- ── Top band ──────────────────────────────────────────────────── -->
        <div  class="h-1 translate-x-full group-hover:translate-x-0 transition-all duration-600 ease-out      bg-linear-to-r from-indigo-500 via-violet-500 to-teal-500 dark:from-indigo-300 dark:via-violet-300 dark:to-teal-300" />

        <div class="group px-2.5 lg:px-6 py-3 lg:py-5.5">

            <!-- ── Identity row ──────────────────────────────────────────── -->
            <div class="flex items-center gap-4 mb-5">
                <!-- Avatar -->
                <div class="relative shrink-0">
                    <div v-if="auth.profilePhoto"
                         class="w-14 h-14 rounded-2xl overflow-hidden ring-2 ring-white dark:ring-gray-800 shadow-md">
                        <img :src="auth.profilePhoto" :alt="auth.userName" class="w-full h-full object-cover" />
                    </div>
                    <div v-else
                         :class="['w-14 h-14 rounded-2xl flex items-center justify-center',
                                  'bg-linear-to-br shadow-md ring-2 ring-white dark:ring-gray-800',
                                  avatarColor]">
                        <span class="text-white text-lg font-bold tracking-tight">{{ initials }}</span>
                    </div>

                    <!-- Online dot -->
                    <span class="absolute -bottom-0.5 -right-0.5
                                 w-3.5 h-3.5 rounded-full bg-emerald-400
                                 ring-2 ring-white dark:ring-[#1a1b1a]" />
                </div>

                <!-- Name / email / role -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white truncate leading-tight">
                        {{ auth.userName ?? 'User' }}
                    </h2>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate mt-0.5">
                        {{ auth.userEmail ?? '' }}
                    </p>
                    <span v-if="auth.userRole"
                          class="inline-block mt-1.5 px-2 py-0.5 rounded-full
                                 bg-indigo-50 dark:bg-indigo-900/30
                                 text-indigo-600 dark:text-indigo-400
                                 text-[10px] font-semibold capitalize">
                        {{ auth.userRole }}
                    </span>
                </div>

                <!-- Edit profile link -->
                <a
                   class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold
                          text-gray-500 dark:text-gray-400
                          bg-gray-100 dark:bg-gray-800
                          hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                          hover:text-indigo-600 dark:hover:text-indigo-400
                          transition-all duration-150">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>

            <!-- ── Quick stats ─────────────────────────────────────────── -->
            <div class="grid grid-cols-4 gap-2 mb-5 text-nowrap">
                <div v-for="s in stats" :key="s.label"
                     :class="[s.bg, 'rounded-xl px-3 py-2.5 flex flex-col items-center']">
                    <span :class="[s.color, 'text-sm md:text-xl font-bold leading-none tabular-nums']">
                        {{ s.value }}
                    </span>
                    <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 mt-1">
                        {{ s.label }}
                    </span>
                    <span class="text-[9px] text-gray-400 dark:text-gray-500">{{ s.sub }}</span>
                </div>
            </div>



        </div>
    </div>
</template>
