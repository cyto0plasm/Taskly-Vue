@extends('layout')
@section('page-name', 'Projects')

@section('meta_description', 'View and manage your Projects. Organize your workflow with our task management system.')

@section('main')

    <div id="ModalApp"></div>
   <div id="projectApp"></div>

@endsection
@vite('resources/js/SPA/main.js')
