@extends('layout')
@section('page-name', 'Tasks')


@section('main')
    @foreach ($projects as $project)
        <li>
            {{ $project->name }}
        </li>
    @endforeach
    {{ $projects->links() }}



@endsection
