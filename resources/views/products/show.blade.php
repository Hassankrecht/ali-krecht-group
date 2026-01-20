@extends('layouts.app')

@section('title', ($product->title_localized ?? $product->title) . ' | ' . __('messages.products.hero_title'))
@section('meta_description', __('messages.meta.product_show_description', ['product' => $product->title_localized ?? $product->title]))

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_10_16 PM.png') }}" alt="{{ __('messages.product_show.breadcrumb') }}"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.product_show.breadcrumb') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('messages.nav.products') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ $product->title_localized ?? $product->title }}</li>
            </ol>
        </div>
    </div>
  
    @php
        $resolvePath = function ($path) {
            if (!$path) {
                return null;
            }
            return (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/'))
                ? asset($path)
                : asset('storage/' . $path);
        };

        $mainImage = $resolvePath($product->image)
            ?? ($product->images->first()
                ? $resolvePath($product->images->first()->image)
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
                        @php $thumb = $resolvePath($img->image); @endphp
                        @if ($thumb)
                        <img src="{{ $thumb }}"
                            onclick="document.getElementById('mainImage').src=this.src" class="rounded shadow"
                            style="width:90px;height:70px;object-fit:cover;cursor:pointer;"
                            alt="{{ $product->title }} thumbnail" loading="lazy">
                        @endif
                    @endforeach
                </div>

            </div>

            {{-- RIGHT INFO --}}
            <div class="col-lg-6">

                <h2 class="text-gold fw-bold">{{ $product->title_localized }}</h2>
                <h4 class="text-light mb-3">${{ number_format($product->price, 2) }}</h4>

                <p class="text-muted">{{ $product->description_localized }}</p>

                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold px-4 mt-3"
                            data-track-action="buy_click"
                            data-track-meta='@json(["product_id" => $product->id, "source" => "product_show"])'>
                        <i class="fa fa-cart-plus me-1"></i> {{ __('messages.product_show.add_to_cart') }}
                    </button>
                </form>

                <hr class="my-4 border-gold">

                {{-- SPECIFICATIONS --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.specs') }}</h4>

                <ul class="list-group mb-4">
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.category_label') }}:</strong> {{ $product->category->name_localized ?? __('messages.common.na') }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.sku_label') }}:</strong> {{ $product->id }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.price_label') }}:</strong> ${{ number_format($product->price, 2) }}
                    </li>
                </ul>

                {{-- FEATURES --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.features') }}</h4>

                <ul class="text-light mb-4">
                    <li>✔ {{ __('messages.product_show.feature_1') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_2') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_3') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_4') }}</li>
                </ul>

                {{-- SUITABLE FOR --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.best_for') }}</h4>

                <p class="text-muted">
                    {{ __('messages.product_show.best_for_desc') }}
                </p>

            </div>

        </div>

    </div>
@endsection
