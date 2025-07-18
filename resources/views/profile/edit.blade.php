@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <h1>Edit Profile</h1>
@endsection

@section('content')
  {{-- Success Message --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
     
        {{ session('success') }}
    </div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input name="name" class="form-control" value="{{ old('name', $user->name) }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input name="email" class="form-control" value="{{ old('email', $user->email) }}">
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
