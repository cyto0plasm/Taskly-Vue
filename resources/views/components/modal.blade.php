@props([
    'id',
    'title',
    'description' => null,
    'gradientFrom' => '#10B981',
    'gradientTo' => '#34c796',
    'size' => 'max-w-4xl',
    'sidebar' => null, // pass HTML for sidebar if needed
])

<div id="{{ $id }}"
    class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 opacity-0 transition-opacity duration-[700ms]"
    role="dialog" aria-labelledby="{{ $id }}-title" aria-modal="true">

    {{-- Backdrop --}}
    <div data-modal-backdrop class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"></div>

    {{-- Modal container --}}
    <div class="relative w-full {{ $size }} mx-auto">
        <div class="gradient-border rounded-2xl">

            <div
                class="flex flex-col lg:flex-row bg-[#f5f5f5] lg:bg-transparent rounded-2xl overflow-hidden max-h-[85vh] lg:max-h-[85vh]">

                {{-- Main modal content --}}
                <div
                    class="flex-1 flex flex-col min-w-0 bg-[#f5f5f5] rounded-t-2xl lg:rounded-l-2xl lg:rounded-tr-none max-h-[85vh] pb-2">

                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-r from-[{{ $gradientFrom }}] to-[{{ $gradientTo }}] text-white px-4 sm:px-6 py-4 rounded-t-2xl lg:rounded-tl-2xl lg:rounded-tr-none">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h2 id="{{ $id }}-title" class="text-xl sm:text-2xl font-bold truncate">
                                    {{ $title }}
                                </h2>
                                @if ($description)
                                    <p id="{{ $id }}-description"
                                        class="text-xs sm:text-sm text-white/90 mt-1">
                                        {{ $description }}
                                    </p>
                                @endif
                            </div>
                            <button data-modal-close="{{ $id }}"
                                class="ml-4 text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 transition-all flex-shrink-0"
                                aria-label="Close modal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Scrollable content slot --}}
                    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-6 scrollbar-gradient">
                        {{ $slot }}
                    </div>

                </div>

                {{-- Optional sidebar --}}
                @if ($sidebar)
                    <div class="hidden lg:flex flex-col bg-white w-64 rounded-r-2xl">
                        {!! $sidebar !!}
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>
