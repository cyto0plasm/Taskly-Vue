// stores/projectHelpers.js

// Update a project in projects array, cache, selectedProject
export function updateProject(store, projectId, updates) {
  if (!projectId || !updates) return;

  // Selected project
  if (store.selectedProject?.id === projectId)
    store.selectedProject = { ...store.selectedProject, ...updates };

  // projects array
  const idx = store.projects.findIndex((p) => p.id === projectId);
  if (idx !== -1) {
    store.projects[idx] = { ...store.projects[idx], ...updates };
    store.projects = [...store.projects];
  }

  // cache
  if (store.projectCache[projectId])
    store.projectCache[projectId] = { ...store.projectCache[projectId], ...updates };

  // update status counts
  updateStatusCounts(store.projects, "pagination", store);
  if (store.projects?.length) updateStatusCounts(store.projects, "all", store);
}

// Update counts
export function updateStatusCounts(projects, target, store) {
  const counts = { done: 0, pending: 0, in_progress: 0 };
  projects.forEach((p) => { if (counts[p.status] !== undefined) counts[p.status]++; });
  if (target === "pagination") store.pagination.statusCounts = counts;
  else store.allStatusCounts = counts;
}

// =========================
// VALIDATION
// =========================
export function validateProject(projectData) {
  const errors = [];

  const name = projectData.name?.trim() || "";
  if (!name) errors.push("Name is required.");
  else if (name.length < 3) errors.push("Name must be at least 3 characters.");
  else if (name.length > 255) errors.push("Name cannot exceed 255 characters.");

  if (projectData.description && typeof projectData.description !== "string") {
    errors.push("Description must be a string.");
  }

  if (projectData.start_date) {
    const date = new Date(projectData.start_date);
    if (isNaN(date.getTime())) errors.push("Start date must be a valid date.");
  }

  if (projectData.end_date) {
    const date = new Date(projectData.end_date);
    if (isNaN(date.getTime())) errors.push("End date must be a valid date.");
  }

  const allowedStatuses = ["pending", "in_progress", "done"];
  if (!projectData.status) errors.push("Status is required.");
  else if (!allowedStatuses.includes(projectData.status))
    errors.push("Status must be one of: pending, in_progress, done.");

  if (projectData.creator_id && isNaN(Number(projectData.creator_id)))
    errors.push("Creator ID must be a valid number.");

  if (projectData.position && isNaN(Number(projectData.position)))
    errors.push("Position must be a valid number.");

  return errors;
}
