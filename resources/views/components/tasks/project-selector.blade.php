@props(['projects' => [], 'formId' => 'task-modal-form'])

<details class="block lg:hidden bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
    <summary class="font-semibold text-sm px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors">
        Add to <span class="text-[#5c2fd1]">Project</span>
    </summary>

    <div class="divide-y divide-gray-200 max-h-48 overflow-y-auto">
        @forelse ($projects as $project)
            <label class="flex items-center px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors">
                <input form="{{ $formId }}" type="radio" name="project_id" value="{{ $project->id }}"
                    class="mr-3 text-[#5c2fd1] focus:ring-[#5c2fd1]" />
                <span class="truncate text-sm">{{ $project->name }}</span>
            </label>
        @empty
            <div class="px-4 py-3 text-gray-500 text-sm italic text-center">
                No projects yet!
            </div>
        @endforelse
    </div>

    <!-- Always show the "New Project" button at the bottom -->
    <div class="px-4 py-3 border-t border-gray-200 bg-white">
        <button type="button" data-modal-open="project-modal"
            class="w-full px-4 py-2 bg-white border border-[#6B3EEA] text-[#6B3EEA] rounded-lg hover:bg-[#6B3EEA] hover:text-white transition-all text-sm font-medium">
            + New Project
        </button>
    </div>
</details>
