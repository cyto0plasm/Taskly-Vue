# Task Management Web Application 
 
A full-stack task management web application built with **Laravel** for the backend and **Vue.js** for the frontend.  
The application enables structured task organization through list and calendar views, task statuses, and sorting capabilities.

---

## Overview

This project is designed to help users manage tasks efficiently by offering multible operations such as CRUDS, Sorting, Filtering .  
It follows a decoupled architecture with a RESTful backend API and a modern frontend consuming it.

## how it works
# User Auth
for user authentication and permissions i used breeze with a custom Login-Register Page and other user specific operations like resiting password or updating profile...

# Main Models(Entities)
Task - Project -> Models
TaskService, ProjectService -> building a centeralized Queries for models 
TaskControllerApiVue, ProjectControllerApiVue -> managing api routes for vue 
TaskController, ProjectController -> the application was based on SSR(Server Side Rendering) but i decided to split loading pages to ssr and managing Entities handled by vue's CSR(Client Side Rendering).
# Request LifeCycle
User action → (pinia)Store calls api via apiRequest.js → auth and other middlewares catch request → Backend controller handles request → Database(MySql) → Backend sends JSON → Store updates UI/State

---

## Application Preview

This section provides visual previews and demonstrations of the application's core functionality, including task creation, task views, and status management.

### List View
Displays tasks in a structured list format, allowing users to create, update, delete, and sort tasks efficiently.

![List View Preview](previews/list-view.png)

### Calendar View
Provides a calendar-based visualization of tasks, enabling users to plan and track tasks based on scheduled dates.

![Calendar View Preview](previews/calendar-view.png)

### Task Status Management
Demonstrates assigning and updating task statuses to reflect progress.

![Task Status Preview](previews/task-status.png)

---

## Key Features

- Full CRUD operations for tasks
- Task sorting  
- List view for structured task management
- Calendar view for visual task scheduling(upComming)
- Task status assignment and updates
- Clean and scalable frontend architecture
- RESTful API design
- caching and state management 

---

## Technology Stack

### Backend
- Laravel
- RESTful API
- MySQL 
- Eloquent ORM

### Frontend
- Vue.js
- pinia
- sortable.js
- Composition API
- custom apiHelper.js util for API communication + axios
- Tailwind CSS for styling

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
  </a>
  &nbsp;&nbsp;&nbsp; <!-- space between logos -->
  <a href="https://vuejs.org" target="_blank">
    <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Vue.js_Logo_2.svg" width="100" height=""50 alt="Vue 3 Logo">
  </a>
</p>



