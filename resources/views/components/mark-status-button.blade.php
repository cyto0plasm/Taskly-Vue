@props([
    'taskId' => null,
    'bgColor' => 'bg-[#10b981]',
    'hoverColor' => 'hover:bg-[#04bd7f]',
    'activeColor' => 'active:bg-[#36bd90]',
    'textColor' => 'text-white',
])

<form id="update-status-form-{{ $taskId }}" data-task-id="{{ $taskId }}"
    action="{{ $taskId ? route('tasks.status-update', $taskId) : '' }}" method="POST"
    class="inline-flex relative w-full sm:w-auto">
    @csrf
    @method('PATCH')

    {{-- Hidden input to store the selected status --}}
    <input type="hidden" name="status" value="done">

    {{-- Left main button: mark as complete --}}
    <button type="submit"
        onclick="event.preventDefault(); this.form.querySelector('input[name=status]').value='done'; this.form.submit();"
        class="{{ $bgColor }} {{ $textColor }} {{ $hoverColor }} {{ $activeColor }} rounded-l-md px-4 py-2 font-semibold text-xs uppercase tracking-widest focus:outline-none   shadow-md transition-colors duration-150">
        Mark as Complete
    </button>

    {{-- Right arrow dropdown --}}
    <div class="relative">
        <button type="button"
            class="{{ $bgColor }} {{ $textColor }} {{ $hoverColor }} {{ $activeColor }} rounded-r-md px-2 py-2 flex items-center justify-center focus:outline-none  shadow-md transition-colors duration-150"
            onclick="event.stopPropagation(); this.nextElementSibling.classList.toggle('hidden')">
            <span class="text-xs">▼</span>
        </button>
        <ul
            class="absolute right-0 mt-1 hidden bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg z-50 min-w-[150px]">
            <li>
                <button type="button"
                    onclick="event.preventDefault(); const form = this.closest('form'); form.querySelector('input[name=status]').value='in_progress'; form.submit();"
                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                    In Progress
                </button>
            </li>
            <li>
                <button type="button"
                    onclick="event.preventDefault(); const form = this.closest('form'); form.querySelector('input[name=status]').value='pending'; form.submit();"
                    class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                    Pending
                </button>
            </li>
        </ul>
    </div>
</form>

<script>
    // Close dropdown if clicked outside
    document.addEventListener('click', function(e) {
        document.querySelectorAll('form[id^="update-status-form-"] ul').forEach(ul => {
            if (!ul.contains(e.target) && !ul.previousElementSibling.contains(e.target)) {
                ul.classList.add('hidden');
            }
        });
    });
</script>
