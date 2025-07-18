@extends('adminlte::auth.login')

@section('auth_header')
    {{-- Custom login page logo/title --}}
   <style>.login-logo { display: none; }</style>
    <p class="login-box-msg">Sign in to HealthCare CRM</p>
@endsection
@section('auth_body')

    @if (session('status'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('status') }}
        </div>
    @endif
  {{-- Login failure or validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    {{-- The login form fields --}}
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

@endsection
