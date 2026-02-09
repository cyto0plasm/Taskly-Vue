@extends('layout')
@section('page-name', 'Tasks')

@section('main')

<div id="flashApp"></div>
<div id="ModalApp"></div>
<div id="taskApp"></div>

@endsection

@vite('resources/js/SPA/main.js')
