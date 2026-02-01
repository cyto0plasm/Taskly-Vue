@extends('layout')
@section('page-name', 'Tasks')

@section('main')
    <section id="mainSection"
        class="relative flex flex-col gap-3 min-h-screen p-2
         sm:p-3 md:p-6
         lg:flex-row lg:items-start lg:gap-6">
        {{-- <x-fab></x-fab> --}}
        <x-vue.fab-vue></x-vue.fab-vue>
        <!-- Flash Message -->
        <div id="flash-message"
            class="hidden fixed top-4 left-1/2 -translate-x-1/2 px-4 py-3 rounded-lg shadow-lg text-white opacity-0 transition-opacity duration-500 z-50 max-w-[90vw] sm:max-w-md">
        </div>

        <!-- Task List -->
        <div class="w-full lg:w-auto lg:sticky lg:top-20">
            <x-vue.task-list-vue >
            </x-vue.task-list-vue>
        </div>

        <!-- Task Details -->
        <div class="w-full lg:flex-1 lg:min-w-4xl">
            {{-- Blade Rendering --}}
            {{-- <x-task-details :tasks="$tasks" :firstTask="$firstTask" :projects="$projects">
            </x-task-details> --}}

            {{-- Vue Rendering --}}
            <x-vue.task-details-vue >
            </x-vue.task-details-vue>
        </div>

        <!-- Task Models -->
        {{-- <x-task-model :projects="$projects" /> --}}
        {{-- <x-tasks.task-modal :projects="$projects" /> --}}
        {{-- <x-projects.project-modal :projects="$projects" /> --}}
        {{-- <x-edit-task :projects="$projects" :tasks="$tasks" /> --}}
    </section>

    <!-- Drawer Canvas -->
    <x-drawer-canvas />
@endsection
<!-- Utils and State -->
{{-- @vite(['resources/js/utils/domHelpers.js', 'resources/js/utils/flash.js', 'resources/js/utils/tasks/TaskState.js', 'resources/js/data/tasks/TaskAPI.js']) --}}

<!-- Controllers & UI -->
@vite([
    // 'resources/js/tasks/main_Task_Entry.js',
    // 'resources/js/tasks/switchTask.js',
    // 'resources/js/tasks/taskDelete.js',
    // 'resources/js/tasks/updateStatus.js',
    // 'resources/js/tasks/taskEditUI.js',
    // 'resources/js/tasks/sortableTasks.js',
    // 'resources/js/tasks/taskDescriptionToggle.js',
    // 'resources\css\taskList.css',
])
