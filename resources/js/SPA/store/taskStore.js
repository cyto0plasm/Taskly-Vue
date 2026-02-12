import { defineStore } from "pinia";
import * as TaskAPI from "../../domain/tasks/taskApi.js";
import { useFlash } from "../components/useFlash.js";
import { updateTask, updateStatusCounts, validateTask } from "./taskHelpers.js";
import { useProjectStore } from "./projectStore.js";

const { show } = useFlash();

export const useTaskStore = defineStore("task", {
    state: () => ({
        taskCache: {},
        selectedTaskId: null,
        selectedTask: null,
        deletingTaskIds: new Set(),
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
        //filter state
         filters: {
             priority: null,
        status: null,
        project_id: null,
        has_project: null,
        due: null,
        from: null,
        to: null,
        search: null,
    },
    //loading state
        loading: true,
        softLoading: false,
        //pagination mode
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
    async loadTasks(page = 1, perPage = this.pagination.perPage, replace = true, { useSoftLoading = false } = {}) {
    // Decide whether this is a "load more" request
    const isLoadMore = !replace && page > 1;

    // Determine which loading flag to use
    const loadingFlag = useSoftLoading
        ? "softLoading"
        : isLoadMore
        ? "loadingMore"
        : "loading";

    this[loadingFlag] = true;

    try {
        const res = await TaskAPI.fetchAllTasks({
            page,
            perPage,
            ...this.getApiFilters(),
        });
        const tasksData = res.data || [];
        const meta = res.meta || {};

        // Replace or append
        if (replace) this.tasks = tasksData;
        else this.tasks.push(...tasksData);

        // Update pagination
        this.pagination.page = meta.page || page;
        this.pagination.perPage = meta.perPage || perPage;
        this.pagination.lastPage = meta.lastPage || 1;
        this.pagination.total = typeof meta.total === "number" ? meta.total : this.tasks.length;
        this.pagination.hasMore = meta.hasMore ?? false;
        this.pagination.statusCounts = meta.statusCounts ?? { done: 0, pending: 0, in_progress: 0 };
        this.allStatusCounts = meta.allStatusCounts ?? this.allStatusCounts;

    } catch (err) {
        console.error(err);
        show("error", "Failed to load tasks");
        if (replace) this.tasks = [];
    } finally {
        // Only reset the flag you set
        this[loadingFlag] = false;
    }
}


,
setFilters(newFilters) {
    this.filters = {
        ...this.filters,
        ...newFilters,
    };

    // reset pagination when filters change
    this.pagination.page = 1;

    // reload tasks with new filters
    this.loadTasks(1, this.pagination.perPage, true);
}

,clearFilters() {
    Object.keys(this.filters).forEach(k => this.filters[k] = null);
    this.loadTasks(1, this.pagination.perPage, true);
}
,
getApiFilters() {
    const f = {};

    // status
    if (this.filters.status) {
        f.status = this.filters.status;
    }

    // ⬅️ ADD PRIORITY FILTER
    if (this.filters.priority) {
        f.priority = this.filters.priority;
    }

    // project_id
    if (this.filters.project_id) {
        f.project_id = this.filters.project_id;
    }

    // has_project → must be boolean (Laravel-safe)
    if (this.filters.has_project !== null) {
        f.has_project = this.filters.has_project ? 1 : 0;
    }

    // due → ONLY allowed enum (⬅️ ADD 'this_week' to array)
    if (['today', 'overdue', 'upcoming', 'this_week'].includes(this.filters.due)) {
        f.due = this.filters.due;
    }

    // date range
    if (this.filters.from) {
        f.from = this.filters.from;
    }

    if (this.filters.to) {
        f.to = this.filters.to;
    }

    // search
    if (this.filters.search) {
        f.search = this.filters.search;
    }

    return f;
},

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


                // ✅ UPDATE PROJECT CACHE IF TASK IS ASSIGNED TO A PROJECT
       if (newTask.project_id) {
    const projectStore = useProjectStore();

    // Update cached project (this automatically updates selectedProject since it's the same reference)
    const cachedProject = projectStore.projectCache[newTask.project_id];
    if (cachedProject) {
        if (!cachedProject.tasks) {
            cachedProject.tasks = [];
        }
        cachedProject.tasks.push(newTask);
    }
}


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

  if (this.deletingTaskIds.has(taskId)) return;
  this.deletingTaskIds.add(taskId);

  try {
    const res = await TaskAPI.deleteTask(taskId);
    if (!res.success)
      throw new Error(res.message || "Failed to delete task");

    this.tasks = this.tasks.filter(t => t.id !== taskId);
    delete this.taskCache[taskId];

    if (this.selectedTaskId === taskId) {
      this.selectedTaskId = this.tasks[0]?.id ?? null;
      this.selectedTask = this.taskCache[this.selectedTaskId] ?? null;
    }

    show("success", res.message || "Task deleted");
  } catch (err) {
    console.error(err);
    show("error", err.message || "Failed to delete task");
  } finally {
    this.deletingTaskIds.delete(taskId);
  }
}
,

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
                show("success", "Task order saved", 3000);
            } catch (err) {
                console.error("Failed to reorder tasks:", err);
                show("error", "Failed to save task order", 3000);
                await this.loadAllTasks();
            }
        },
    },
});
