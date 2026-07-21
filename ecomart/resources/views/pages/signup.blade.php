@extends('layouts.app')

@section('title', 'Sign Up · EcoMart')

@section('content')

<section class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card shadow border-0 rounded-4">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <img src="{{ asset('assets/images/logo.jpg') }}"
                             alt="EcoMart"
                             height="70">

                        <h2 class="mt-3">
                            Create Your EcoMart Account
                        </h2>

                        <p class="text-muted">
                            Join EcoMart and start shopping today.
                        </p>

                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('signup.submit') }}">

                        @csrf

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    class="form-control"
                                    value="{{ old('first_name') }}"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    class="form-control"
                                    value="{{ old('last_name') }}"
                                    required>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone') }}"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                required>

                        </div>

                        <button type="submit" class="btn btn-success w-100">

                            <i class="bi bi-person-plus"></i>

                            Create Account

                        </button>

                    </form>

                    <hr>

                    <p class="text-center mb-0">

                        Already have an account?

                        <a href="{{ route('login') }}">
                            Login Here
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection