@props([
    'title' => 'title',
    'description' => 'description',
    'number' => 0,
    'href' => '#',
    'type' => 'tasks',
    'color' => 'green',
    'badgeColor' => 'gray',
])
@php
    $icons = [
        'projects' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        'tasks' =>
            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'progress' => 'M4 19h16M8 17V9m4 8V5m4 12v-3',
        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'upcoming-deadlines' =>
            'M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'completed-tasks' => 'M5 13l4 4L19 7',
    ];
@endphp
<a href="{{ $href }}"
    class="block min-w-[250px] group relative w-[250px] h-[150px] px-6 py-4 rounded-2xl shadow-md bg-white/30 dark:bg-[#1e1f1e] backdrop-blur-sm hover:bg-white/40 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">

    <!-- Status badge with icon -->
    <div
        class="absolute top-0 left-0 rounded-tl-2xl rounded-br-2xl w-[120px] h-[35px]   shadow-md flex items-center justify-center gap-1 overflow-hidden group-hover:w-[130px] transition-all duration-300  @if ($color === 'yellow') bg-yellow-500
    @elseif($color === 'green') bg-green-500
    @elseif($color === 'teal') bg-teal-600
    @elseif($color === 'red') bg-red-500
    @elseif($color === 'pink') bg-pink-500
    @elseif($color === 'indigo') bg-indigo-500 @endif">
        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
        <span class="text-white dark:text-gray-100 bg- text-xs font-semibold text-nowrap ">{{ $title }}</span>
    </div>

    <!-- Content Area -->
    <div class="flex flex-col justify-between h-full pt-8 ">

        <!-- Icon and discription -->
        <div class="flex items-center gap-2">
            <div
                class="w-8 h-8 rounded-lg flex items-center justify-center
    @if ($color === 'yellow') bg-yellow-500
        @elseif($color === 'teal') bg-teal-600
    @elseif($color === 'green') bg-green-400
    @elseif($color === 'red') bg-red-400
    @elseif($color === 'pink') bg-pink-400
    @elseif($color === 'indigo') bg-indigo-400 @endif">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="{{ $icons[$type] ?? $icons['tasks'] }}" />
                </svg>

            </div>
            <span class="text-xs text-gray-500 dark:text-[#c5c5c5] font-medium">{{ $description }}</span>
        </div>

        <!-- Main Stats -->
        <div>
            <div class="flex items-end gap-2">
                <p
                    class="text-4xl font-bold text-gray-800 dark:text-white transition-colors duration-300
    @if ($color === 'yellow') group-hover:text-yellow-500
        @elseif($color === 'teal') group-hover:text-teal-600
    @elseif($color === 'green') group-hover:text-green-500
    @elseif($color === 'red') group-hover:text-red-500
    @elseif($color === 'pink') group-hover:text-pink-500
    @elseif($color === 'indigo') group-hover:text-indigo-500 @endif">
                    {{ $number }}
                </p>




            </div>
        </div>
    </div>

    <!-- Hover arrow -->
    <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </div>
</a>
