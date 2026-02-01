{{-- resources/views/Task/partials/task-detail-content.blade.php --}}
@props(['task', 'projects' => []])
@php
    $statusColors = [
        'pending' => [
            'bg' => 'bg-yellow-500',
            'textBg' => 'bg-yellow-100',
            'text' => 'Pending',
            'textColor' => 'text-yellow-800',
        ],
        'in_progress' => [
            'bg' => 'bg-blue-500',
            'textBg' => 'bg-blue-100',
            'text' => 'In Progress',
            'textColor' => 'text-blue-800',
        ],
        'done' => [
            'bg' => 'bg-green-500',
            'textBg' => 'bg-green-100',
            'text' => 'Done',
            'textColor' => 'text-green-800',
        ],
    ];
    $badge = $statusColors[$task->status] ?? $statusColors['pending'];
@endphp


<!-- Task Detail Panel -->

<div class="w-full bg-white dark:bg-[#222321] rounded-lg shadow-md p-4 sm:p-6 h-fit min-h-[20rem] sm:min-h-[24rem]">

    <!-- Skeleton Loader -->
    <div id="taskDetailSkeleton" class="hidden animate-pulse">
        <!-- Status Badge Skeleton -->
        <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-300 rounded-full"></div>
            <div class="h-6 sm:h-7 w-20 sm:w-24 bg-gray-300 rounded-full"></div>
        </div>

        <!-- Title Skeleton -->
        <div class="h-7 sm:h-9 bg-gray-300 rounded-lg mb-3 sm:mb-4 w-full sm:w-3/4"></div>

        <!-- Description Skeleton -->
        <div class="space-y-2 mb-4 sm:mb-6">
            <div class="h-5 sm:h-6 bg-gray-200 rounded w-full"></div>
            <div class="h-5 sm:h-6 bg-gray-200 rounded w-5/6"></div>
            <div class="h-5 sm:h-6 bg-gray-200 rounded w-4/6"></div>
        </div>

        <!-- Info Cards Skeleton -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4 sm:mb-6">
            <!-- Main Info Card -->
            <div class="bg-gray-100 rounded-lg p-3 sm:p-4 flex-1">
                <div class="h-5 sm:h-6 bg-gray-300 rounded w-28 sm:w-32 mb-2 sm:mb-3"></div>
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-32 sm:w-40"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-36 sm:w-44"></div>
                    </div>
                </div>
            </div>

            <!-- Date Info Card -->
            <div class="bg-gray-100 rounded-lg p-3 sm:p-4 flex-1">
                <div class="h-5 sm:h-6 bg-gray-300 rounded w-16 sm:w-20 mb-2 sm:mb-3"></div>
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-32 sm:w-40"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <div class="h-4 sm:h-5 bg-gray-200 rounded w-36 sm:w-44"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons Skeleton -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="h-9 sm:h-10 bg-gray-300 rounded-lg w-full sm:w-40"></div>
            <div class="h-9 sm:h-10 bg-gray-200 rounded-lg w-full sm:w-32"></div>
        </div>
    </div>

    <!-- Actual Content -->
    <div class="w-full overflow-hidden">
        <div id="taskDetail" class="w-full ">

            <!-- Status Badge -->
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div
                    class="w-5 h-5 sm:w-6 sm:h-6 {{ $badge['bg'] }} rounded-full flex items-center justify-center transition-colors duration-300 ease-out">
                    <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <span
                    class="task-state px-2.5 py-1 sm:px-3 sm:py-1 {{ $badge['textBg'] }} {{ $badge['textColor'] }} text-xs sm:text-sm font-medium rounded-full transition-colors duration-300 ease-out">
                    {{ $badge['text'] }}
                </span>

            </div>

            <!-- Title -->
            <h1
                class="task-title text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-3 sm:mb-4 break-words">
                {{ $task->title }}
            </h1>

            <div class="prose max-w-none overflow-hidden">
                <!-- Description -->
                <div class="mb-4 sm:mb-6">
                    <p id="taskDescription"
                        class="task-description text-gray-600 dark:text-gray-300 text-base sm:text-lg leading-relaxed break-words max-w-full line-clamp-3"
                        style="overflow-wrap: anywhere;">
                        {{ $task->description }}
                    </p>
                    <button id="toggleDescription"
                        class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium mt-2 hidden focus:outline-none px-2 py-1">
                        Show more
                    </button>
                </div>

                <!-- Info Cards -->
                <div class="flex flex-col md:flex-row gap-3 mb-4 sm:mb-6">
                    <!-- Task Details Card -->
                    <div
                        class="bg-gray-100 dark:bg-[#acacac] rounded-lg p-3 sm:p-4 flex-1 transition-all duration-300 hover:bg-gray-50">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Task Details</h3>
                        <ul class="space-y-2 text-sm sm:text-base text-gray-600">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">
                                    Priority: <span class="task-priority font-medium">Medium</span>
                                </span>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">Type: <span
                                        class="task-type font-medium">{{ $task->type ?? 'No Type' }}</span></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">Project: <span
                                        class="project font-medium">{{ $task->project ?? 'No Project' }}</span></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Dates Card -->
                    <div
                        class="bg-gray-100 dark:bg-[#acacac] rounded-lg p-3 sm:p-4 flex-1 transition-all duration-300 hover:bg-gray-50">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Dates</h3>
                        <ul class="space-y-2 text-sm sm:text-base text-gray-600">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">Due: <span
                                        class="task-time font-medium">{{ $task->due_date ?? 'No Due Date' }}</span></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">Created: <span
                                        class="task-Created-at font-medium">{{ $task->created_at ?? 'N/A' }}</span></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-teal-500 rounded-full flex-shrink-0"></span>
                                <span class="break-words">Updated: <span
                                        class="task-updated-at font-medium">{{ $task->updated_at ?? 'N/A' }}</span></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <!-- Mark as Complete Button -->
                    <form class="update-status-form w-full sm:w-auto mb-1" data-task-id="{{ $task->id }}"
                        action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <x-button type="submit" bgColor="bg-[#10b981]" hoverColor="hover:bg-[#04bd7f]"
                            activeColor="active:bg-[#36bd90]" textColor="text-white" text="Mark as Complete"
                            class="w-full sm:w-auto">
                        </x-button>
                    </form>

                    <!-- Edit Task Button -->
                    <form id="task-edit-Form" action="{{ route('tasks.edit', $task->id) }}" method="GET"
                        class="w-full sm:w-auto mx-1 mb-1">
                        <x-button id="task-edit-btn" type="submit" bgColor="bg-gray-100"
                            hoverColor="hover:bg-gray-200" activeColor="active:bg-gray-300"
                            textColor="text-[#10b981]" text="Edit Task" class="w-full sm:w-auto">
                        </x-button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- js for descripton --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const description = document.getElementById('taskDescription');
        const toggleBtn = document.getElementById('toggleDescription');
        let isExpanded = false;

        // Function to check if content overflows
        function checkOverflow() {
            if (description.scrollHeight > description.clientHeight) {
                toggleBtn.classList.remove('hidden');
            } else {
                toggleBtn.classList.add('hidden');
            }
        }

        // Check immediately and after delays for dynamic content
        checkOverflow();
        setTimeout(checkOverflow, 100);
        setTimeout(checkOverflow, 500);

        // Recheck on window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(checkOverflow, 100);
        });

        toggleBtn.addEventListener('click', function() {
            isExpanded = !isExpanded;

            if (isExpanded) {
                description.classList.remove('line-clamp-3');
                toggleBtn.textContent = 'Show less';
            } else {
                description.classList.add('line-clamp-3');
                toggleBtn.textContent = 'Show more';
                // Scroll back to description smoothly
                description.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    });
</script>
<script type="module" src="{{ asset('js/tasks/editTask.js') }}"></script>

<script type="module" src="{{ asset('js/tasks/updateStatus.js') }}"></script>
