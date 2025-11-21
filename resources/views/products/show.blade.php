@extends('layouts.app')

@section('title', $product->title . ' | ' . __('messages.products.hero_title'))

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_10_16 PM.png') }}" alt="Product detail"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.product_show.breadcrumb') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                <li class="breadcrumb-item text-light active">{{ $product->title }}</li>
            </ol>
        </div>
    </div>
  
    @php
        $mainImage = $product->image
            ? asset('storage/' . $product->image)
            : ($product->images->first()
                ? asset('storage/' . $product->images->first()->image)
                : asset('assets/img/default.jpg'));
    @endphp

    <div class="container-xxl py-5">
        <div class="row g-5">

            {{-- LEFT GALLERY --}}
            <div class="col-lg-6">

                <img id="mainImage" src="{{ $mainImage }}" class="img-fluid rounded shadow mb-3"
                    style="max-height:450px; object-fit:cover;" alt="{{ $product->title }}" loading="lazy">

                <div class="d-flex flex-wrap gap-2">
                    <img src="{{ $mainImage }}" onclick="document.getElementById('mainImage').src=this.src"
                        class="rounded shadow" style="width:90px;height:70px;object-fit:cover;cursor:pointer;"
                        alt="{{ $product->title }}" loading="lazy">
                    @foreach ($product->images as $img)
                        <img src="{{ asset('storage/' . $img->image) }}"
                            onclick="document.getElementById('mainImage').src=this.src" class="rounded shadow"
                            style="width:90px;height:70px;object-fit:cover;cursor:pointer;"
                            alt="{{ $product->title }} thumbnail" loading="lazy">
                    @endforeach
                </div>

            </div>

            {{-- RIGHT INFO --}}
            <div class="col-lg-6">

                <h2 class="text-gold fw-bold">{{ $product->title }}</h2>
                <h4 class="text-light mb-3">${{ number_format($product->price, 2) }}</h4>

                <p class="text-muted">{{ $product->description }}</p>

                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-gold text-dark fw-semibold px-4 mt-3">
                        <i class="fa fa-cart-plus me-1"></i> {{ __('messages.product_show.add_to_cart') }}
                    </button>
                </form>

                <hr class="my-4 border-gold">

                {{-- SPECIFICATIONS --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.specs') }}</h4>

                <ul class="list-group mb-4">
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>SKU:</strong> {{ $product->id }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>Price:</strong> ${{ number_format($product->price, 2) }}
                    </li>
                </ul>

                {{-- FEATURES --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.features') }}</h4>

                <ul class="text-light mb-4">
                    <li>✔ Premium materials & finish</li>
                    <li>✔ Custom sizing available</li>
                    <li>✔ Durable construction</li>
                    <li>✔ Dedicated after-sales support</li>
                </ul>

                {{-- SUITABLE FOR --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.best_for') }}</h4>

                <p class="text-muted">
                    Ideal for luxury interiors, bespoke installations, and high-end residential or commercial projects.
                </p>

            </div>

        </div>

    </div>
@endsection
