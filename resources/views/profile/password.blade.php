@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
    <h1>Change Password</h1>
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


 <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PATCH')

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Update Password</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                    @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-success">Update Password</button>
            </div>
        </div>
    </form>

@endsection
