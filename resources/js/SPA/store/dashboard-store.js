import { defineStore } from 'pinia';
import * as DashboardApi from '../../domain/tasks/dashboard-api.js';
import { useFlash }       from '../components/useFlash.js';

const STATS_TTL   = 2 * 60 * 1000;   // 2 min — aggregation numbers
const WIDGETS_TTL = 3 * 60 * 1000;   // 3 min — list data changes less

export const useDashboardStore = defineStore('dashboard', {

    // ─────────────────────────────────────────────────────────────────────
    // STATE
    // ─────────────────────────────────────────────────────────────────────
    state: () => ({
        // ── Stats (summary banner + cards) ───────────────────────────────
        projectsCount:             0,
        completedProjects:         0,
        pendingProjects:           0,
        projectsProgress:          0,
        upcomingProjectDeadlines:  0,

        tasksCount:                0,
        completedTasks:            0,
        pendingTasks:              0,
        tasksProgress:             0,
        upcomingTaskDeadlines:     0,

        overdueCount:              0,
        dueTodayCount:             0,
        dueTomorrowCount:          0,

        // ── Widgets (list panels) ─────────────────────────────────────────
        // Each item: { type, id, name, status, updated_at }
        recentActivity: [],

        // Each item: { id, name, status, end_date, total_tasks, done_tasks,
        //              tasks_left, completion_pct }
        nearCompletion: [],

        // Each item: { type, id, name, status, due_date, days_late }
        overdueItems:   [],

        // ── Meta ──────────────────────────────────────────────────────────
        loadingStats:    false,
        loadingWidgets:  false,
        refreshingStats:   false,
        refreshingWidgets: false,

        statsFetchedAt:    null,
        widgetsFetchedAt:  null,
        error: null,
    }),

    // ─────────────────────────────────────────────────────────────────────
    // GETTERS
    // ─────────────────────────────────────────────────────────────────────
    getters: {
        hasStats:   (s) => s.statsFetchedAt   !== null,
        hasWidgets: (s) => s.widgetsFetchedAt !== null,

        statsStale:   (s) => !s.statsFetchedAt   || Date.now() - s.statsFetchedAt   > STATS_TTL,
        widgetsStale: (s) => !s.widgetsFetchedAt || Date.now() - s.widgetsFetchedAt > WIDGETS_TTL,

        // Convenience for the summary component
        isLoading: (s) => s.loadingStats || s.loadingWidgets,
    },

    // ─────────────────────────────────────────────────────────────────────
    // ACTIONS
    // ─────────────────────────────────────────────────────────────────────
    actions: {
        /**
         * Load everything needed for the dashboard.
         * Stats and widgets are fetched in parallel — stats return fast
         * (aggregations), widgets may be slightly slower (row queries).
         */
        async loadDashboardData(force = false) {
            await Promise.all([
                this.loadStats(force),
                this.loadWidgets(force),
            ]);
        },

        // ── Stats ─────────────────────────────────────────────────────────
        async loadStats(force = false) {
            if (!force && !this.statsStale && this.hasStats) return;

            const first = !this.hasStats;
            first ? (this.loadingStats = true) : (this.refreshingStats = true);

            try {
                const { data } = await DashboardApi.fetchDashboardData();
                this.$patch({
                    projectsCount:             data.projectsCount            ?? 0,
                    completedProjects:         data.completedProjects         ?? 0,
                    pendingProjects:           data.pendingProjects           ?? 0,
                    projectsProgress:          data.projectsProgress          ?? 0,
                    upcomingProjectDeadlines:  data.upcomingProjectDeadlines  ?? 0,

                    tasksCount:                data.tasksCount                ?? 0,
                    completedTasks:            data.completedTasks            ?? 0,
                    pendingTasks:              data.pendingTasks              ?? 0,
                    tasksProgress:             data.tasksProgress             ?? 0,
                    upcomingTaskDeadlines:     data.upcomingTaskDeadlines     ?? 0,

                    overdueCount:              data.overdueCount              ?? 0,
                    dueTodayCount:             data.dueTodayCount             ?? 0,
                    dueTomorrowCount:          data.dueTomorrowCount          ?? 0,

                    statsFetchedAt: Date.now(),
                    error: null,
                });
            } catch (err) {
                this.error = err?.response?.data?.message ?? 'Failed to load stats.';
                if (first) useFlash().show(this.error, 'error');
                console.error('[DashboardStore] stats error:', err);
            } finally {
                this.loadingStats    = false;
                this.refreshingStats = false;
            }
        },

        // ── Widgets ───────────────────────────────────────────────────────
        async loadWidgets(force = false) {
            if (!force && !this.widgetsStale && this.hasWidgets) return;

            const first = !this.hasWidgets;
            first ? (this.loadingWidgets = true) : (this.refreshingWidgets = true);

            try {
                const { data } = await DashboardApi.fetchDashboardWidgets();
                this.$patch({
                    recentActivity:   data.recentActivity ?? [],
                    nearCompletion:   data.nearCompletion ?? [],
                    overdueItems:     data.overdueItems   ?? [],
                    widgetsFetchedAt: Date.now(),
                    error: null,
                });
            } catch (err) {
                this.error = err?.response?.data?.message ?? 'Failed to load widgets.';
                if (first) useFlash().show(this.error, 'error');
                console.error('[DashboardStore] widgets error:', err);
            } finally {
                this.loadingWidgets    = false;
                this.refreshingWidgets = false;
            }
        },

        /**
         * Force-refresh both — call this after any create/update/delete action
         * anywhere in the app so the dashboard stays in sync.
         */
        async invalidateAndRefresh() {
            await Promise.all([
                this.loadStats(true),
                this.loadWidgets(true),
            ]);
        },

        /**
         * Optimistic counter nudge.
         * e.g. after marking a task done:
         *   store.adjustCounter('completedTasks', +1)
         *   store.adjustCounter('pendingTasks',   -1)
         */
        adjustCounter(key, delta) {
            if (key in this.$state && typeof this.$state[key] === 'number') {
                this.$state[key] = Math.max(0, this.$state[key] + delta);
            }
        },
    },
});
