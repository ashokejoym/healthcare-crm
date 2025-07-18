@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Admin Dashboard</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-info-box title="Patients" text="123" icon="fas fa-users" theme="info" />
        </div>
        <div class="col-md-6">
            <x-adminlte-info-box title="Appointments" text="45 Today" icon="fas fa-calendar-check" theme="success" />
        </div>
    </div>
@endsection

@section('css')
    {{-- Add custom CSS here if needed --}}
@endsection

@section('js')
    <script>console.log('Admin panel loaded');</script>
@endsection
