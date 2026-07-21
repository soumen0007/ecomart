@extends('admin.layouts.admin')

@section('title','Edit Product')

@section('content')

<h1>Edit Product</h1>
<p class="text-muted">Update existing product information</p>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="panel">
    <div class="panel-header">Product Information</div>

    <div class="p-4">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Current Image</label><br>
                <img src="{{ $product->image_url }}" width="120" class="rounded shadow-sm mb-3">

                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Leave blank to keep current image.</small>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" rows="5" class="form-control" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="is_organic" id="organic" class="form-check-input" @checked($product->is_organic)>
                <label for="organic" class="form-check-label">Certified Organic</label>
            </div>

            <button class="btn btn-green">
                <i class="bi bi-save"></i> Update Product
            </button>

            <a href="{{ route('admin.products') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>

@endsection