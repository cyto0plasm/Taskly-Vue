@props([
    'projects' => [],
    'task' => null,
    'taskAction' => route('tasks.store'),
    'projectAction' => route('projects.store'),
])
@php
    $projects = collect($projects);
    $projectOptions = $projects->pluck('name', 'id')->toArray();

@endphp


<x-modal id="task-modal" title="{{ $task ? 'Edit Task' : 'Create Task' }}"
    description=" {{ $task ? 'Update your task details' : 'Add a new task to your project' }}" gradientFrom="#10B981"
    gradientTo="#34c796" :sidebar="view('components.tasks.task-project-sidebar', ['projects' => $projects, 'task' => $task])">

    <form id="task-modal-form" action="{{ $taskAction }}" method="POST" class="space-y-4">
        @csrf
        @if ($task)
            @method('PATCH')
        @endif

        <x-form.form-input label="Title" name="title" placeholder="Enter task title" value="{{ $task->title ?? '' }}"
            required />
        <x-form.form-textarea label="Description" name="description" placeholder="Enter task description"
            value="{{ $task->description ?? '' }}" />
        <x-form.form-input label="Due Date" type="date" name="due_date"
            value="{{ $task?->due_date?->format('Y-m-d') }}" />
        <x-form.form-select label="Priority" name="priority" :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']" :selected="$task->priority ?? 'medium'" />
        <x-form.form-select label="Status" name="status" :options="['pending' => 'Pending', 'in_progress' => 'In Progress', 'done' => 'Done']" :selected="$task->status ?? 'pending'" />

        {{-- Desktop sidebar (hidden on mobile) --}}
        <div class="hidden lg:block">
            <input type="hidden" name="project_id" value="{{ $task->project_id ?? '' }}">
            <x-tasks.project-selector :projects="$projects" form-id="task-modal-form" />
        </div>

        {{-- Mobile project picker (visible only on mobile, at bottom of form) --}}
        <div class="lg:hidden">
            <x-tasks.project-selector :projects="$projects" form-id="task-modal-form" />
        </div>

        {{-- Action buttons --}}
        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-gray-200 mt-6">
            <button type="button" data-modal-close="task-modal"
                class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all">
                Cancel
            </button>
            <button type="submit"
                class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-[#10B981] text-white rounded-lg hover:bg-[#0ea472] active:bg-[#0d9268] font-medium transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                {{ $task ? 'Update Task' : 'Create Task' }}
            </button>
        </div>
    </form>
</x-modal>

@vite(['resources/js/utils/animatedColoredBorder.js', 'resources/css/animatedColoredBorder.css'])
