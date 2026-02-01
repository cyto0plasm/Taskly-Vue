<div class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-50">
    {{-- Expandable Options Menu --}}
    <div id="fab-menu"
        class="absolute bottom-16 sm:bottom-20 right-0 flex flex-col gap-2 sm:gap-3 mb-2 z-50 scale-y-0 opacity-0 origin-bottom transition-all duration-300"
        aria-label="Create options menu">

        <button data-modal-open="project-modal" aria-controls="project-modal" aria-label="Create new project"
            class="flex items-center gap-2 sm:gap-3 bg-white text-gray-700 px-4 sm:px-5 py-2.5 sm:py-3 text-sm sm:text-base rounded-full shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all whitespace-nowrap font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6b3eea]" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            New Project
        </button>

        <button data-modal-open="task-modal" aria-controls="task-modal" aria-label="Create new task"
            class="flex items-center gap-2 sm:gap-3 bg-white text-gray-700 px-4 sm:px-5 py-2.5 sm:py-3 text-sm sm:text-base rounded-full shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all whitespace-nowrap font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#10B981]" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            New Task
        </button>
    </div>

    {{-- Main FAB Button --}}
    <button data-fab-main id="fab-btn"
        class="bg-[#6b3eea] text-white p-3 sm:p-4 rounded-full shadow-xl hover:bg-[#5c2fd1] hover:shadow-2xl active:bg-[#6335e0] transition-all duration-200"
        aria-label="Create new item" aria-expanded="false" aria-controls="fab-menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>
