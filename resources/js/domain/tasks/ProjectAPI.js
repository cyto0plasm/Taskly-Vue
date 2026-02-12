import { apiRequest, postRequest, patchRequest, deleteRequest, getRequest } from "../../utils/apiHelpers.js"; // new axios helper

/* ---------- READS ---------- */
export const fetchAllProjects = ({
  page = 1,
  perPage = 20,

  status = null,
  project_id = null,
  has_project = null,
  due = null,
  from = null,
  to = null,
  search = null,
} = {}) => {
  return getRequest("/api/projects", {
    page,
    perPage,
    status,
    project_id,
    has_project,
    due,
    from,
    to,
    search,
  });
};



export const fetchProject = (id) => getRequest(`/api/projects/${id}`);

/* ---------- WRITES ---------- */
export const createProject = (data) => postRequest("/api/projects", data);

export const updateProject = (id, data) => patchRequest(`/api/projects/${id}`, data);

export const deleteProject = (id) => deleteRequest(`/api/projects/${id}`);

export const updateProjectStatus = (id, status) => {
  if (!["pending", "in_progress", "done"].includes(status)) {
    throw new Error(`Invalid status: ${status}`);
  }
  return patchRequest(`/api/projects/${id}/status`, { status });
};

export const reorderProjects = (order) => {
  if (!Array.isArray(order) || !order.length) return Promise.resolve();
  return postRequest("/api/projects/reorder", { order });
};
