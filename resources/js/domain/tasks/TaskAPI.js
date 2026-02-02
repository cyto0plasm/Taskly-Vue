// domain/tasks/taskApi.js
import { apiRequest, patchRequest, deleteRequest } from "../../utils/apiHelpers.js";

const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "";

/* ---------- READS ---------- */

/**
 * Fetch a single task by ID
 */
export const fetchTask = async (id) => {
  const res = await apiRequest(`/tasks/show/${id}`);
  return res; // res should be the task object
};

/**
 * Fetch paginated tasks for Vue store
 * @param {Object} options - { showAll: boolean, page: number }
 * @returns {Object} { data: tasks[], current_page, last_page, total }
 */
export const fetchAllTasksVue = async ({ showAll = false, page = 1 } = {}) => {
  const res = await apiRequest(`/tasks/json/all?showAll=${showAll ? 1 : 0}&page=${page}`);
  return res;
};

/**
 * Fetch all tasks (for total counts / show all)
 * @returns {Array} tasks
 */
export const fetchAllTasks = async () => {
  const res = await apiRequest("/tasks/json/all");
  return Array.isArray(res.data) ? res.data : [];
};

/* ---------- WRITES ---------- */

/**
 * Create a new task
 * @param {FormData} formData
 */
export const createTask = async (formData) => {
  return apiRequest("/tasks/store", {
    method: "POST",
    headers: { "X-CSRF-TOKEN": csrf },
    body: formData,
  });
};

/**
 * Update a task
 * @param {number} id
 * @param {FormData} formData
 */
export const UpdateTask = async (id, data) => {
    return patchRequest(`/tasks/update/${id}`, data);
};


/**
 * Delete a task
 * @param {number} id
 */
export const deleteTask = async (id) => deleteRequest(`/tasks/delete/${id}`, csrf);

/**
 * Mark a task as complete
 * @param {number} id
 */
export const markAsCompleteTask = async (id) =>
  patchRequest(`/tasks/status-update/${id}`, { status: "done" }, csrf);

/**
 * Reorder tasks
 * @param {Array} order - [{ id, position }]
 */
export const reorderTasks = async (order) =>
  apiRequest("/tasks/reorder", {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": csrf,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ order }),
  });
