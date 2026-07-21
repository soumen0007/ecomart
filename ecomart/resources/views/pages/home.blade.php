@extends('layouts.app')

@section('title', 'Home · EcoMart')

@section('content')

<section class="hero"
         style="background-image: linear-gradient(rgba(52,199,89,0.75), rgba(27,67,50,0.75)), url('{{ asset('assets/images/hero-bg.jpg') }}');">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-light text-success mb-3">Hamilton · New Zealand</span>
                <h1>Hamilton's #1 Organic Choice.</h1>
                <p>Welcome to EcoMart — your neighbourhood organic grocery store. Browse our hand-picked selection of fresh produce, pantry staples, and eco-friendly goods, then build a shopping list for your next in-store visit.</p>
                <div class="mt-4">
                    <a href="#products" class="btn btn-light btn-lg me-2"><i class="bi bi-bag-heart"></i> Shop Now</a>
                    <a href="#categories" class="btn btn-outline-light btn-lg">Browse Categories</a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ asset('assets/images/logo.jpg') }}" alt="EcoMart"
                     style="filter: drop-shadow(0 8px 20px rgba(0,0,0,0.3)); max-width: 320px; background: rgba(255,255,255,0.85); border-radius: 1rem; padding: 1rem;">
            </div>
        </div>
    </div>
</section>

<section class="container text-center my-5">
    <div class="section-divider"></div>
    <h2>Kia ora, welcome to EcoMart</h2>
    <p class="lead">
        We believe healthy living should be simple, local, and kind to the planet.
        Every product on our shelves is sourced from trusted local growers and ethical suppliers.
    </p>
</section>

<section id="categories" class="container my-5">
    <div class="section-heading">
        <div class="section-divider"></div>
        <h2>Shop by Category</h2>
        <p>Explore our range — from crisp fresh produce to planet-friendly household essentials.</p>
    </div>

    <div class="row g-4">
        @foreach($categories as $category)
            @php
                $icons = [
                    'fruits' => 'apple',
                    'vegetables' => 'tree',
                    'pantry' => 'basket',
                    'eco-friendly' => 'recycle',
                    'dairy' => 'egg',
                    'beverages' => 'cup-straw',
                ];
                $icon = $icons[$category->slug] ?? 'shop';
            @endphp

            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('home') }}?category={{ $category->slug }}#products" class="category-card">
                    <div class="cat-icon">
                        <i class="bi bi-{{ $icon }}"></i>
                    </div>
                    <strong>{{ $category->name }}</strong>
                </a>
            </div>
        @endforeach
    </div>
</section>

<section id="products" class="container my-5">
    <div class="section-heading">
        <div class="section-divider"></div>
        <h2>Featured Products</h2>
        <p>A taste of what's on our shelves this week.</p>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <article class="product-card">
                    <a href="{{ route('product', $product->id) }}" class="product-thumb">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    </a>

                    <div class="card-body">
                        <span class="product-category">
                            {{ $product->category->name ?? 'Product' }}
                        </span>

                        <h3 class="product-name">
                            <a href="{{ route('product', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>

                        @if($product->is_organic)
                            <span class="organic-badge">Certified Organic</span>
                        @endif

                        <p class="product-desc">
                            {{ $product->description }}
                        </p>

                        <div class="product-price">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="icon"><i class="bi bi-search"></i></div>
                    <h4>No products found</h4>
                    <p>Please check back soon — our catalogue is updated weekly.</p>
                </div>
            </div>
        @endforelse
    </div>
</section>

<section class="container my-5">
    <div class="row g-4 align-items-center bg-white p-4 p-md-5 rounded-4 shadow-sm">
        <div class="col-md-4 text-center">
            <div style="font-size: 4rem; color: var(--ecomart-secondary);"><i class="bi bi-flower1"></i></div>
            <h4>100% Organic</h4>
            <p class="text-muted small mb-0">Certified produce grown without synthetic pesticides or fertilisers.</p>
        </div>

        <div class="col-md-4 text-center">
            <div style="font-size: 4rem; color: var(--ecomart-secondary);"><i class="bi bi-geo-alt"></i></div>
            <h4>Locally Sourced</h4>
            <p class="text-muted small mb-0">Supporting Waikato growers and reducing food miles.</p>
        </div>

        <div class="col-md-4 text-center">
            <div style="font-size: 4rem; color: var(--ecomart-secondary);"><i class="bi bi-recycle"></i></div>
            <h4>Eco-Friendly</h4>
            <p class="text-muted small mb-0">Plastic-free packaging and reusable options for every shopper.</p>
        </div>
    </div>
</section>

@endsection