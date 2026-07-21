@extends('layouts.app')

@section('title', 'Login · EcoMart')

@section('content')

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="auth-card bg-white p-4 p-md-5 rounded-4 shadow-sm">

                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/logo.jpg') }}"
                         alt="EcoMart"
                         style="height:70px;">

                    <h2 class="mt-3">Login</h2>

                    <p class="text-muted">
                        Access your EcoMart account.
                    </p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">

                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login
                    </button>

                </form>

                <p class="text-center mt-4 mb-0">
                    Don't have an account?

                    <a href="{{ route('signup') }}">
                        Sign up here
                    </a>
                </p>

            </div>

        </div>
    </div>
</section>

@endsection