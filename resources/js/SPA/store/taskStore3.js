// // stores/taskStore.js
// import { defineStore } from "pinia";
// import { shallowRef } from "vue";
// import * as TaskAPI from "../../domain/tasks/TaskAPI.js";
// import { useFlash  } from "../components/useFlash.js";
// import { updateTask, updateStatusCounts, validateTask } from "./taskHelpers.js";
// const { flash, show } = useFlash();

// export const useTaskStore = defineStore("task", {
//   id: "task",

//   state: () => ({
//     tasks: shallowRef([]),
//     allTasks: shallowRef([]),
//     taskCache: {},
//     selectedTask: null,
//     selectedTaskId: null,
//     selectedTaskForModal: null,
//     loading: false,
//     loadingSelectedTask: false,
//     pagination: {
//       page: 1,
//       lastPage: 1,
//       total: 0,
//       statusCounts: { done: 0, pending: 0, in_progress: 0 },
//     },
//     allStatusCounts: { done: 0, pending: 0, in_progress: 0 },
//   }),

//   actions: {
//     // Modal
//     setSelectedTaskForModal(task) { this.selectedTaskForModal = task; },
//     clearSelectedTaskForModal() { this.selectedTaskForModal = null; },

//     // --------------------------
//     // Load paginated tasks only
//     // --------------------------
//     async loadPaginatedTasks(page = 1) {
//       this.loading = true;
//       try {
//         const res = await TaskAPI.fetchAllTasksVue({ page, showAll: false });
//         const tasks = Array.isArray(res.data) ? res.data : [];
//         this.tasks = tasks;
//         this.pagination = {
//           ...this.pagination,
//           page: res.current_page ?? 1,
//           lastPage: res.last_page ?? 1,
//           total: res.total ?? tasks.length,
//         };
//         updateStatusCounts(tasks, "pagination", this);

//         // load all tasks in background if not yet loaded
//         if (!this.allTasks.length) this.loadAllTasks();
//       } catch (err) {
//         console.error("Failed to load paginated tasks:", err);
//         this.tasks = [];
//         updateStatusCounts([], "pagination", this);
//       } finally {
//         this.loading = false;
//       }
//     },

//     // --------------------------
//     // Load all tasks
//     // --------------------------
//     async loadAllTasks() {
//       try {
//         const res = await TaskAPI.fetchAllTasksVue({ showAll: true });
//         const allTasks = Array.isArray(res.data) ? res.data : [];
//         this.allTasks = allTasks;

//         // if tasks not loaded yet, show all as tasks
//         if (!this.tasks.length) this.tasks = allTasks;

//         updateStatusCounts(allTasks, "all", this);
//       } catch (err) {
//         console.error("Failed to load all tasks:", err);
//         this.allTasks = [];
//         updateStatusCounts([], "all", this);
//       }
//     },

//     // --------------------------
//     // Select Task
//     // --------------------------
//     async selectTask(id) {
//       if (!id || this.selectedTaskId === id) return;
//       this.selectedTaskId = id;

//       if (!this.taskCache[id]) {
//         this.loadingSelectedTask = true;
//         try {
//           this.taskCache[id] = await TaskAPI.fetchTask(id);
//         } catch (err) {
//           console.error("Failed to fetch task:", err);
//           this.selectedTaskId = null;
//           show("error", "Failed to fetch task");
//         } finally {
//           this.loadingSelectedTask = false;
//         }
//       }

//       this.selectedTask = this.taskCache[id] || null;
//     },

//     // Update Status
//     async updateTaskStatus(taskId, newStatus) {
//       if (!taskId || !newStatus) return;
//       try {
//         if (newStatus === "done") await TaskAPI.markAsCompleteTask(taskId);
//         updateTask(this, taskId, { status: newStatus });
//         show("success", "Task status updated", 3000);
//       } catch (err) {
//         console.error(`Failed to update status for ${taskId}`, err);
//         show("error", "Failed to update task status");
//       }
//     },

//     // CRUD operations (create/edit/delete/reorder)
//    async createTask(taskData) {
//   const errors = validateTask(taskData);
//   if (errors.length) {
//     show("error", errors.join(" | "), 3000);
//     return null;
//   }

//   try {
//     const formData = new FormData();
//     Object.entries({
//       title: taskData.title,
//       description: taskData.description || "",
//       due_date: taskData.due_date || "",
//       priority: taskData.priority,
//       status: taskData.status,
//       project_id: taskData.project_id,
//     }).forEach(([key, val]) => val != null && formData.append(key, val));

//     const res = await TaskAPI.createTask(formData);
//     const newTask = res.task;
//     this.tasks.push(newTask);
//     this.allTasks.push(newTask);
//     this.taskCache[newTask.id] = newTask;
//     show("success", "Task created successfully", 3000, true);
//     return newTask;
//   } catch (err) {
//     console.error("Failed to create task:", err);
//     show(
//       "error",
//       err?.response?.data?.errors
//         ? Object.values(err.response.data.errors).flat().join(" | ")
//         : err.message || "Failed to create task"
//     );
//     return null;
//   }
// },

// async editTask(taskId, taskData) {
//   if (!taskId || !taskData) return null;

//   const errors = validateTask(taskData);
//   if (errors.length) {
//     show("error", errors.join(" | "), 3000);
//     return null;
//   }

//   try {
//     const payload = {
//       title: taskData.title,
//       description: taskData.description || "",
//       due_date: taskData.due_date || null,
//       priority: taskData.priority,
//       status: taskData.status,
//       project_id: taskData.project_id || null,
//       color: taskData.color || null,
//     };
//     const res = await TaskAPI.UpdateTask(taskId, payload);
//     updateTask(this, taskId, res.task);
//     show("success", "Task updated successfully", 3000, true);
//     return res.task;
//   } catch (err) {
//     console.error("Failed to edit task:", err);
//     show(
//       "error",
//       err?.response?.data?.errors
//         ? Object.values(err.response.data.errors).flat().join(" | ")
//         : err.message || "Failed to update task"
//     );
//     return null;
//   }
// }
// ,
//     async deleteTask(taskId) {
//       if (!taskId) return;
//       try {
//         await TaskAPI.deleteTask(taskId);
//         this.tasks = this.tasks.filter((t) => t.id !== taskId);
//         delete this.taskCache[taskId];
//         updateStatusCounts(this.tasks, "pagination", this);

//         if (this.selectedTaskId === taskId) {
//           if (this.tasks.length) await this.selectTask(this.tasks[0].id);
//           else { this.selectedTaskId = null; this.selectedTask = null; }
//         }

//         show("success", "Task deleted");
//       } catch (err) {
//         console.error("Failed to delete task:", err);
//         show("error", "Failed to delete task");
//       }
//     },

//     async reorderTasks(newOrder) {
//       try {
//         await TaskAPI.reorderTasks(newOrder.map((t) => ({ id: t.id, position: t.position })));
//         show("success", "Task order saved");
//       } catch (err) {
//         console.error("Failed to reorder tasks:", err);
//         show("error", "Failed to save task order");
//         await this.loadAllTasks();
//       }
//     },
//   },
// });
