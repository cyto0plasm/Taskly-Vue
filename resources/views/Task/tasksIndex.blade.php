@extends('layout')
@section('page-name', 'Tasks')
@section('meta_description', 'View and manage your tasks. Organize your workflow with our task management system.')
@section('main')

<div id="flashApp"></div>
<div id="ModalApp"></div>
<div id="taskApp"></div>

@endsection

@vite('resources/js/SPA/main.js')
