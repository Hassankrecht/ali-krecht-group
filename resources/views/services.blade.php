@extends('layouts.app')

@section('title', __('messages.services_page.hero_title'))
@section('meta_description', 'Explore our range of luxury carpentry, interior design, and bespoke woodwork services for homes and businesses in the UAE.')

@section('content')

    {{-- ================= HERO ================= --}}
    <div class="akg-hero-img-box position-relative">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_12_34 PM.png') }}" class="akg-hero-img"
            alt="{{ __('messages.services_page.hero_title') }}" loading="lazy">
        <div class="akg-hero-overlay"></div>

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.services_page.hero_title') }}</h1>
            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.nav.services') }}</li>
            </ol>
        </div>
    </div>

    {{-- ================= INTRO ================= --}}
    <section class="container-xxl py-5">
        <div class="container text-center">
            <h5 class="akg-section-label">{{ __('messages.services_page.intro_label') }}</h5>
            <h2 class="akg-section-head mb-4">{{ __('messages.services_page.intro_head') }}</h2>
            <p class="text-muted col-lg-8 mx-auto" style="font-size:1.1rem;">
                {{ __('messages.services_page.intro_body') }}
            </p>
        </div>
    </section>

    {{-- ================= SERVICES GRID ================= --}}
    <style>
        .akg-service-card {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 24px;
            gap: 10px;
        }
        .akg-card-icon {
            font-size: 34px;
        }
        .akg-line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .akg-service-excerpt {
            line-height: 1.5;
        }
        .akg-service-card .btn {
            margin-top: auto;
        }
    </style>

    <section id="services" class="container-xxl py-5">
        <div class="container">

            <div class="text-center mb-5">
                <h5 class="akg-section-label">{{ __('messages.services_page.hero_title') }}</h5>
                <h2 class="akg-section-head mb-2">{{ __('messages.services_page.offer_head') }}</h2>
                <p class="text-muted small">
                    {{ __('messages.services_page.intro_body') }}
                </p>
            </div>

            <div class="row g-4">
                @foreach ($services as $service)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="akg-card akg-service-card">

                            {{-- ICON --}}
                            @if(!empty($service['icon']))
                                <i class="fa {{ $service['icon'] }} akg-card-icon"></i>
                            @endif

                            {{-- TITLE --}}
                            <h4 class="text-gold mt-3">
                                {{ $service['title'] ?? 'Service' }}
                            </h4>

                            {{-- EXCERPT --}}
                            @php
                                $excerpt = trim($service['excerpt'] ?? '');
                                $shortExcerpt = \Illuminate\Support\Str::limit($excerpt, 70, '');
                            @endphp

                            <p class="text-muted small mb-2 akg-line-clamp-2 akg-service-excerpt">
                                {{ $shortExcerpt }}
                            </p>

                            {{-- VIEW MORE BUTTON --}}
                            @php
                                $viewMoreText = __('messages.services_page.view_more');
                                if ($viewMoreText === 'messages.services_page.view_more') {
                                    $viewMoreText = app()->getLocale() === 'ar' ? 'عرض المزيد' : 'View More';
                                }
                            @endphp
                            <a href="{{ route('services.show', $service['slug']) }}"
                               class="btn btn-outline-gold btn-sm fw-bold">
                                {{ $viewMoreText }}
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ================= WHY CHOOSE US ================= --}}
    <section class="container-xxl py-5">
        <div class="container">
            <h2 class="akg-section-head text-center mb-5">{{ __('messages.services_page.why_head') }}</h2>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-award akg-card-icon"></i>
                        <h5 class="text-gold">{{ __('messages.services_page.why_quality') }}</h5>
                        <p class="text-muted small">{{ __('messages.services_page.why_quality_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-users-gear akg-card-icon"></i>
                        <h5 class="text-gold">{{ __('messages.services_page.why_team') }}</h5>
                        <p class="text-muted small">{{ __('messages.services_page.why_team_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-clock akg-card-icon"></i>
                        <h5 class="text-gold">{{ __('messages.services_page.why_time') }}</h5>
                        <p class="text-muted small">{{ __('messages.services_page.why_time_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-handshake akg-card-icon"></i>
                        <h5 class="text-gold">{{ __('messages.services_page.why_trusted') }}</h5>
                        <p class="text-muted small">{{ __('messages.services_page.why_trusted_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="container-xxl py-5">
        <div class="container text-center">
            <h2 class="text-gold fw-bold mb-3">{{ __('messages.home.cta_quote') }}</h2>
            <p class="text-muted mb-4">
                {{ __('messages.services_page.intro_body') }}
            </p>
            @php
                $ctaText = __('messages.services_page.cta');
                if ($ctaText === 'messages.services_page.cta') {
                    $ctaText = app()->getLocale() === 'ar' ? 'احجز استشارة' : 'Book Consultation';
                }
            @endphp
            <a href="{{ route('contact') }}" class="btn btn-gold px-5 py-3 fw-bold">
                {{ $ctaText }}
            </a>
        </div>
    </section>

@endsection
