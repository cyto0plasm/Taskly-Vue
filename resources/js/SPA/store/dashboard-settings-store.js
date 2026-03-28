import { defineStore } from 'pinia';
import { ref } from 'vue';

const PROJECT_KEYS = ['projects', 'deadlines-projects', 'completed-projects', 'progress-projects', 'pending-projects'];
const TASK_KEYS    = ['tasks',    'deadlines-tasks',    'completed-tasks',    'progress-tasks',    'pending-tasks'];

export const CARD_LABELS = {
    'projects':            'Total Projects',
    'deadlines-projects':  'Deadlines',
    'completed-projects':  'Completed',
    'progress-projects':   'In Progress',
    'pending-projects':    'Pending',
    'tasks':               'Total Tasks',
    'deadlines-tasks':     'Deadlines',
    'completed-tasks':     'Completed',
    'progress-tasks':      'In Progress',
    'pending-tasks':       'Pending',
};

function makeDefaults(keys) {
    return Object.fromEntries(keys.map((k, i) => [k, { visible: true, collapsed: false, order: i }]));
}

export const useDashboardSettingsStore = defineStore('dashboardSettings', () => {
    const projectCards = ref(makeDefaults(PROJECT_KEYS));
    const taskCards    = ref(makeDefaults(TASK_KEYS));

    function _map(type) {
        return type === 'projects' ? projectCards : taskCards;
    }

    /** Returns cards sorted by order for rendering */
    function ordered(type) {
        return Object.entries(_map(type).value)
            .sort(([, a], [, b]) => a.order - b.order)
            .map(([key, s]) => ({ key, label: CARD_LABELS[key], ...s }));
    }

    function toggleVisible(type, key)  { _map(type).value[key].visible   ^= true; }
    function toggleCollapse(type, key) { _map(type).value[key].collapsed ^= true; }

    function move(type, key, dir) {
        const map    = _map(type).value;
        const sorted = Object.entries(map).sort(([, a], [, b]) => a.order - b.order);
        const idx    = sorted.findIndex(([k]) => k === key);
        const swap   = dir === 'up' ? idx - 1 : idx + 1;
        if (swap < 0 || swap >= sorted.length) return;
        [sorted[idx][1].order, sorted[swap][1].order] = [sorted[swap][1].order, sorted[idx][1].order];
    }

    return { projectCards, taskCards, ordered, toggleVisible, toggleCollapse, move };
});
