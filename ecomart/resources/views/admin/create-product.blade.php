@extends('admin.layouts.admin')

@section('title','Add Product')

@section('content')

<h1>Add New Product</h1>
<p class="text-muted">Create a new organic grocery product</p>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="panel">
    <div class="panel-header">Product Information</div>

    <div class="p-4">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Choose Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Upload Product Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="is_organic" id="organic" class="form-check-input">
                <label for="organic" class="form-check-label">Certified Organic</label>
            </div>

            <button class="btn btn-green">
                <i class="bi bi-save"></i> Save Product
            </button>

            <a href="{{ route('admin.products') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>

@endsection