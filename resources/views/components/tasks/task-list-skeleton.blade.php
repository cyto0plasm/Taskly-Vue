@props(['items' => 5])

<div id="tasks-loading" class="animate-pulse">

    <!-- Header Skeleton -->
    <div class="border-b dark:border-0 border-gray-200 px-4 sm:px-6 py-3 sm:py-4">

        <div class="flex items-center justify-between mb-2">
            <div class="h-7 sm:h-8 w-40 bg-gray-300 dark:bg-gray-700 rounded"></div>

            <div
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg border
                        bg-gray-100 dark:bg-gray-800
                        border-gray-200 dark:border-gray-700">
                <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                <div class="h-3 w-20 bg-gray-300 dark:bg-gray-600 rounded"></div>
            </div>
        </div>

        <!-- Status Counters Skeleton -->
        <div class="flex justify-center flex-wrap gap-3 sm:gap-5 mt-3 text-xs sm:text-sm">
            @for ($i = 0; $i < 3; $i++)
                <span class="flex items-center gap-1.5">
                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="h-4 w-6 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded hidden md:inline"></div>
                </span>
            @endfor
        </div>
    </div>

    <!-- Tasks List Skeleton -->
    <ul class="p-0 max-h-[50vh] sm:max-h-[60vh] lg:max-h-[65vh] overflow-y-auto">

        @for ($i = 0; $i < $items; $i++)
            <li
                class="group task-item block border-b dark:border-0 border-gray-100
                       p-4 flex items-center gap-3">

                <div
                    class="task-status-icon flex-shrink-0 w-6 h-6
                           bg-gray-300 dark:bg-gray-600 rounded-full">
                </div>

                <div class="flex-1 min-w-0 space-y-2">
                    <div class="h-5 w-3/4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>

                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full opacity-50"></div>
            </li>
        @endfor

    </ul>
</div>
