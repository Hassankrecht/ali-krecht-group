@extends('layouts.app')

@php
    $serviceTitle   = $service['title']   ?? __('messages.services_show.default_title');
    $serviceExcerpt = $service['excerpt'] ?? '';
    $heroImg        = asset('assets/img/ChatGPT Image Nov 7, 2025, 12_12_34 PM.png');
    $features       = $service['features']       ?? [];
    $processSteps   = $service['process_steps']  ?? [];
    $highlightText  = $service['highlight']      ?? $serviceExcerpt;
    $ctaText        = $service['cta']            ?? __('messages.services_show.cta');

    $gallery        = $service['gallery'] ?? [];
    $categorySlug   = $service['category_slug'] ?? null;
@endphp

@section('title', $serviceTitle)
@section('meta_description', $serviceExcerpt ?: __('messages.meta.service_description', ['service' => $serviceTitle]))
@section('og_title', $serviceTitle)
@section('og_description', $serviceExcerpt)
@section('og_image', $heroImg)

@section('content')

    {{-- ================= HERO ================= --}}
    <div class="akg-hero-img-box position-relative">
        <img src="{{ $heroImg }}" class="akg-hero-img" alt="{{ $serviceTitle }}" loading="lazy">
        <div class="akg-hero-overlay"></div>

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ $serviceTitle }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services') }}">{{ __('messages.nav.services') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ $serviceTitle }}</li>
            </ol>
        </div>
    </div>


    {{-- ================= OVERVIEW ================= --}}
    <section class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">

                <div class="col-lg-6">
                    <img src="{{ $heroImg }}" class="img-fluid rounded shadow" alt="{{ $serviceTitle }}" loading="lazy">
                </div>

                <div class="col-lg-6">
                    <h5 class="akg-section-label">{{ __('messages.services_page.intro_label') }}</h5>

                    <h2 class="akg-section-head mb-3">{{ $serviceTitle }}</h2>

                    @if($serviceExcerpt)
                        <p class="text-muted" style="font-size:1.05rem; line-height:1.7;">
                            {{ $serviceExcerpt }}
                        </p>
                    @endif

                    @php
                        $projectsLink = $categorySlug
                            ? route('projects.index', ['category' => $categorySlug])
                            : route('projects.index');
                    @endphp

                    <a href="{{ $projectsLink }}" class="btn btn-gold px-4 mt-3 fw-bold">
                        {{ $ctaText }}
                    </a>
                </div>

            </div>
        </div>
    </section>


    {{-- ================= FEATURES ================= --}}
    @if(!empty($features))
        <section class="container-xxl py-4">
            <div class="container akg-newcard p-4">
                <h3 class="text-gold mb-3">{{ __('messages.services_page.included') }}</h3>

                <div class="akg-why-box mt-2">
                    @foreach($features as $feature)
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>{{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ================= PROCESS ================= --}}
    @if(!empty($processSteps))
        <section class="container-xxl py-4">
            <div class="container akg-newcard p-4">
                <h3 class="text-gold mb-3">{{ __('messages.services_page.process') }}</h3>

                <ol class="text-muted small ps-3" style="font-size:1rem;">
                    @foreach($processSteps as $step)
                        <li class="mb-2">{{ $step }}</li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif


    {{-- ================= HIGHLIGHT ================= --}}
    @if(!empty($highlightText))
        <section class="container-xxl py-4">
            <div class="container akg-newcard p-4">
                <h3 class="text-gold mb-3">{{ __('messages.services_show.why_matters') }}</h3>

                <p class="text-muted" style="font-size:1.07rem; line-height:1.8;">
                    {{ $highlightText }}
                </p>
            </div>
        </section>
    @endif


    {{-- ================= GALLERY ================= --}}
    @if(!empty($gallery))
        <section class="container-xxl py-5">
            <div class="container">
                <h3 class="text-gold mb-3">{{ __('messages.services_show.work_showcase') }}</h3>
                <p class="text-muted small mb-4">{{ __('messages.services_show.work_showcase_desc') }}</p>

                <div class="d-flex flex-wrap gap-3">
                    @foreach($gallery as $img)
                        <img src="{{ $heroImg }}"
                             class="rounded shadow"
                             style="height:170px; width:260px; object-fit:cover;"
                             alt="{{ $serviceTitle }}">
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ================= RELATED PROJECTS ================= --}}
    @if(isset($projects) && $projects->count())
        <section class="container-xxl py-5">
            <div class="container">

                <div class="d-flex justify-content-between mb-3">
                    <h3 class="text-gold mb-0">{{ __('messages.services_show.related_projects') }}</h3>

                    <a href="{{ route('projects.index') }}" class="btn btn-outline-gold btn-sm">
                        {{ __('messages.home.projects_all') }}
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($projects as $project)
                        <div class="col-lg-4 col-md-6">
                            <div class="akg-card h-100">

                                  <img src="{{ $heroImg }}"
                                     class="akg-project-img"
                                     loading="lazy"
                                     alt="{{ $project->title_localized }}">

                                <div class="p-3 text-center">
                                    <h5 class="text-gold">{{ $project->title_localized }}</h5>
                                    <p class="small text-muted">
                                        {{ Str::limit($project->description_localized, 80) }}
                                    </p>

                                    <a href="{{ route('projects.show', $project->id) }}"
                                       class="btn btn-outline-gold btn-sm">
                                        {{ __('messages.home.projects_view') }}
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif


    {{-- ================= RELATED PRODUCTS ================= --}}
    @if(isset($products) && $products->count())
        <section class="container-xxl py-5">
            <div class="container">

                <div class="d-flex justify-content-between mb-3">
                    <h3 class="text-gold mb-0">{{ __('messages.services_show.related_products') }}</h3>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-gold btn-sm">
                        {{ __('messages.home.products_title') }}
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($products as $product)
                        @php
                            $resolvePath = function ($path) {
                                if (!$path) {
                                    return null;
                                }
                                return (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/'))
                                    ? asset($path)
                                    : asset('storage/' . $path);
                            };

                            $img = $resolvePath($product->image)
                                ?? ($product->images->first()
                                    ? $resolvePath($product->images->first()->image)
                                    : asset('assets/img/default.jpg'));
                        @endphp

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="akg-product-card h-100">

                                <div class="akg-product-img-box">
                                     <img src="{{ $heroImg }}"
                                         class="akg-product-img"
                                         alt="{{ $product->title_localized }}"
                                         loading="lazy">
                                </div>

                                <div class="akg-product-info text-center">
                                    <h5 class="akg-product-title">{{ $product->title_localized }}</h5>
                                    <p class="akg-product-desc">
                                        {{ Str::limit($product->description_localized, 55) }}
                                    </p>

                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="btn-gold-small">{{ __('messages.home.products_view') }}</a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif

@endsection
