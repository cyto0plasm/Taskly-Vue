@props([
    'tasks' => collect(),
    'taskStatusDoneCount' => 0,
    'taskStatusProgressCount' => 0,
    'taskStatusPendingCount' => 0,
    'showAll' => request()->has('showAll'),
])

<div id="tasksList"
    class="w-full lg:min-w-[320px] lg:w-[28rem] lg:max-w-[32rem] bg-white dark:bg-[#222321] rounded-lg shadow-md lg:ml-[5rem]">

    <!-- Header -->
    <div class="border-b dark:border-0 border-gray-200 px-4 sm:px-6 py-3 sm:py-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Tasks List</h2>

            <!-- Show All / Sorting Toggle -->
            <a href="{{ $showAll ? route('tasks.index') : route('tasks.index', ['showAll' => 1]) }}"
                class="group flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200
               {{ $showAll
                   ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30'
                   : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                @if ($showAll)
                    <x-svg.menu class="w-4 h-4" />
                    <span>Show Pages</span>
                @else
                    <x-svg.sort class="w-4 h-4" />
                    <span>Enable Sorting</span>
                @endif
            </a>
        </div>

        <!-- Status Counters -->
        <div class="flex justify-center flex-wrap gap-3 sm:gap-5 mt-3 text-xs sm:text-sm text-gray-500">
            <span class="flex items-center gap-1.5">
                <x-svg.check-mark class="w-4 h-4" />
                <span class="text-green-500 font-bold">{{ $taskStatusDoneCount }}</span>
                <span class="hidden md:inline dark:text-white">Done</span>
            </span>
            <span class="flex items-center gap-1.5 whitespace-nowrap">
                <x-svg.progress class="w-4 h-4" />
                <span class="text-yellow-500 font-bold">{{ $taskStatusProgressCount }}</span>
                <span class="hidden md:inline dark:text-white">In Progress</span>
            </span>
            <span class="flex items-center gap-1.5">
                <x-svg.pending class="w-4 h-4" />
                <span class="text-red-500 font-bold">{{ $taskStatusPendingCount }}</span>
                <span class="hidden md:inline dark:text-white">Pending</span>
            </span>
        </div>

        @if ($showAll)
            <div
                class="mt-3 px-3 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-xs text-green-700 dark:text-green-300 font-medium text-center">
                    Drag & drop enabled - All tasks loaded
                </p>
            </div>
        @endif
    </div>

    <!-- Tasks List -->
    <div class="relative">

        {{-- Loading --}}
        {{-- <div id="tasks-loading">
            <x-tasks.task-list-skeleton :items="6" />
        </div> --}}

        {{-- Empty --}}
        {{-- <ul id="tasks-empty" class="hidden p-6 text-center text-gray-500">
            <li>
                <x-svg.empty-task class="w-12 h-12 mx-auto mb-3" />
                <p class="text-sm sm:text-base">No tasks yet</p>
            </li>
        </ul> --}}
        <ul id="sortable-list" data-url="{{ route('tasks.reorder') }}" data-csrf="{{ csrf_token() }}"
            data-sortable="{{ $showAll ? 'true' : 'false' }}"
            class=" p-0 max-h-[50vh] sm:max-h-[60vh] lg:max-h-[65vh] overflow-y-auto">

            {{-- @forelse ($tasks as $task)
            <x-task-button :state="$task->status" :title="$task->title" :task-id="$task->id" :url="route('tasks.show', $task->id)" />
        @empty
            <li class="p-6 text-center text-gray-500" id="emptyTasksList">
                <x-svg.empty-task class="w-12 h-12 mx-auto mb-3" />
                <p class="text-sm sm:text-base">No tasks yet</p>
            </li>
        @endforelse --}}

        </ul>
    </div>

    <!-- Pagination -->
    @if (!$showAll && method_exists($tasks, 'hasPages') && $tasks->hasPages())
        <div class="border-t dark:border-gray-700">
            {{ $tasks->links('pagination::custom') }}
        </div>
    @endif
</div>

{{-- Preload tasks data for JavaScript --}}
<script>
    // Ensure tasksData is always an array
    window.tasksData = {!! json_encode(collect($tasks)->values()->all()) !!};
</script>
