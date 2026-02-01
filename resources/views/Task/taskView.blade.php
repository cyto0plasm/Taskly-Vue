@extends('layout')
@section('page-name', 'Tasks')

@section('main')
    {{-- Task View --}}

    <ul id="sortable-list" data-url="{{ route('tasks.reorder') }}" data-csrf="{{ csrf_token() }} "class="space-y-2">
        @foreach ($tasks as $task)
            <li class="p-2 bg-gray-200 rounded cursor-move" data-id="{{ $task->id }}">
                {{ $task->title }}
            </li>
        @endforeach
    </ul>
    {{-- <button id="saveOrderBtn" class="mt-3 p-2 bg-blue-500 text-white rounded">Save Order</button> --}}

@endsection
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/tasks/sortableTasks.js') }}"></script>
