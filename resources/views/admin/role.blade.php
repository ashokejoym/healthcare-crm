@extends('adminlte::page')

@section('title', 'User Role Assignment')

@section('content_header')
    <h1>Assign Roles to Users</h1>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($users as $user)
        <form method="POST" action="{{ url('/admin/users/'.$user->id.'/assign-roles') }}">
            @csrf
            <strong>{{ $user->name }}</strong> ({{ $user->email }})<br>
            @foreach($roles as $role)
                <label>
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            @endforeach
            <button type="submit" class="btn btn-primary btn-sm mt-2">Update</button>
        </form>
        <hr>
    @endforeach
@endsection
