
@extends('layout')
@section('page-name', 'Dashboard')


@section('main')
@auth
        <style>
            /* Hide scrollbar */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
        <div class="relative mx-auto  ">
            <div id="ModalApp"></div>

            <!-- Scrollable Cards -->
            <section class="flex flex-col">
                <div id="total_cards"
                    class="flex gap-4 overflow-x-auto flex-nowrap px-10 py-4 no-scrollbar scroll-smooth snap-x snap-mandatory mt-4">
                    <x-cards.dashboard-card title="Total Projects" description="Count of your projects" :number="$projectsCount ?? 0"
                        color="indigo" href="{{ route('projects.index') }}" type="projects" />

                    <x-cards.dashboard-card title="Total Tasks" description="Count of your tasks" :number="$tasksCount ?? 0"
                        color="teal" href="/tasks" type="tasks" />

                    <x-cards.dashboard-card title="Total in progress" description="Count of your Active tasks" :number="$tasksStatusCounts['in_progress'] ?? 0"
                        color="yellow" href="{{ route('tasks.index') }}" type="progress" />

                    <x-cards.dashboard-card title="Pending Tasks" description="Count of your pending tasks" :number="$tasksStatusCounts['pending'] ?? 0"
                        color="red" href="{{ route('tasks.index') }}" type="pending" />

                    <x-cards.dashboard-card title="Completed" description="Count of your completed tasks" :number="$tasksStatusCounts['done'] ?? 0"
                        color="green" href="{{ route('tasks.index') }}" type="completed-tasks" />

                    <x-cards.dashboard-card title="Deadlines" description="Upcoming Projects deadlines" :number="$tasksStatusCounts['done'] ?? 0"
                        color="pink" href="{{ route('projects.index') }}" type="upcoming-deadlines" />

                </div>
                <!-- Scroll Buttons - Bottom Center -->
                <div class="flex justify-center gap-3 mt-1 pb-4">
                    <!-- Left Scroll Button -->
                    <button id="scrollLeft"
                        class="bg-gray-800/60 dark:bg-gray-400 dark:text-black text-white p-2 rounded-full hover:bg-gray-800 dark:hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.293 15.707a1 1 0 010-1.414L8.414 10l3.879-4.293a1 1 0 10-1.414-1.414l-4.586 5.086a1 1 0 000 1.414l4.586 5.086a1 1 0 001.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Right Scroll Button -->
                    <button id="scrollRight"
                        class="bg-gray-800/60 dark:bg-gray-400 dark:text-black text-white p-2 rounded-full hover:bg-gray-800 dark:hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.707 4.293a1 1 0 010 1.414L11.586 10l-3.879 4.293a1 1 0 101.414 1.414l4.586-5.086a1 1 0 000-1.414L9.121 2.879a1 1 0 00-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </section>
            <section>
                <div class="w-full md:w-3/4 mx-auto h-64">
                    <canvas id="projectsLineChart" data-projects="{{ json_encode($tasksStatusCounts) }}"></canvas>
                </div>

            </section>
        </div>


    @endauth



    @vite(['resources/js/dashboard/chart.js'])
    @vite(['resources/js/dashboard/scrollable-Cards.js'])
@vite('resources/js/SPA/main.js')

    {{-- <script src="{{ asset('js/dashboard/scrollable-Cards.js') }}"></script> --}}

@endsection
