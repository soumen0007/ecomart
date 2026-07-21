@extends('layouts.app')

@section('title','Admin Login')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-body p-5">

                    <h2 class="text-center mb-4">

                        EcoMart Admin

                    </h2>

                    @if($errors->any())

                        <div class="alert alert-danger">

                            {{ $errors->first() }}

                        </div>

                    @endif

                    <form method="POST"
                          action="{{ route('admin.login.submit') }}">

                        @csrf

                        <div class="mb-3">

                            <label>Username</label>

                            <input
                                type="text"
                                name="username"
                                class="form-control">

                        </div>

                        <div class="mb-4">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control">

                        </div>

                        <button class="btn btn-success w-100">

                            Login

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection