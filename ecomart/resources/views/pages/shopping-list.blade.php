@extends('layouts.app')

@section('title', 'Shopping List · EcoMart')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-basket-fill text-success"></i>
            My Shopping List
        </h2>

        <a href="{{ route('home') }}#products"
           class="btn btn-outline-success">

            Continue Shopping

        </a>

    </div>

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if($items->count())

        @php
            $grandTotal = 0;
        @endphp

        <div class="card border-0 shadow rounded-4">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-success">

                        <tr>

                            <th>Image</th>

                            <th>Product</th>

                            <th>Price</th>

                            <th>Quantity</th>

                            <th>Total</th>

                            <th width="120"></th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($items as $item)

                        @php

                            $total = $item->product->price * $item->quantity;

                            $grandTotal += $total;

                        @endphp

                        <tr>

                            <td width="90">

                                <img
                                    src="{{ $item->product->image_url }}"
                                    class="img-fluid rounded"
                                    width="70">

                            </td>

                            <td>

                                <strong>

                                    {{ $item->product->name }}

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $item->product->category->name }}

                                </small>

                            </td>

                            <td>

                                ${{ number_format($item->product->price,2) }}

                            </td>

                            <td>

                                {{ $item->quantity }}

                            </td>

                            <td>

                                <strong>

                                    ${{ number_format($total,2) }}

                                </strong>

                            </td>

                            <td>

                                <form method="POST"
                                      action="{{ route('shopping.remove',$item->id) }}">

                                    @csrf

                                    <button class="btn btn-danger btn-sm">

                                        <i class="bi bi-trash"></i>

                                        Remove

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        <div class="text-end mt-4">

            <h3>

                Grand Total :

                <span class="text-success">

                    ${{ number_format($grandTotal,2) }}

                </span>

            </h3>

        </div>

    @else

        <div class="card shadow border-0 rounded-4">

            <div class="card-body text-center py-5">

                <i class="bi bi-cart-x display-1 text-muted"></i>

                <h3 class="mt-3">

                    Your shopping list is empty.

                </h3>

                <p class="text-muted">

                    Browse our products and start adding your favourites.

                </p>

                <a href="{{ route('home') }}#products"
                   class="btn btn-success">

                    Shop Now

                </a>

            </div>

        </div>

    @endif

</div>

@endsection