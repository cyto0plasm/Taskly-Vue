@props(['tasks' => collect(), 'firstTask' => null, 'projects' => []])

{{-- Skeleton Loading State --}}
<x-tasks.task-skeleton id="taskDetailSkeleton" />

{{-- Empty State (No Task Selected) --}}
<div id="taskDetailEmpty"
    class="w-full bg-white dark:bg-[#222321] rounded-lg shadow-md p-6 min-h-[18rem] sm:min-h-[20rem] flex flex-col items-center justify-center text-center">
    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">No task selected</h2>
    <p class="text-gray-500 dark:text-gray-400 mt-2">Create a task or select one from the list</p>
</div>

{{-- Task Content (Populated by JS) --}}
<div id="taskDetailContent"
    class="hidden w-full bg-white dark:bg-[#222321] rounded-lg shadow-md p-4 sm:p-6 h-auto min-h-[18rem] sm:min-h-[20rem] flex flex-col gap-4">

    {{-- Status Badge --}}
    <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4 flex-wrap">
        <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center">
            @if ($firstTask?->status === 'done')
                <x-svg.check-mark-circle />
            @elseif($firstTask?->status === 'pending')
                <x-svg.pending />
            @elseif($firstTask?->status === 'in_progress')
                <x-svg.progress />
            @endif
        </div>
        <span id="status"
            class="task-state px-2.5 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full
                     @if ($firstTask?->status === 'done') bg-emerald-100 text-emerald-800
                     @elseif($firstTask?->status === 'pending') bg-red-100 text-red-600
                     @else bg-yellow-100 text-yellow-800 @endif">
            {{ ucfirst(str_replace('_', ' ', $firstTask?->status ?? 'In Progress')) }}
        </span>
    </div>

    {{-- Task Title --}}
    <h1 class="task-title text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-4 break-words">
        {{ $firstTask?->title ?? 'Task Title' }}
    </h1>

    {{-- Task Description --}}
    <div class="mb-4 sm:mb-6 relative">
        <p id="task-description"
            class="task-description text-gray-600 dark:text-gray-300 text-base sm:text-lg leading-relaxed break-words max-w-full line-clamp-3">
            {{ $firstTask?->description ?? 'Task description will appear here' }}
        </p>
        <button id="toggleDescription"
            class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium mt-2 focus:outline-none px-2 py-1">
            Show more
        </button>
    </div>

    {{-- Info Cards --}}
    <div class="flex flex-col md:flex-row gap-3 mb-4 sm:mb-6 flex-wrap">
        {{-- Task Details Card --}}
        <div
            class="bg-gray-100 dark:bg-[#acacac] rounded-lg p-3 sm:p-4 flex-1 min-w-[220px] max-w-full transition-all duration-300 hover:bg-gray-50">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Task Details</h3>
            <ul class="space-y-2 text-sm sm:text-base text-gray-600">
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0"></span>
                    Priority: <span
                        class="task-priority font-medium">{{ ucfirst($firstTask?->priority ?? 'Medium') }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                    Type: <span class="task-type font-medium">{{ ucfirst($firstTask?->type ?? '-') }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0"></span>
                    Project: <span class="project font-medium">{{ $firstTask?->project?->name ?? '-' }}</span>
                </li>
            </ul>
        </div>

        {{-- Dates Card --}}
        <div
            class="bg-gray-100 dark:bg-[#acacac] rounded-lg p-3 sm:p-4 flex-1 min-w-[220px] max-w-full transition-all duration-300 hover:bg-gray-50">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Dates</h3>
            <ul class="space-y-2 text-sm sm:text-base text-gray-600">
                <li class="flex items-center gap-2">
                    Due date: <span class="task-time font-medium">{{ $firstTask?->due_date ?? '-' }}</span>
                </li>
                <li class="flex items-center gap-2">
                    Created: <span
                        class="task-Created-at font-medium">{{ $firstTask?->created_at?->format('M d, Y') ?? '-' }}</span>
                </li>
                <li class="flex items-center gap-2">
                    Updated: <span
                        class="task-updated-at font-medium">{{ $firstTask?->updated_at?->format('M d, Y') ?? '-' }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-wrap">
        {{-- Mark Complete --}}
        <form id="update-status-form" data-task-id="{{ $firstTask?->id ?? '' }}"
            action="{{ $firstTask ? route('tasks.status-update', $firstTask->id) : '' }}" method="POST"
            class="w-full sm:w-auto mb-1">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="done">
            <x-button type="submit" bgColor="bg-[#10b981]" hoverColor="hover:bg-[#04bd7f]"
                activeColor="active:bg-[#36bd90]" textColor="text-white" text="Mark as Complete"
                class="w-full sm:w-auto" />

        </form>
        {{-- <x-mark-status-button :taskId="$firstTask?->id" /> --}}

        {{-- Edit Task --}}
        <form id="task-edit-form"
            action="{{ $firstTask ? route('tasks.edit', $firstTask->id) : route('tasks.edit', 0) }}" method="GET"
            class="w-full sm:w-auto mb-1">
            @csrf
            <x-button id="task-edit-btn" bgColor="bg-gray-100" hoverColor="hover:bg-gray-200"
                activeColor="active:bg-gray-300" textColor="text-[#10b981]" text="Edit Task" class="w-full sm:w-auto"
                type="button" />
        </form>



    </div>
</div>
