{{-- resources/views/components/task-project-sidebar.blade.php --}}
@props([
    'projects' => [],
    'task' => null,
])

@php
    $projects = collect($projects);
@endphp

<div class="hidden lg:flex flex-col bg-white w-64 rounded-r-2xl">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-200 rounded-tr-2xl">
        <h3 class="font-bold text-lg text-center">
            Select <span class="text-[#5c2fd1]">Project</span>
        </h3>
    </div>

    {{-- Project List --}}
    <div class="flex-1 overflow-y-auto p-3">
        <ul class="space-y-1">
            @forelse ($projects as $project)
                <li>
                    <label class="block cursor-pointer">
                        <input type="radio" name="project_id" value="{{ $project->id }}" form="task-modal-form"
                            class="sr-only peer" @if (isset($task) && $task->project_id == $project->id) checked @endif />
                        <span
                            class="flex items-center px-3 py-3 rounded-lg hover:bg-gray-50 transition-all truncate
                                     peer-checked:bg-[#6B3EEA] peer-checked:text-white text-sm">
                            {{ $project->name }}
                        </span>
                    </label>
                </li>
            @empty
                <li class="px-3 py-6 text-gray-500 text-sm italic text-center">
                    No projects yet
                    <button type="button" class="block mt-2 w-full text-[#6B3EEA] hover:text-[#5c2fd1] font-medium"
                        data-modal-open="project-modal">
                        Create one
                    </button>
                </li>
            @endforelse
        </ul>
    </div>

    {{-- New Project Button --}}
    @if ($projects->isNotEmpty())
        <div class="p-3 border-t border-gray-200 rounded-br-2xl">
            <button type="button" data-modal-open="project-modal"
                class="w-full px-4 py-2 bg-white border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium">
                + New Project
            </button>
        </div>
    @endif
</div>
