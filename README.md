# 🗂️ Taskly — Task Management Web App

> A full-stack task management application built with **Laravel** and **Vue.js**

![Start](https://img.shields.io/badge/Start-01/02/2026-4CAF50?style=for-the-badge)
![End](https://img.shields.io/badge/End-05/03/2026-F44336?style=for-the-badge)
<br/>

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Pinia](https://img.shields.io/badge/Pinia-FFD859?style=for-the-badge&logo=vue.js&logoColor=black)
![Railway](https://img.shields.io/badge/Railway-0B0D0E?style=for-the-badge&logo=railway&logoColor=white)

**🔗 Live Demo:** [taskly.up.railway.app](https://illustrious-gentleness-production-8b3f.up.railway.app/)

---

## Overview

Taskly is a decoupled web application that separates concerns between a **Laravel RESTful API backend** and a **reactive Vue.js frontend**. Users can manage tasks through structured list and calendar views, assign statuses, sort and filter across projects — all with real-time state updates powered by Pinia.

---

## Application Preview

### Dashboard
Displays tasks in a structured list format with create, update, delete, and drag-to-sort functionality.
![Dashboard](public/images/Dashboard.png)

### List View
Displays tasks in a structured list format with create, update, delete, and drag-to-sort functionality.
![List View](public/images/taskApp2.png)

### Canvas
Display Entity Canvas drawing, text, options, styles and more like Microsoft Word.
![Canvas](public/images/canvas.png)

### Calendar View *(Upcoming)*
Visualizes tasks on a calendar for date-based planning and scheduling.

### Task Status Management *(Upcoming)*
Assign and update task statuses to track progress through workflow stages.

---

## Key Features

- ✅ Full CRUD for tasks and projects
- 📋 List view with drag-to-sort (Sortable.js)
- 📅 Calendar view for date-based task planning
- 🔄 Task status assignment and updates
- 🔍 Sorting and filtering
- ⚡ Reactive state management with Pinia
- 🔐 Authentication with protected routes
- 🏗️ Service layer for centralized, reusable queries
- 🌐 RESTful API with hybrid SSR + CSR rendering

---

## Architecture

The app uses a **hybrid rendering approach**: page shells are served via Laravel SSR (Blade), while task and project entities are managed client-side by Vue CSR.

### Request Lifecycle

```
User Action
  → Pinia Store dispatches via apiRequest.js (Axios wrapper)
    → Laravel Auth + Middleware validates request
      → Controller delegates to Service layer
        → Eloquent queries MySQL
          → JSON response returned
            → Pinia updates state → Vue re-renders UI
```

### Project Structure

| Layer | Files |
|---|---|
| Models | `Task`, `Project` |
| Services | `TaskService`, `ProjectService` — centralized Eloquent queries |
| API Controllers | `TaskControllerApiVue`, `ProjectControllerApiVue` — Vue-facing routes |
| SSR Controllers | `TaskController`, `ProjectController` — Blade page rendering |

---

## Authentication

Built on **Laravel Breeze** with custom UI:

- Custom login and registration pages
- Password reset via email
- Profile management (name, email, password)
- All task/project routes protected by auth middleware

---

## Deployment

Hosted on **Railway**

![Railway](https://img.shields.io/badge/Deployed_on-Railway-0B0D0E?style=for-the-badge&logo=railway&logoColor=white)
