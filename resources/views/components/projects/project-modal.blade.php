@props([
    'project' => null, // singular
    'task' => null,
    'taskAction' => route('tasks.store'),
    'projectAction' => route('projects.store'),
])

<x-modal id="project-modal" title="{{ $project ? 'Edit Project' : 'Create Project' }}"
    description="{{ $project ? 'Update your Project details' : 'Add a new Project to your project' }}"
    gradientFrom="#10B981" gradientTo="#34c796">

    <form id="project-modal-form" action="{{ $projectAction }}" method="POST" class="space-y-4">
        @csrf
        @if ($project)
            @method('PATCH')
        @endif

        <x-form.form-input label="Title" name="title" placeholder="Enter Project title"
            value="{{ $project?->name ?? '' }}" required />

        <x-form.form-textarea label="Description" name="description" placeholder="Enter Project description"
            value="{{ $project?->description ?? '' }}" />

        <x-form.form-input label="Start Date" type="date" name="start_date"
            value="{{ $project?->start_date?->format('Y-m-d') }}" />
        <x-form.form-input label="End Date" type="date" name="end_date"
            value="{{ $project?->end_date?->format('Y-m-d') }}" />


        <x-form.form-select label="Status" name="status" :options="['pending' => 'Pending', 'in_progress' => 'In Progress', 'done' => 'Done']" :selected="$project?->status ?? 'pending'" />

        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-gray-200 mt-6">
            <button type="button" data-modal-close="project-modal"
                class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 active:bg-gray-300 font-medium transition-all">
                Cancel
            </button>
            <button type="submit"
                class="w-full sm:w-auto text-sm sm:text-base px-6 py-2.5 bg-[#6b3eea] text-white rounded-lg hover:bg-[#5c2fd1] active:bg-[#4f29b8] font-medium transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                {{ $project ? 'Update Project' : 'Create Project' }}
            </button>
        </div>
    </form>
</x-modal>

@vite(['resources/js/utils/animatedColoredBorder.js', 'resources/css/animatedColoredBorder.css'])
