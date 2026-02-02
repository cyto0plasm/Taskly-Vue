// stores/taskHelpers.js

// Update a task in tasks, allTasks, cache, selectedTask
export function updateTask(store, taskId, updates) {
  if (!taskId || !updates) return;

  // Selected task
  if (store.selectedTask?.id === taskId) store.selectedTask = { ...store.selectedTask, ...updates };

  // tasks array
  const idx = store.tasks.findIndex((t) => t.id === taskId);
  if (idx !== -1) {
    store.tasks[idx] = { ...store.tasks[idx], ...updates };
    store.tasks = [...store.tasks];
  }

  // cache
  if (store.taskCache[taskId]) store.taskCache[taskId] = { ...store.taskCache[taskId], ...updates };

  // allTasks
  if (store.allTasks?.length)
    store.allTasks = store.allTasks.map((t) => t.id === taskId ? { ...t, ...updates } : t);

  // status counts
  updateStatusCounts(store.tasks, "pagination", store);
  if (store.allTasks?.length) updateStatusCounts(store.allTasks, "all", store);
}

// Update counts
export function updateStatusCounts(tasks, target, store) {
  const counts = { done: 0, pending: 0, in_progress: 0 };
  tasks.forEach((t) => { if (counts[t.status] !== undefined) counts[t.status]++; });
  if (target === "pagination") store.pagination.statusCounts = counts;
  else store.allStatusCounts = counts;
}

// =========================
// VALIDATION (Option 1: throw error)
// =========================
export function validateTask(taskData) {
  const errors = [];

  const title = taskData.title?.trim() || "";
  if (!title) errors.push("Title is required.");
  else if (title.length < 3) errors.push("Title must be at least 3 characters.");
  else if (title.length > 255) errors.push("Title cannot exceed 255 characters.");

  if (taskData.description && typeof taskData.description !== "string") {
    errors.push("Description must be a string.");
  }

  if (taskData.due_date) {
    const date = new Date(taskData.due_date);
    if (isNaN(date.getTime())) errors.push("Due date must be a valid date.");
  }

  const allowedPriorities = ["low", "medium", "high"];
  if (!taskData.priority) errors.push("Priority is required.");
  else if (!allowedPriorities.includes(taskData.priority))
    errors.push("Priority must be one of: low, medium, high.");

  const allowedStatuses = ["pending", "in_progress", "done"];
  if (!taskData.status) errors.push("Status is required.");
  else if (!allowedStatuses.includes(taskData.status))
    errors.push("Status must be one of: pending, in_progress, done.");

  if (taskData.project_id && isNaN(Number(taskData.project_id)))
    errors.push("Project ID must be a valid number.");

  return errors;
}
