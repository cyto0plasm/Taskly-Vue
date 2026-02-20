import { apiRequest, postRequest, patchRequest, deleteRequest, getRequest } from "../../utils/apiHelpers.js"; // new axios helper

/* ---------- READS ---------- */
// taskApi.js
export const fetchAllTasks = ({
  page = 1,
  perPage = 20,
  status = null,
  priority = null,
  project_id = null,
  has_project = null,
  due = null,
  from = null,
  to = null,
  search = null,
  fields = 'id,title,status,created_at',
   include_project = false,
  signal = null,
} = {}) => {
  // Build params object
  const params = {
    page,
    perPage,
    status,
    priority,
    project_id,
    has_project,
    due,
    from,
    to,
    search,
  };

  // Only add fields if provided
  if (fields) {
    params.fields = fields;
  }
  if (include_project) {
    params.include_project = 1;
  }

  return getRequest("/api/tasks", params, { signal });
};



export const fetchTask = (id) => getRequest(`/api/tasks/${id}`);

/* ---------- WRITES ---------- */
export const createTask = (data) => postRequest("/api/tasks", data);

export const updateTask = (id, data) => patchRequest(`/api/tasks/${id}`, data);

export const deleteTask = (id) => deleteRequest(`/api/tasks/${id}`);

export const updateTaskStatus = (id, status) => {
  if (!["pending", "in_progress", "done"].includes(status)) {
    throw new Error(`Invalid status: ${status}`);
  }
  return patchRequest(`/api/tasks/${id}/status`, { status });
};

export const reorderTasks = (order) => {
  if (!Array.isArray(order) || !order.length) return Promise.resolve();
  return postRequest("/api/tasks/reorder", { order });
};
