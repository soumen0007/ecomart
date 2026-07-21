@extends('layouts.app')

@section('title', $product->name . ' · EcoMart')

@section('content')

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-5 align-items-center bg-white p-4 rounded-4 shadow-sm">

        <!-- Product Image -->

        <div class="col-md-6 text-center">

            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="img-fluid rounded-4 shadow-sm">

        </div>

        <!-- Product Details -->

        <div class="col-md-6">

            <span class="badge bg-success mb-3">

                {{ $product->category->name }}

            </span>

            <h1 class="mb-3">

                {{ $product->name }}

            </h1>

            <h2 class="text-success mb-3">

                ${{ number_format($product->price,2) }}

            </h2>

            <p class="lead">

                {{ $product->description }}

            </p>

            <div class="mb-3">

                <strong>Available Stock :</strong>

                {{ $product->stock }}

            </div>

            <div class="mb-4">

                <strong>Organic :</strong>

                @if($product->is_organic)

                    <span class="badge bg-success">

                        Yes

                    </span>

                @else

                    <span class="badge bg-secondary">

                        No

                    </span>

                @endif

            </div>

            @auth

                <form method="POST"
                      action="{{ route('shopping.add',$product->id) }}"
                      class="d-inline">

                    @csrf

                    <button class="btn btn-success btn-lg">

                        <i class="bi bi-basket"></i>

                        Add to Shopping List

                    </button>

                </form>

            @else

                <a href="{{ route('login') }}"
                   class="btn btn-success btn-lg">

                    <i class="bi bi-box-arrow-in-right"></i>

                    Login to Purchase

                </a>

            @endauth

            <a href="{{ route('home') }}#products"
               class="btn btn-outline-success btn-lg ms-2">

                Back to Products

            </a>

        </div>

    </div>

</div>

@endsection