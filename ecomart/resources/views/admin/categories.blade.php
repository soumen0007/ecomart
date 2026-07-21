@extends('admin.layouts.admin')

@section('title','Manage Categories')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success">
            Back to Dashboard
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Total Products</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->description }}</td>
                            <td>{{ $category->products_count }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection