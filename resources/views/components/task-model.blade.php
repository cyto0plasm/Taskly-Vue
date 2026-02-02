{{-- resources/views/components/create-modal.blade.php --}}
@props([
    'projects' => [],
    'task' => null,
    'taskAction' => route('tasks.store'),
    'projectAction' => route('projects.store'),
])
@php
    $projects = collect($projects);
@endphp

<div id="create-modal">



    {{-- Task Modal --}}
    <div id="task-modal"
        class="modal  fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 opacity-0 transition-opacity duration-[100ms] "
        role="dialog" aria-labelledby="task-modal-title" aria-modal="true">

        {{-- Backdrop --}}
        <div data-modal-backdrop class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"></div>

        {{-- Modal Container --}}
        <div class="relative w-full max-w-4xl mx-auto">
            <div class="gradient-border rounded-2xl">
                <div
                    class="flex flex-col lg:flex-row bg-[#f5f5f5] lg:bg-transparent rounded-2xl overflow-hidden max-h-[85vh] lg:max-h-[85vh]">

                    {{-- Main Modal Content --}}
                    <div
                        class="flex-1 flex flex-col min-w-0 bg-[#f5f5f5] rounded-t-2xl lg:rounded-l-2xl lg:rounded-tr-none max-h-[85vh] pb-2">
                        {{-- Header --}}
                        <div
                            class="bg-gradient-to-r from-[#10B981] to-[#34c796] text-white px-4 sm:px-6 py-4 rounded-t-2xl lg:rounded-tl-2xl lg:rounded-tr-none">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h2 id="task-modal-title" class="text-xl sm:text-2xl font-bold truncate">
                                        {{ $task ? 'Edit Task' : 'Create New Task' }}</h2>
                                    <p id="task-modal-description" class="text-xs sm:text-sm text-white/90 mt-1">
                                        {{ $task ? 'Update your task details' : 'Add a new task to your project' }} </p>
                                </div>
                                <button data-modal-close="task-modal"
                                    class="ml-4 text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 transition-all flex-shrink-0"
                                    aria-label="Close modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Scrollable Form Content --}}
                        <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-6  scrollbar-gradient">

                            <form id="task-modal-form" action="{{ $taskAction }}" method="POST" class="space-y-4"
                                data-create-action="{{ route('tasks.store') }}"
                                data-update-action="{{ route('tasks.update', ':id') }}">
                                @csrf

                                {{-- Method field will be added dynamically by JS for updates --}}
                                @if ($task)
                                    @method('PATCH')
                                @endif

                                {{-- Title --}}
                                <div>
                                    <label for="task-title" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Task Title <span class="text-red-500">*</span>
                                    </label>
                                    <input value="{{ $task->title ?? '' }}" type="text" id="task-title"
                                        name="title" required
                                        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition-all"
                                        placeholder="Enter task title" autocomplete="off" />
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label for="task-description"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea id="task-description" name="description" rows="3"
                                        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition-all resize-none"
                                        placeholder="Add task details..." autocomplete="off">{{ $task->description ?? '' }}</textarea>
                                </div>

                                {{-- Due Date --}}
                                <div>
                                    <label for="task-due-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Due Date
                                    </label>
                                    <input type="date" id="task-due-date" name="due_date"
                                        value="{{ $task && $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '' }}"
                                        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition-all" />
                                </div>

                                {{-- Priority & Status Row --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="task-priority"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Priority
                                        </label>
                                        <select id="task-priority" name="priority"
                                            class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition-all">
                                            <option value="low"
                                                {{ ($task->priority ?? 'medium') == 'low' ? 'selected' : '' }}>Low
                                            </option>
                                            <option value="medium"
                                                {{ ($task->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium
                                            </option>
                                            <option value="high"
                                                {{ ($task->priority ?? 'medium') == 'high' ? 'selected' : '' }}>High
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="task-status" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Status
                                        </label>
                                        <select id="task-status" name="status"
                                            class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition-all">
                                            <option value="pending"
                                                {{ ($task->status ?? 'pending') == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="in_progress"
                                                {{ ($task->status ?? 'pending') == 'in_progress' ? 'selected' : '' }}>
                                                In Progress</option>
                                            <option value="done"
                                                {{ ($task->status ?? 'pending') == 'done' ? 'selected' : '' }}>Done
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Project Selection (same as before) --}}
                                <div class="hidden lg:block">
                                    <input type="hidden" name="project_id" value="{{ $task->project_id ?? '' }}">

                                </div>
                                <details
                                    class="block lg:hidden bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
                                    <summary
                                        class="font-semibold text-sm px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors">
                                        Add to <span class="text-[#5c2fd1]">Project</span>
                                    </summary>
                                    <div>
                                        <ul class="divide-y divide-gray-200 max-h-48 overflow-y-auto">
                                            @forelse ($projects as $project)
                                                <li>
                                                    <label
                                                        class="flex items-center px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors">
                                                        <input form="{{ $formId }}" type="radio"
                                                            name="project_id" value="{{ $project->id }}"
                                                            class="mr-3 text-[#5c2fd1] focus:ring-[#5c2fd1]" />
                                                        <span class="truncate text-sm">{{ $project->name }}</span>
                                                    </label>
                                                </li>
                                            @empty
                                                <li class="px-4 py-3 text-gray-500 text-sm italic text-center">No
                                                    projects yet!</li>
                                            @endforelse
                                        </ul>

                                        @if ($projects->isNotEmpty())
                                            <div class="px-4 py-3 border-t border-gray-200 bg-white">
                                                <button type="button" data-modal-open="project-modal"
                                                    class="w-full px-4 py-2 bg-white border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium">
                                                    + New Project
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </details>

                                {{-- Action Buttons --}}
                                <div
                                    class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-gray-200 mt-6">
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
                        </div>
                    </div>

                    {{-- Desktop Project Picker Sidebar --}}
                    <div class="hidden lg:flex flex-col bg-white w-64 rounded-r-2xl">
                        <div class="p-4 border-b border-gray-200 rounded-tr-2xl">
                            <h3 class="font-bold text-lg text-center">
                                Select <span class="text-[#5c2fd1]">Project</span>
                            </h3>
                        </div>

                        <div class="flex-1 overflow-y-auto p-3">
                            <ul class="space-y-1">
                                @forelse ($projects as $project)
                                    <li>
                                        <label class="block cursor-pointer">
                                            <input type="radio" name="project_id" value="{{ $project->id }}"
                                                form="task-modal-form" class="sr-only peer" />
                                            <span
                                                class="flex items-center px-3 py-3 rounded-lg hover:bg-gray-50 transition-all truncate peer-checked:bg-[#6B3EEA] peer-checked:text-white text-sm">
                                                {{ $project->name }}
                                            </span>
                                        </label>
                                    </li>
                                @empty
                                    <li class="px-3 py-6 text-gray-500 text-sm italic text-center">
                                        No projects yet
                                        <button type="button"
                                            class="block mt-2 w-full text-[#6B3EEA] hover:text-[#5c2fd1] font-medium"
                                            data-modal-open="project-modal">
                                            Create one
                                        </button>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        @if ($projects->isNotEmpty())
                            <div class="p-3 border-t border-gray-200 rounded-br-2xl">
                                <button type="button" data-modal-open="project-modal"
                                    class="w-full px-4 py-2 bg-white border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium">
                                    + New Project
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Modal --}}
    <div id="project-modal"
        class="modal  fixed inset-0 z-[60] flex items-center justify-center p-3 sm:p-4 opacity-0 transition-opacity duration-[00ms]"
        role="dialog" aria-labelledby="project-modal-title" aria-modal="true">

        {{-- Backdrop --}}
        <div data-modal-backdrop class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"></div>

        {{-- Modal Container --}}
        <div class="relative w-full max-w-lg mx-auto">
            <div class="gradient-border rounded-2xl">
                <div class="bg-white rounded-2xl max-h-[85vh] flex flex-col pb-2">

                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-r from-[#6b3eea] to-[#8b5cf6] text-white px-4 sm:px-6 py-4 rounded-t-2xl">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h2 id="project-modal-title" class="text-xl sm:text-2xl font-bold truncate">Create New
                                    Project</h2>
                            </div>
                            <button data-modal-close="project-modal"
                                class="ml-4 text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 transition-all flex-shrink-0"
                                aria-label="Close modal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-6">
                        <form action="{{ $projectAction }}" method="POST" class="space-y-4">
                            @csrf

                            {{-- Project Title --}}
                            <div>
                                <label for="project-name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="project-name" name="name" required
                                    class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#6b3eea] focus:border-transparent transition-all"
                                    placeholder="Enter project name" autocomplete="off" />
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="project-description"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea id="project-description" name="description" rows="3"
                                    class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#5c2fd1] focus:border-transparent transition-all resize-none"
                                    placeholder="Describe your project..." autocomplete="off"></textarea>
                            </div>

                            {{-- Dates Row --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="project-start-date"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        Start Date
                                    </label>
                                    <input type="date" id="project-start-date" name="start_date"
                                        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#6b3eea] focus:border-transparent transition-all" />
                                </div>

                                <div>
                                    <label for="project-end-date"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        End Date
                                    </label>
                                    <input type="date" id="project-end-date" name="end_date"
                                        class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#6b3eea] focus:border-transparent transition-all" />
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="project-status" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Status
                                </label>
                                <select id="project-status" name="status"
                                    class="text-sm sm:text-base w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#6b3eea] focus:border-transparent transition-all">
                                    <option value="pending" selected>Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>

                            {{-- Action Buttons --}}
                            <div
                                class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-gray-200 mt-6">
                                <button type="button" data-modal-close="project-modal"
                                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-[#6b3eea] text-white rounded-lg hover:bg-[#5c2fd1] active:bg-[#4f29b8] font-medium transition-all shadow-sm">
                                    Create Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{{-- @vite(['resources/js/utils/animatedColoredBorder.js', 'resources/css/animatedColoredBorder.css']) --}}
