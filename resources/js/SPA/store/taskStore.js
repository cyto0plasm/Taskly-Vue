import { defineStore } from "pinia";
import * as TaskAPI from "../../domain/tasks/taskApi.js";
import { useFlash } from "../components/useFlash.js";
import { updateTask, updateStatusCounts, validateTask } from "./taskHelpers.js";

const { show } = useFlash();

export const useTaskStore = defineStore("task", {
    state: () => ({
        taskCache: {},
        selectedTaskId: null,
        selectedTask: null,
        selectedTaskForModal: null,
        loadingSelectedTask: false,

        tasks: [],

        pagination: {
            page: 1,
            perPage: 20,
            lastPage: 1,
            total: 0,
            statusCounts: { done: 0, pending: 0, in_progress: 0 },
        },
        allStatusCounts: { done: 0, pending: 0, in_progress: 0 },

        loading: false,
        loadingMore:false
    }),

    actions: {
        setSelectedTaskForModal(task) {
            this.selectedTaskForModal = task || null;
        },
        clearSelectedTaskForModal() {
            this.selectedTaskForModal = null;
        },

        // --------------------------
        // Load tasks with pagination
        // --------------------------
    async loadTasks(page = 1, perPage = this.pagination.perPage, replace = true) {
    // Decide whether this is a "load more" request
    const isLoadMore = !replace && page > 1;

    // Set loading flags
    if (isLoadMore) this.loadingMore = true;
    else this.loading = true;

    try {
        const res = await TaskAPI.fetchAllTasks({ page, perPage });
        const tasksData = res.data || [];
        const meta = res.meta || {};

        // Append or replace tasks
        if (replace) {
            this.tasks = tasksData;
        } else {
            this.tasks.push(...tasksData);
        }

        // Update pagination
        this.pagination.page = meta.page || page;
        this.pagination.perPage = meta.perPage || perPage;
        this.pagination.lastPage = meta.lastPage || 1;
        this.pagination.total = meta.total || this.tasks.length;
        this.pagination.hasMore = meta.hasMore ?? false;
        this.pagination.statusCounts = meta.statusCounts ?? {
            done: 0,
            pending: 0,
            in_progress: 0,
        };
        this.allStatusCounts = meta.statusCounts ?? this.allStatusCounts;
    } catch (err) {
        console.error(err);
        show("error", "Failed to load tasks");
        if (replace) this.tasks = [];
    } finally {
        this.loading = false;
        this.loadingMore = false;
    }
}

,
        // --------------------------
        // Select single task
        // --------------------------
        async selectTask(id) {
            if (!id || this.selectedTaskId === id) return;
            this.selectedTaskId = id;

            if (!this.taskCache[id]) {
                this.loadingSelectedTask = true;
                try {
                    const res = await TaskAPI.fetchTask(id);
                    if (!res.success)
                        throw new Error(res.message || "Failed to fetch task");
                    this.taskCache[id] = res.data;
                } catch (err) {
                    console.error("Failed to fetch task:", err);
                    this.selectedTaskId = null;
                    show("error", err.message || "Failed to fetch task");
                } finally {
                    this.loadingSelectedTask = false;
                }
            }

            this.selectedTask = this.taskCache[id] || null;
        },

        // --------------------------
        // Create task
        // --------------------------
        async createTask(taskData) {
            const errors = validateTask(taskData);
            if (errors.length) {
                show("error", errors.join(" | "), 3000);
                return null;
            }

            try {
                const res = await TaskAPI.createTask(taskData);
                if (!res.success)
                    throw new Error(res.message || "Failed to create task");

                const newTask = res.data;
                this.tasks.push(newTask);
                this.taskCache[newTask.id] = newTask;
                updateStatusCounts(this.tasks, "pagination", this);

                show(
                    "success",
                    res.message || "Task created successfully",
                    3000,
                    true,
                );
                return newTask;
            } catch (err) {
                console.error("Failed to create task:", err);
                show("error", err.message || "Failed to create task");
                return null;
            }
        },

        // --------------------------
        // Edit task
        // --------------------------
        async editTask(taskId, taskData) {
            if (!taskId) return null;

            const errors = validateTask(taskData);
            if (errors.length) {
                show("error", errors.join(" | "), 3000);
                return null;
            }

            try {
                const res = await TaskAPI.updateTask(taskId, taskData); // fixed function name
                if (!res.success)
                    throw new Error(res.message || "Failed to update task");

                const updatedTask = res.data;
                updateTask(this, taskId, updatedTask);
                this.taskCache[taskId] = updatedTask;
                show(
                    "success",
                    res.message || "Task updated successfully",
                    3000,
                    true,
                );
                return updatedTask;
            } catch (err) {
                console.error("Failed to edit task:", err);
                show("error", err.message || "Failed to update task");
                return null;
            }
        },

        // --------------------------
        // Delete task
        // --------------------------
        async deleteTask(taskId) {
            if (!taskId) return;

            try {
                const res = await TaskAPI.deleteTask(taskId);
                if (!res.success)
                    throw new Error(res.message || "Failed to delete task");

                this.tasks = this.tasks.filter((t) => t.id !== taskId);
                delete this.taskCache[taskId];

                if (this.selectedTaskId === taskId) {
                    this.selectedTaskId = this.tasks[0]?.id ?? null;
                    this.selectedTask =
                        this.taskCache[this.selectedTaskId] ?? null;
                }

                show("success", res.message || "Task deleted");
            } catch (err) {
                console.error("Failed to delete task:", err);
                show("error", err.message || "Failed to delete task");
            }
        },

        // --------------------------
        // Update task status
        // --------------------------
        async updateTaskStatus(taskId, status = "done") {
            if (!taskId) return;

            try {
                const res = await TaskAPI.updateTaskStatus(taskId, status);
                if (!res.success)
                    throw new Error(res.message || "Failed to update status");

                if (res.data) {
                    updateTask(this, taskId, res.data);
                    this.taskCache[taskId] = res.data;
                }

                show("success", res.message || "Status updated", 3000);
            } catch (err) {
                console.error(`Failed to update task status (${taskId}):`, err);
                show("error", err.message || "Failed to update status");
            }
        },

        // --------------------------
        // Reorder tasks
        // --------------------------
        async reorderTasks(newOrder) {
            try {
                await TaskAPI.reorderTasks(
                    newOrder.map((t, index) => ({
                        id: t.id,
                        position: index + 1,
                    })),
                );
                show("success", "Task order saved", 3000, true);
            } catch (err) {
                console.error("Failed to reorder tasks:", err);
                show("error", "Failed to save task order", 3000);
                await this.loadAllTasks();
            }
        },
    },
});
