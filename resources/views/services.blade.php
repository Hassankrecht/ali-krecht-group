@extends('layouts.app')

@section('title', 'Our Services')

@section('content')

    <div class="akg-hero-img-box position-relative">

        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_12_34 PM.png') }}" class="akg-hero-img"
            alt="Services hero" loading="lazy">

        <div class="akg-hero-overlay"></div>

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">Our Services</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Services</li>
            </ol>
        </div>

    </div>




    {{-- ======================================
      INTRO SECTION
====================================== --}}
    <section class="container-xxl py-5">
        <div class="container text-center">

            <h5 class="akg-section-label">What We Do</h5>

            <h2 class="akg-section-head mb-4">Crafting Excellence in Every Detail</h2>

            <p class="text-muted col-lg-8 mx-auto" style="font-size:1.1rem;">
                At <span class="text-gold fw-bold">Ali Krecht Group</span>, we bring together craftsmanship,
                innovation, and luxury to deliver exceptional services in construction, carpentry and interior design.
            </p>

        </div>
    </section>


    {{-- ======================================
      MAIN SERVICES GRID (Luxury Cards)
====================================== --}}
    <section id="services" class="container-xxl py-5">
        <div class="container">

            <div class="text-center mb-5">
                <h5 class="akg-section-label">Our Services</h5>
                <h2 class="akg-section-head mb-2">What We Offer</h2>
                <p class="text-muted small">
                    From structure to finishing, we deliver complete solutions with luxury craftsmanship.
                </p>
            </div>

            @php
                $services = [
                    [
                        'icon' => 'fa-screwdriver-wrench',
                        'title' => 'Carpentry & Woodwork',
                        'short' => 'Doors, kitchens, wardrobes, furniture & luxury wood finishes.',
                        'full' => 'At Ali Krecht Group, carpentry is not just a craft — it is precision, art, and heritage.
                            We design and manufacture luxury woodwork pieces including bespoke doors, kitchens, wardrobes,
                            and furniture with unmatched attention to detail. Every cut, every joint, and every finish reflects
                            decades of experience and mastery. Using premium materials and advanced techniques, we create wooden 
                            elements that elevate your home or business with warmth, elegance, and durability. Whether you desire 
                            modern sleek lines or rich traditional styles, our team transforms your vision into refined reality.',
                    ],
                    [
                        'icon' => 'fa-hard-hat',
                        'title' => 'Construction & Renovation',
                        'short' => 'Full building construction, renovation and structural work.',
                        'full' => 'Our construction and renovation services deliver strength, precision, and long-lasting quality.
                            From full villa builds to structural modifications and complete refurbishments, we manage every detail with engineering accuracy and strict safety standards. Our team ensures seamless coordination between planning, execution, and finishing, giving you a stress-free, reliable experience. Whether upgrading a single room or executing a major renovation, we transform your property with clean workmanship, strong foundations, and beautiful finishing — all aligned with modern architectural trends.',
                    ],
                    [
                        'icon' => 'fa-paint-roller',
                        'title' => 'Interior Decoration',
                        'short' => 'Modern, classic & luxury décor with premium finishing.',
                        'full' => 'Your space should reflect your identity — luxurious, modern, and thoughtfully crafted.
Our interior decoration service blends creativity with technical mastery to produce stylish, fully harmonized interiors. From color palettes to textures, lighting, wall finishes, and décor pieces, we combine design principles with your unique vision. Modern, classic, or luxury themes — we curate each detail to make your spaces elegant, functional, and comfortable. Our finishing experts ensure flawless surfaces, durable materials, and a premium seamless look.',
                    ],
                    [
                        'icon' => 'fa-compass-drafting',
                        'title' => 'Custom Design & Shop Drawings',
                        'short' => 'Tailored designs for homes, villas, offices & retail.',
                        'full' => 'Every space is unique — and so should be its design.
Our custom design service allows you to create personalized layouts, furniture pieces, décor elements, and functional areas that reflect your taste and lifestyle. We carefully study the space, optimize it, and develop tailored concepts using state-of-the-art design tools. Whether it’s a modern villa, a commercial space, or a boutique interior, we ensure every element is balanced, practical, and visually inspiring. With AKG, your ideas become high-end realities.',
                    ],
                    [
                        'icon' => 'fa-house-chimney',
                        'title' => 'Home & Villa Finishing',
                        'short' => 'Complete high-end finishing, ready for handover.',
                        'full' => 'Finishing is where luxury becomes visible.
We deliver complete home and villa finishing including gypsum works, flooring, ceilings, lighting, wall cladding, marble installation, painting, and more. Our team ensures perfectly smooth finishes, accurate alignments, and premium materials. Whether you seek modern minimalism or rich luxury aesthetics, our finishing experts transform empty structures into beautiful, livable spaces with elegance and precision. Every detail matters — and we treat each project as a masterpiece.',
                    ],
                    [
                        'icon' => 'fa-couch',
                        'title' => 'Decor & Furniture Manufacturing',
                        'short' => 'Custom furniture built with precision and detail.',
                        'full' => 'Our bespoke furniture and décor manufacturing delivers exclusivity and luxury tailored to your lifestyle.
Using premium woods, metals, fabrics, and finishing techniques, we create unique pieces that combine comfort, aesthetics, and durability. From sofas to dining sets, shelving, doors, tables, and décor elements — every piece is custom-made with passion and expertise. Designed to perfectly fit your spaces and elevate your interior identity, our craftsmanship ensures long-lasting quality and timeless beauty.',
                    ],
                ];
            @endphp

            <div class="row g-4">
                @foreach ($services as $index => $service)
                    <div class="col-lg-4 col-md-6">
                        <div class="akg-card text-center p-4">

                            <i class="fa {{ $service['icon'] }} akg-card-icon"></i>

                            <h4 class="text-gold mt-3">{{ $service['title'] }}</h4>

                            <p class="text-muted small mb-2">{{ $service['short'] }}</p>

                            <button class="akg-service-toggle btn btn-link text-gold p-0 mt-1" data-bs-toggle="collapse"
                                data-bs-target="#service-{{ $index }}">
                                Learn more <i class="fa fa-chevron-down small ms-1"></i>
                            </button>

                            <div id="service-{{ $index }}"
                                class="collapse akg-service-details mt-3 text-start small">
                                <p class="text-muted mb-0">{{ $service['full'] }}</p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    {{-- ======================================
      WHY CHOOSE US
====================================== --}}
    <section class="container-xxl py-5">
        <div class="container">

            <h2 class="akg-section-head text-center mb-5">Why Choose Ali Krecht Group?</h2>

            <div class="row g-4">

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-award akg-card-icon"></i>
                        <h5 class="text-gold">Premium Quality</h5>
                        <p class="text-muted small">We use only the best materials.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-users-gear akg-card-icon"></i>
                        <h5 class="text-gold">Expert Team</h5>
                        <p class="text-muted small">Professionals with decades of experience.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-clock akg-card-icon"></i>
                        <h5 class="text-gold">On-Time Delivery</h5>
                        <p class="text-muted small">Fast and precise project execution.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card text-center">
                        <i class="fa fa-handshake akg-card-icon"></i>
                        <h5 class="text-gold">Trusted</h5>
                        <p class="text-muted small">Reliable quality & customer satisfaction.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>



    {{-- ======================================
      CTA SECTION
====================================== --}}
    <section class="container-xxl py-5">
        <div class="container text-center">

            <h2 class="text-gold fw-bold mb-3">Ready to Transform Your Space?</h2>

            <p class="text-muted mb-4">
                Contact us today and let’s bring your vision to life.
            </p>

            <a href="{{ route('contact') }}" class="btn btn-gold px-5 py-3 fw-bold">
                Contact Us
            </a>

        </div>
    </section>

@endsection
