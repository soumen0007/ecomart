@extends('admin.layouts.admin')

@section('title','Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold text-success">Dashboard</h2>
        <p class="text-muted mb-0">
            Welcome back! Here's what's happening at EcoMart.
        </p>
    </div>

</div>

<div class="row g-4 mb-5">

    <div class="col-md-3">
        <div class="stat-card">
            <h2>{{ $products }}</h2>
            <small class="text-muted">TOTAL PRODUCTS</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h2>{{ $categories }}</h2>
            <small class="text-muted">CATEGORIES</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h2>{{ $users }}</h2>
            <small class="text-muted">CUSTOMERS</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h2>{{ $lowStock }}</h2>
            <small class="text-muted">LOW STOCK</small>
        </div>
    </div>

</div>

<div class="row g-4 mb-5">

    <div class="col-md-3">

        <a href="{{ route('admin.products.create') }}" class="action-card">

            <i class="bi bi-plus-circle-fill"></i>

            <h5 class="mt-3">Add Product</h5>

            <small class="text-muted">
                Create new products
            </small>

        </a>

    </div>

    <div class="col-md-3">

        <a href="{{ route('admin.products') }}" class="action-card">

            <i class="bi bi-box-seam-fill"></i>

            <h5 class="mt-3">Manage Products</h5>

            <small class="text-muted">
                View & edit inventory
            </small>

        </a>

    </div>

    <div class="col-md-3">

        <a href="{{ route('admin.categories') }}" class="action-card">

            <i class="bi bi-tags-fill"></i>

            <h5 class="mt-3">Categories</h5>

            <small class="text-muted">
                Browse categories
            </small>

        </a>

    </div>

    <div class="col-md-3">

        <a href="{{ url('/') }}" class="action-card">

            <i class="bi bi-shop"></i>

            <h5 class="mt-3">View Store</h5>

            <small class="text-muted">
                Open customer website
            </small>

        </a>

    </div>

</div>

<div class="row g-4">

    <div class="col-lg-5">

        <div class="panel">

            <div class="panel-header">
                Product Distribution
            </div>

            <div class="p-4">

                @foreach($categoryStats as $category)

                    @php
                        $percent = $products > 0
                            ? round(($category->products_count / $products) * 100)
                            : 0;
                    @endphp

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <strong>{{ $category->name }}</strong>

                            <span>

                                {{ $category->products_count }}

                            </span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar bg-success"
                                 style="width:{{ $percent }}%">

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

    <div class="col-lg-7">

        <div class="panel">

            <div class="panel-header">

                Recently Added Products

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>

                    <tr>

                        <th>Image</th>

                        <th>Name</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Stock</th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($recentProducts as $product)

                        <tr>

                            <td>

                                <img src="{{ $product->image_url }}"
                                     width="55"
                                     class="rounded">

                            </td>

                            <td>

                                {{ $product->name }}

                            </td>

                            <td>

                                <span class="badge bg-success">

                                    {{ $product->category->name }}

                                </span>

                            </td>

                            <td>

                                ${{ number_format($product->price,2) }}

                            </td>

                            <td>

                                {{ $product->stock }}

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection