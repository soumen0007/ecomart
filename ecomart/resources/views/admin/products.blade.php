@extends('admin.layouts.admin')

@section('title','Product Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Product Management</h1>
        <p class="text-muted">Manage your organic grocery inventory</p>
    </div>

    <a href="{{ route('admin.products.create') }}" class="btn btn-green">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="panel mb-4">
    <div class="p-4">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search products...">
            </div>

            <div class="col-md-3">
                <select class="form-select">
                    <option>All Categories</option>
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select">
                    <option>Name A-Z</option>
                    <option>Price Low-High</option>
                    <option>Stock Low-High</option>
                </select>
            </div>

            <div class="col-md-1">
                <button class="btn btn-green w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel-header">Products</div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>IMAGE</th>
                    <th>NAME</th>
                    <th>CATEGORY</th>
                    <th>PRICE</th>
                    <th>STOCK</th>
                    <th>ORGANIC</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <img src="{{ $product->image_url }}" width="60" class="rounded">
                        </td>

                        <td><strong>{{ $product->name }}</strong></td>

                        <td>
                            <span class="badge bg-success">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </td>

                        <td>${{ number_format($product->price,2) }}</td>

                        <td>{{ $product->stock }}</td>

                        <td>
                            @if($product->is_organic)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('product', $product->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.products.delete', $product->id) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf

                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection