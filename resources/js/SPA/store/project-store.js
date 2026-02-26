import { defineStore } from "pinia";
import * as ProjectAPI from "../../domain/tasks/project-api.js";
import { useFlash } from "../components/useFlash.js";
import { updateProject, updateStatusCounts, validateProject } from "./project-helper.js";
import { useTaskStore } from "./task-store.js";

const { show } = useFlash();

export const useProjectStore = defineStore("project", {
  state: () => ({
    projectCache: {},
    selectedProjectId: null,
    selectedProject: null,
    deletingProjectIds: new Set(),
    selectedProjectForModal: null,
    loadingSelectedProject: false,

    projects: [],
    allProjects: [],

    pagination: {
      page: 1,
      perPage: 20,
      lastPage: 1,
      total: 0,
      hasMore: false,
      statusCounts: { done: 0, pending: 0, in_progress: 0 },
    },
    allStatusCounts: { done: 0, pending: 0, in_progress: 0 },

    // filters
    filters: {
      status: null,
      from: null,
      to: null,
      search: null,
    },

    // loading states
    loading: true,
    softLoading: false,
    loadingMore: false,
  }),
    getters: {
     //  Get any project with its tasks
    getProjectWithTasks: (state) => {
      return (projectId) => {
        const project = state.projectCache[projectId];
        if (!project) return null;

        // Get taskStore
        const taskStore = useTaskStore();

        // Look up each task by ID
        const tasks = (project.taskIds || [])
          .map(taskId => taskStore.taskCache[taskId]?.data)
          .filter(Boolean); // Remove null/undefined

        // Return project with tasks attached
        return {
          ...project,
          tasks  // Full task objects (computed on-the-fly)
        };
      };
    },
    // Get the currently selected project with tasks
    selectedProjectWithTasks: (state) => {
      if (!state.selectedProject) return null;

      const taskStore = useTaskStore();

      const tasks = (state.selectedProject.taskIds || [])
        .map(taskId => taskStore.taskCache[taskId]?.data)
        .filter(Boolean);

      return {
        ...state.selectedProject,
        tasks
      };
    }

  },

  actions: {
    setSelectedProjectForModal(project) {
      this.selectedProjectForModal = project || null;
    },

    clearSelectedProjectForModal() {
      this.selectedProjectForModal = null;
    },

    // --------------------------
    // Load projects with pagination
    // --------------------------
    async loadProjects(page = 1, perPage = this.pagination.perPage, replace = true, { useSoftLoading = false } = {}) {
      const isLoadMore = !replace && page > 1;
      const loadingFlag = useSoftLoading ? "softLoading" : isLoadMore ? "loadingMore" : "loading";
      this[loadingFlag] = true;

      try {
        const res = await ProjectAPI.fetchAllProjects({
          page,
          perPage,
          ...this.getApiFilters(),
        });

        const projectsData = res.data || [];
        const meta = res.meta || {};

        if (replace) this.projects = projectsData;
        else this.projects.push(...projectsData);

        // Update pagination
        this.pagination.page = meta.page || page;
        this.pagination.perPage = meta.perPage || perPage;
        this.pagination.lastPage = meta.lastPage || 1;
        this.pagination.total = typeof meta.total === "number" ? meta.total : this.projects.length;
        this.pagination.hasMore = meta.hasMore ?? false;
        this.pagination.statusCounts = meta.statusCounts ?? { done: 0, pending: 0, in_progress: 0 };
        this.allStatusCounts = meta.allStatusCounts ?? this.allStatusCounts;


      } catch (err) {
        console.error(err);
        show("error", "Failed to load projects");
        if (replace) this.projects = [];
      } finally {
        this[loadingFlag] = false;
      }
    },async loadAllProjects() {
  try {
    const res = await ProjectAPI.fetchAllProjects({
      page: 1,
      perPage: 100,
    });

    this.allProjects = res.data || [];
  } catch (err) {
    console.error(err);
  }
},

    setFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters };
      this.pagination.page = 1;
      this.loadProjects(1, this.pagination.perPage, true);
    },

    clearFilters() {
      Object.keys(this.filters).forEach(k => this.filters[k] = null);
      this.loadProjects(1, this.pagination.perPage, true);
    },

    getApiFilters() {
      const f = {};
      if (this.filters.status) f.status = this.filters.status;
      if (this.filters.from) f.from = this.filters.from;
      if (this.filters.to) f.to = this.filters.to;
      if (this.filters.search) f.search = this.filters.search;
      return f;
    },

    // --------------------------
    // Select single project
    // --------------------------
    async selectProject(id) {
  if (!id || this.selectedProjectId === id) return;
  this.selectedProjectId = id;

  if (!this.projectCache[id]) {
    this.loadingSelectedProject = true;
    try {
      const res = await ProjectAPI.fetchProject(id);
      if (!res.success) throw new Error(res.message || "Failed to fetch project");

      const project = res.data;

      // ✅ Normalize: Convert tasks array to taskIds and cache tasks
      if (project.tasks && Array.isArray(project.tasks)) {
        const taskStore = useTaskStore();

        // Store each task in taskCache
        project.tasks.forEach(task => {
          taskStore.taskCache[task.id] = {
            data: task,
            timestamp: Date.now()
          };
        });

        // Convert to taskIds
        project.taskIds = project.tasks.map(t => t.id);

        // Remove the tasks array to save memory
        delete project.tasks;
      }

      this.projectCache[id] = project;
    } catch (err) {
      console.error("Failed to fetch project:", err);
      this.selectedProjectId = null;
      show("error", err.message || "Failed to fetch project");
    } finally {
      this.loadingSelectedProject = false;
    }
  }

  this.selectedProject = this.projectCache[id] || null;
},

    // --------------------------
    // Create project
    // --------------------------
    async createProject(projectData) {
      const errors = validateProject(projectData);
      if (errors.length) {
        show("error", errors.join(" | "), 3000);
        return null;
      }

      try {
        const res = await ProjectAPI.createProject(projectData);
        if (!res.success) throw new Error(res.message || "Failed to create project");

        const newProject = res.data;
        this.projects.push(newProject);
        this.projectCache[newProject.id] = newProject;
        updateStatusCounts(this.projects, "pagination", this);
        show("success", res.message || "Project created successfully", 3000, true);

        return newProject;
      } catch (err) {
        console.error("Failed to create project:", err);
        show("error", err.message || "Failed to create project");
        return null;
      }
    },

    // --------------------------
    // Edit project
    // --------------------------
    async editProject(projectId, projectData) {
      if (!projectId) return null;
      const errors = validateProject(projectData);
      if (errors.length) {
        show("error", errors.join(" | "), 3000);
        return null;
      }

      try {
        const res = await ProjectAPI.updateProject(projectId, projectData);
        if (!res.success) throw new Error(res.message || "Failed to update project");

        const updatedProject = res.data;
        updateProject(this, projectId, updatedProject);
        this.projectCache[projectId] = updatedProject;

        show("success", res.message || "Project updated successfully", 3000, true);
        return updatedProject;
      } catch (err) {
        console.error("Failed to edit project:", err);
        show("error", err.message || "Failed to update project");
        return null;
      }
    },

    // --------------------------
    // Delete project
    // --------------------------
    async deleteProject(projectId) {
      if (!projectId || this.deletingProjectIds.has(projectId)) return;
      this.deletingProjectIds.add(projectId);

      try {
        const res = await ProjectAPI.deleteProject(projectId);
        if (!res.success) throw new Error(res.message || "Failed to delete project");

        this.projects = this.projects.filter(t => t.id !== projectId);
        delete this.projectCache[projectId];

        if (this.selectedProjectId === projectId) {
          this.selectedProjectId = this.projects[0]?.id ?? null;
          this.selectedProject = this.projectCache[this.selectedProjectId] ?? null;
        }

        show("success", res.message || "Project deleted");
      } catch (err) {
        console.error(err);
        show("error", err.message || "Failed to delete project");
      } finally {
        this.deletingProjectIds.delete(projectId);
      }
    },

    // --------------------------
    // Update project status
    // --------------------------
    async updateProjectStatus(projectId, status = "done") {
      if (!projectId) return;

      try {
        const res = await ProjectAPI.updateProjectStatus(projectId, status);
        if (!res.success) throw new Error(res.message || "Failed to update status");

        if (res.data) {
          updateProject(this, projectId, res.data);
          this.projectCache[projectId] = res.data;
        }

        show("success", res.message || "Status updated", 3000);
      } catch (err) {
        console.error(`Failed to update project status (${projectId}):`, err);
        show("error", err.message || "Failed to update status");
      }
    },

    // --------------------------
    // Reorder projects
    // --------------------------
    async reorderProjects(newOrder) {
      try {
        await ProjectAPI.reorderProjects(
          newOrder.map((p, index) => ({ id: p.id, position: index + 1 }))
        );
        show("success", "Project order saved", 3000);
      } catch (err) {
        console.error("Failed to reorder projects:", err);
        show("error", "Failed to save project order", 3000);
        await this.loadProjects();
      }
    },
    async searchProjects(query) {
    try {
      if (!query) {
        this.allProjects = [];
        return;
      }

      const res = await ProjectAPI.searchProjects({ query });
      if (!res.success) throw new Error(res.message || "Failed to search projects");

      this.allProjects = res.data || [];
    } catch (err) {
      console.error("Project search failed:", err);
      this.allProjects = [];
    }
  },
  },

});
