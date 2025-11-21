@extends('layouts.app')

@section('title', 'About Us')

@section('content')

    <!-- ===================== HERO SECTION (FULL IMAGE – NO CROP) ===================== -->
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 11_50_19 AM.png') }}" alt="Ali Krecht Group workshop"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">About Us</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">About</li>
            </ol>
        </div>
    </div>


    <!-- ===================== ABOUT SECTION ===================== -->
    <div class="container-xxl py-5">
        <div class="container about-item">
            <div class="row g-5 align-items-center">

                <!-- Images -->
                <div class="col-lg-6">
                    <div class="row gx-3 gy-3">

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-cottonbro-7492889.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" style="height: 280px; object-fit: cover;"
                                alt="Master carpenter crafting wood" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-pixabay-159375.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Premium carpentry tools"
                                style="height: 240px; object-fit: cover; margin-top: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-enginakyurt-1463917.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Luxury interior detailing"
                                style="height: 240px; object-fit: cover; margin-bottom: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-ivan-s-4491884.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Custom wood paneling"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                    </div>
                </div>

                <!-- Text -->
                <div class="col-lg-6 about-text">
                    <h5 class="akg-section-label">About Us</h5>

                    <h1 class="mb-4 text-gold">
                        Welcome to <span class="text-light">Ali Krecht Group</span>
                    </h1>

                    <p class="text-light">
                        At <strong>Ali Krecht Group</strong>, craftsmanship is not only a profession—it is a heritage.
                        We bring together decades of experience in construction, carpentry,
                        and interior design to deliver projects defined by elegance, precision, and long-lasting value.
                    </p>

                    <p class="text-light">
                        From structural development to the final artistic touches, our work reflects a deep commitment
                        to detail and refined quality. Every material, every design choice, and every finishing element
                        is handled with exceptional care—ensuring that the spaces we build carry a signature of excellence.
                    </p>

                    <p class="text-muted">
                        Our team consists of engineers, master carpenters, designers, and specialists dedicated to
                        transforming
                        raw materials into functional, luxurious environments. Whether it’s a custom-made furniture piece,
                        a full interior renovation, or a complete construction project, we deliver with integrity and
                        passion.
                    </p>

                    <p class="text-warning fw-bold" style="font-size: 1.25rem;">
                        Your vision. Our craftsmanship. A partnership built on trust and excellence.
                    </p>

                    <!-- Trust stats -->
                    <div class="row g-3 mt-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="akg-card text-center">
                                <span class="akg-trust-number">15+</span>
                                <span class="akg-trust-label">Years Mastery</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="akg-card text-center">
                                <span class="akg-trust-number">50+</span>
                                <span class="akg-trust-label">Turnkey Projects</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="akg-card text-center">
                                <span class="akg-trust-number">24/7</span>
                                <span class="akg-trust-label">Client Support</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="akg-card text-center">
                                <span class="akg-trust-number">Q/A</span>
                                <span class="akg-trust-label">European Standards</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 flex-wrap mt-4">
                        <a href="{{ route('projects.index') }}" class="btn btn-gold px-4 py-2">View Work</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-gold px-4 py-2">Book a Consultation</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ===================== METHOD / PARTNERS ===================== -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h5 class="akg-section-label">How We Work</h5>
                    <h2 class="akg-section-head mb-4">From Brief to Handover</h2>
                    <ul class="list-unstyled text-light small">
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>Discovery & site visit to capture goals,
                            budget, and timeline.</li>
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>Concept + detailed BOQ for full cost
                            clarity.</li>
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>Execution led by dedicated project
                            manager with weekly updates.</li>
                        <li><i class="fa fa-check text-gold me-2"></i>Quality control, snag-free finishing, and supported
                            handover.</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="akg-card p-4">
                        <h5 class="text-gold mb-3">Trusted By</h5>
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="badge bg-dark text-gold border border-gold">Hospitality</span>
                            <span class="badge bg-dark text-gold border border-gold">Residential</span>
                            <span class="badge bg-dark text-gold border border-gold">Retail</span>
                            <span class="badge bg-dark text-gold border border-gold">Offices</span>
                            <span class="badge bg-dark text-gold border border-gold">Developers</span>
                        </div>
                        <p class="text-muted small mb-0 mt-3">Add client logos here to strengthen credibility.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
