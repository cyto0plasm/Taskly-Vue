// stores/taskStore.js
import { defineStore } from "pinia";
import { shallowRef } from "vue";
import {
  fetchTask as apiFetchTask,
  fetchAllTasksVue as apiFetchAllTasks,
  markAsCompleteTask as apiMarkAsComplete,
  reorderTasks as apiReorderTasks,
  deleteTask as apiDeleteTask,
} from "@/domain/tasks/TaskAPI";
import { useFlash } from "../components/useFlash.js";

export const useTaskStore = defineStore("task", {
  id: "task",

  state: () => ({
    tasks: shallowRef([]),         // Visible tasks (paginated or all)
    allTasks: shallowRef([]),      // Cached "all tasks" for totals & Show All
    pagination: {
      page: 1,
      lastPage: 1,
      total: 0,
      statusCounts: { done: 0, pending: 0, in_progress: 0 },
    },
    allStatusCounts: { done: 0, pending: 0, in_progress: 0 }, // Total counts
    taskCache: {},                // Individual task cache
    selectedTaskId: null,
    selectedTask: null,
    loading: false,
    loadingSelectedTask: false,
  }),

  actions: {

    /**
     * Load tasks from API
     * @param {object} options
     * @param {number} options.page - current page for pagination
     * @param {boolean} options.showAll - whether to load all tasks
     */
    async loadTasks({ page = 1, showAll = false } = {}) {
      this.loading = true;

      try {
        if (showAll) {
          // ------------------------
          // SHOW ALL: Load all tasks
          // ------------------------
          const res = await apiFetchAllTasks({ showAll: true });
          const allTasks = Array.isArray(res.data) ? res.data : [];

          this.allTasks = allTasks; // cache all tasks
          this.tasks = allTasks;    // set visible tasks
          this._updateStatusCounts(allTasks, "all");
          this._updateStatusCounts(allTasks, "pagination"); // visible counts
          this.pagination.page = 1;
          this.pagination.lastPage = 1;
          this.pagination.total = allTasks.length;

        } else {
          // ------------------------
          // PAGINATION: Load current page
          // ------------------------
          const res = await apiFetchAllTasks({ page, showAll: false });
          const tasks = Array.isArray(res.data) ? res.data : [];

          this.tasks = tasks;
          this.pagination.page = res.current_page ?? 1;
          this.pagination.lastPage = res.last_page ?? 1;
          this.pagination.total = res.total ?? tasks.length;

          this._updateStatusCounts(tasks, "pagination");

          // ------------------------
          // Load all tasks in background for total counts
          // only if not already loaded
          // ------------------------
          if (!this.allTasks.length) {
            apiFetchAllTasks({ showAll: true })
              .then(res => {
                const allTasks = Array.isArray(res.data) ? res.data : [];
                this.allTasks = allTasks;
                this._updateStatusCounts(allTasks, "all");
              })
              .catch(err => console.error("Failed to load all tasks for totals", err));
          }
        }
      } catch (err) {
        console.error("Failed to load tasks", err);
        this.tasks = [];
        this._updateStatusCounts([], "pagination");
      } finally {
        this.loading = false;
      }
    },

    /**
     * Select a single task by ID
     * Loads from cache if available
     */
    async selectTask(id) {
      if (!id || this.selectedTaskId === id) return;

      this.selectedTaskId = id;

      if (!this.taskCache[id]) {
        this.loadingSelectedTask = true;
        try {
          this.taskCache[id] = await apiFetchTask(id);
        } catch (err) {
          console.error("Failed to fetch task:", err);
          this.selectedTaskId = null;
        } finally {
          this.loadingSelectedTask = false;
        }
      }

      this.selectedTask = this.taskCache[id] || null;
    },

    /**
     * Update task status (done, in_progress, pending)
     */
    async updateTaskStatus(taskId, newStatus) {
      if (!taskId) return;

      const { show } = useFlash();

      try {
        if (newStatus === "done") {
          await apiMarkAsComplete(taskId);
        }

        this._updateTask(taskId, { status: newStatus });

        show("success", "Task status updated");
      } catch (err) {
        console.error(`Failed to update task ${taskId} status`, err);
        show("error", "Failed to update task status");
      }
    },

    /**
     * Internal helper: update task in all relevant places
     */
    _updateTask(taskId, updates) {
      // update selected task
      if (this.selectedTask?.id === taskId) {
        this.selectedTask = { ...this.selectedTask, ...updates };
      }

      // update tasks array (visible)
      const idx = this.tasks.findIndex(t => t.id === taskId);
      if (idx !== -1) {
        this.tasks[idx] = { ...this.tasks[idx], ...updates };
      }

      // update cache
      if (this.taskCache[taskId]) {
        this.taskCache[taskId] = { ...this.taskCache[taskId], ...updates };
      }

      // recalc counts
      this._updateStatusCounts(this.tasks, "pagination");
      if (this.allTasks?.length) this._updateStatusCounts(this.allTasks, "all");
    },

    /**
     * Internal helper: count tasks by status
     */
    _updateStatusCounts(tasks = this.tasks, target = "pagination") {
      const counts = { done: 0, pending: 0, in_progress: 0 };
      tasks.forEach(t => {
        if (counts[t.status] !== undefined) counts[t.status]++;
      });

      if (target === "pagination") this.pagination.statusCounts = counts;
      else this.allStatusCounts = counts;
    },

    /**
     * Reorder tasks (drag & drop)
     */
    async reorderTasks(newOrder) {
      try {
        await apiReorderTasks(newOrder.map(item => ({ id: item.id, position: item.position })));
      } catch (err) {
        console.error("Failed to reorder tasks", err);
        await this.loadTasks({ showAll: true }); // fallback
      }
    },

    /**
     * Delete a task
     */
    async deleteTask(taskId) {
      try {
        await apiDeleteTask(taskId);

        const idx = this.tasks.findIndex(t => t.id === taskId);
        if (idx !== -1) this.tasks.splice(idx, 1);
        delete this.taskCache[taskId];

        this._updateStatusCounts(this.tasks, "pagination");
        if (this.selectedTaskId === taskId) {
          if (this.tasks.length) {
            await this.selectTask(this.tasks[Math.min(idx, this.tasks.length - 1)].id);
          } else {
            this.selectedTaskId = null;
            this.selectedTask = null;
          }
        }
      } catch (err) {
        console.error("Failed to delete task:", err);
        throw err;
      }
    },

    // -----------------------
    // Placeholders for future create/edit
    // -----------------------
    async createTask(taskData) { /* implement API call */ },
    async editTask(taskId, taskData) { /* implement API call */ },
  },
});
