@extends('layout')
@section('page-name', 'Tasks')

@section('main')
    <section id="mainSection"
        class="relative flex flex-col gap-3 min-h-screen p-2
         sm:p-3 md:p-6
         lg:flex-row lg:items-start lg:gap-6">
        {{-- <x-fab></x-fab> --}}
        <x-vue.fab-vue></x-vue.fab-vue>


        <!-- Task List -->
        <div class="w-full lg:w-auto lg:sticky lg:top-20">
            <x-vue.task-list-vue >
            </x-vue.task-list-vue>
        </div>

        <!-- Task Details -->
        <div class="w-full lg:flex-1 lg:min-w-4xl">
            <x-vue.task-details-vue >
            </x-vue.task-details-vue>
        </div>

        <!-- Task Models -->
        {{-- <x-task-model :projects="$projects" /> --}}
        {{-- <x-tasks.task-modal :projects="$projects" /> --}}
        {{-- <x-projects.project-modal :projects="$projects" /> --}}
        {{-- <x-edit-task :projects="$projects" :tasks="$tasks" /> --}}
    </section>

    <!-- Drawer Canvas -->
    <x-drawer-canvas />
    {{-- rendered from main.js vue --}}
    <div id="global-ui"></div>

@endsection

