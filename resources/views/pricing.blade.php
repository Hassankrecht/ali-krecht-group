@extends('layouts.app')

@section('title', __('messages.nav.pricing') ?? 'Pricing')
@section('meta_description', 'See transparent pricing for all Ali Krecht Group luxury carpentry and interior design services in the UAE.')

@php
    $lang = app()->getLocale();
    $services = $services ?? collect();
    $activeSlug = optional($services->first())['slug'] ?? null;
    $ctaText = __('messages.services_page.cta');
    if ($ctaText === 'messages.services_page.cta') {
        $ctaText = $lang === 'ar' ? 'احجز استشارة' : 'Book Consultation';
    }
    $quoteText = $lang === 'ar' ? 'اطلب عرض سعر' : 'Request Quote';
    $disclaimer = $lang === 'ar'
        ? 'الأسعار تقديرية وتتغير حسب المساحة، المواد، والتفاصيل. يتم تقديم عرض سعر نهائي بعد زيارة موقع وقياسات دقيقة.'
        : 'Pricing is indicative and varies by scope, materials, and complexity. Final quote after site visit and measurements.';
    $whyPricing = [
        $lang === 'ar' ? 'المساحة وحجم العمل' : 'Scope & area',
        $lang === 'ar' ? 'المواد والتشطيبات' : 'Materials & finishes',
        $lang === 'ar' ? 'التفاصيل والتفصيل' : 'Detailing & customization',
        $lang === 'ar' ? 'الجدول الزمني والموقع' : 'Timeline & site access',
    ];
@endphp

@section('content')
    @push('head')
        <style>
            .pricing-hero .akg-hero-overlay {
                background: linear-gradient(120deg, rgba(12, 12, 12, 0.7), rgba(0, 0, 0, 0.55));
            }
            .pricing-nav .nav-link {
                border: 1px solid rgba(255, 215, 128, 0.35);
                border-radius: 999px;
                color: #c7954b;
                font-weight: 600;
                background: rgba(199, 149, 75, 0.06);
            }
            .pricing-nav .nav-link.active {
                background: linear-gradient(90deg, #c7954b, #d8aa65);
                color: #0f172a;
                border-color: transparent;
            }
            .pricing-summary {
                border-left: 3px solid #c7954b;
            }
            .pricing-chip {
                display: inline-block;
                background: rgba(199, 149, 75, 0.14);
                color: #c7954b;
                border-radius: 999px;
                padding: 4px 10px;
                font-weight: 600;
                font-size: 0.85rem;
            }
        </style>
    @endpush

    <div class="akg-hero-img-box position-relative">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_12_34 PM.png') }}" class="akg-hero-img" alt="Pricing" loading="lazy">
        <div class="akg-hero-overlay"></div>
        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-2">
                {{ __('messages.nav.pricing') ?? 'Pricing & Estimates' }}
            </h1>
            <p class="text-light small">{{ $disclaimer }}</p>
        </div>
    </div>

    <section class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="text-center mb-4">
                <h2 class="akg-section-head">{{ __('messages.services_page.offer_head') ?? 'Services & Estimates' }}</h2>
                <p class="text-muted small">{{ $disclaimer }}</p>
                <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                    <a href="{{ route('contact') }}" class="btn btn-gold px-4">{{ $ctaText }}</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-gold px-4">{{ $quoteText }}</a>
                </div>
            </div>

            <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav pricing-nav">
                <div class="pricing-chip mb-2">{{ $lang === 'ar' ? 'اختر الخدمة' : 'Choose a service' }}</div>
                <ul class="nav nav-pills justify-content-center flex-wrap gap-2" id="pricingNav">
                    @foreach($services as $service)
                        <li class="nav-item">
                            <a class="nav-link {{ $service['slug'] === $activeSlug ? 'active' : '' }}"
                               data-bs-toggle="pill"
                               href="#pricing-{{ $service['slug'] }}">
                                {{ $service['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content">
                @foreach($services as $service)
                    <div id="pricing-{{ $service['slug'] }}"
                         class="tab-pane fade {{ $service['slug'] === $activeSlug ? 'show active' : '' }}">
                        <div class="akg-card p-4 mb-4 pricing-summary">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                                <div class="mb-3 mb-lg-0">
                                    <div class="pricing-chip mb-2"><i class="fa fa-tags me-1"></i> {{ $lang === 'ar' ? 'عرض سريع' : 'Quick Estimate' }}</div>
                                    <h4 class="text-gold mb-2">{{ $service['title'] }}</h4>
                                    <p class="text-muted mb-1">{{ $service['highlight'] }}</p>
                                    <small class="text-muted">{{ $lang === 'ar' ? 'السعر النهائي بعد المعاينة' : 'Final quote after site visit' }}</small>
                                </div>
                                <div class="text-lg-end">
                                    <p class="text-gold fw-bold mb-1">
                                        {{ $lang === 'ar' ? 'من — حسب المواد والتفاصيل' : 'From — depends on materials & scope' }}
                                    </p>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('contact') }}" class="btn btn-gold px-3">{{ $quoteText }}</a>
                                        <a href="{{ route('contact') }}" class="btn btn-outline-gold px-3">{{ $ctaText }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="akg-card h-100 p-3">
                                    <h6 class="text-gold mb-3">{{ __('messages.services_page.included') ?? 'What is included' }}</h6>
                                    <ul class="text-muted small mb-0 ps-0 list-unstyled">
                                        @forelse($service['features'] as $f)
                                            <li class="mb-2 d-flex align-items-start gap-2">
                                                <i class="fa fa-check text-gold mt-1"></i>
                                                <span>{{ $f }}</span>
                                            </li>
                                        @empty
                                            <li class="mb-2 d-flex align-items-start gap-2"><i class="fa fa-check text-gold mt-1"></i><span>{{ __('messages.services_page.default_feature_1') ?? 'Tailored design and quality build.' }}</span></li>
                                            <li class="mb-2 d-flex align-items-start gap-2"><i class="fa fa-check text-gold mt-1"></i><span>{{ __('messages.services_page.default_feature_2') ?? 'Premium materials with luxury finishes.' }}</span></li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="akg-card h-100 p-3">
                                    <h6 class="text-gold mb-3">{{ __('messages.services_page.process') ?? 'How we work' }}</h6>
                                    <ol class="text-muted small mb-0 ps-3">
                                        @forelse($service['process'] as $p)
                                            <li class="mb-2 d-flex align-items-start gap-2">
                                                <i class="fa fa-circle-dot text-gold mt-1"></i>
                                                <span>{{ $p }}</span>
                                            </li>
                                        @empty
                                            <li class="mb-1">{{ __('messages.services_page.default_process_1') ?? 'Discovery & requirements.' }}</li>
                                            <li class="mb-1">{{ __('messages.services_page.default_process_2') ?? 'Concept, approvals, and BOQ.' }}</li>
                                            <li class="mb-1">{{ __('messages.services_page.default_process_3') ?? 'Execution, QA, and handover.' }}</li>
                                        @endforelse
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="akg-newcard mt-5 p-4">
                <h5 class="text-gold mb-3">{{ $lang === 'ar' ? 'كيف نحدد السعر؟' : 'How we price' }}</h5>
                <div class="row g-3">
                    @foreach($whyPricing as $item)
                        <div class="col-md-3 col-6">
                            <div class="akg-card text-center py-3 h-100">
                                <p class="text-muted small mb-0">{{ $item }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-muted small mt-3 mb-0">{{ $disclaimer }}</p>
            </div>
        </div>
    </section>
@endsection