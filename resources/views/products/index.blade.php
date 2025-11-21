@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <!-- Hero Section -->
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_10_16 PM.png') }}" alt="Luxury products hero"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.products.hero_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Products</li>
            </ol>
        </div>
    </div>
  
    @php
        $activeCategory = $categoryId ?? request('category');
    @endphp

    <div class="container-xxl py-5">
        <div class="text-center mb-4">
            <h5 class="akg-section-label">{{ __('messages.home.products_label') }}</h5>
            <h2 class="akg-section-head">{{ __('messages.products.browse') }}</h2>
        </div>

        <ul class="nav akg-tabs justify-content-center mb-5 flex-wrap">
            <li class="nav-item">
                <a class="nav-link {{ $activeCategory ? '' : 'active' }}" href="{{ route('products.index') }}">
                    {{ __('messages.products.all') }}
                </a>
            </li>
            @foreach ($categories as $category)
                <li class="nav-item">
                    <a class="nav-link {{ (string) $activeCategory === (string) $category->id ? 'active' : '' }}"
                        href="{{ route('products.index', ['category' => $category->id]) }}">
                        {{ ucfirst($category->name) }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="row g-4">

            @forelse ($products as $item)
                <div class="col-md-3">

                    <div class="akg-card h-100">

                        {{-- IMAGE --}}
                        @php
                            $img = $item->image
                                ? asset('storage/' . $item->image)
                                : ($item->images->first()
                                    ? asset('storage/' . $item->images->first()->image)
                                    : asset('assets/img/default.jpg'));
                        @endphp

                        <img src="{{ $img }}" class="card-img-top" style="height: 200px; object-fit: cover;"
                            alt="{{ $item->title }}" loading="lazy">

                        <div class="card-body">

                            <h5 class="text-gold fw-bold">{{ $item->title }}</h5>

                            <p class="small text-muted mb-1">
                                Category: {{ $item->category->name ?? '—' }}
                            </p>

                            <p class="fw-bold text-gold">${{ number_format($item->price, 2) }}</p>

                            <a href="{{ route('products.show', $item->id) }}" class="btn btn-gold w-100 fw-bold">
                                {{ __('messages.products.view_details') }}
                            </a>

                        </div>

                    </div>

                </div>
            @empty
                <div class="col-12">
                    <div class="akg-card text-center py-5">
                        <h5 class="text-gold mb-2">{{ __('messages.products.no_products') }}</h5>
                        <p class="text-muted mb-3">{{ __('messages.products.no_products_hint') }}</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a class="btn btn-gold" href="{{ route('products.index') }}">{{ __('messages.products.all') }}</a>
                            <a class="btn btn-outline-gold" href="{{ route('projects.index') }}">{{ __('messages.products.see_all_projects') }}</a>
                        </div>
                    </div>
                </div>
            @endforelse

        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
