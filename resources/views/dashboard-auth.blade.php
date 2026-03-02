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
    <div id="dashboard"></div>





            <section>
                {{-- <div class="w-full md:w-3/4 mx-auto h-64">
                    <canvas id="projectsLineChart" data-projects="{{ json_encode($tasksStatusCounts) }}"></canvas>
                </div> --}}

                {{-- <h2>Notifications</h2>
                @foreach (auth()->user()->notifications as $notification)
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm">{{ $notification->data['message'] }}</p>
                        <small class="text-gray-500 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach --}}

            </section>
        </div>

    <div id="walkthrough"></div>
    @endauth


    @vite(['resources/js/dashboard/scrollable-Cards.js'])

    @vite(['resources/js/dashboard/chart.js'])
    @vite('resources/js/SPA/main.js')


@endsection
