@extends('layouts.app')

@section('content')
    {{-- ================= HERO ================= --}}
    <section class="akg-hero container-xxl d-flex align-items-center" style="margin-top: 25px;">

        <!-- VIDEO BACKGROUND -->
        <video class="akg-hero-video" autoplay loop muted playsinline
            poster="{{ asset('assets/img/video.jpg') }}">
            <source src="{{ asset('assets/videos/last.mp4') }}" type="video/mp4">
        </video>

        <!-- OVERLAY -->
        <div class="akg-hero-overlay"></div>

        <!-- HERO CONTENT -->
        <div class="container py-5 position-relative">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 text-light ps-lg-5">

                    <h1 class="akg-hero-title mb-3">
                        {{ __('messages.home.hero_title') }}
                    </h1>

                    <p class="akg-hero-subtitle mb-4">
                        {{ __('messages.home.hero_subtitle') }}
                    </p>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#projects" class="btn btn-gold px-4 py-2">{{ __('messages.home.cta_projects') }}</a>
                        <a href="#contact" class="btn btn-outline-gold px-4 py-2">{{ __('messages.home.cta_quote') }}</a>
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- ================= TRUST BAR ================= --}}
    <section class="container-xxl py-5">
        <div class="container akg-newcard ">
            <div class="  row g-3 text-center">
                <div class=" col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class=" akg-trust-number">15+</span>
                        <span class=" akg-trust-label">{{ __('messages.home.trust_years') }}</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">50+</span>
                        <span class="akg-trust-label">{{ __('messages.home.trust_projects') }}</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">Full-Scope</span>
                        <span class="akg-trust-label">{{ __('messages.home.trust_scope') }}</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">24/7</span>
                        <span class="akg-trust-label">{{ __('messages.home.trust_support') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ================= SERVICES ================= --}}
    <section id="services" class="container-xxl py-5">
        <div class="container akg-newcard">
            <h2 class="akg-section-head text-center mb-5">{{ __('messages.home.services_title') }}</h2>

            <div class="row g-4">
                @php
                    $services = [
                        [
                            'icon' => 'fa-screwdriver-wrench',
                            'title' => 'Carpentry & Woodwork',
                            'desc' => 'Doors, kitchens, furniture & luxury wood finishes.',
                            'proof' => 'Premium ash/oak, millimeter-precise joinery.',
                        ],
                        [
                            'icon' => 'fa-hard-hat',
                            'title' => 'Construction & Renovation',
                            'desc' => 'Professional building, renovation & maintenance.',
                            'proof' => 'Site-led teams, predictable schedules.',
                        ],
                        [
                            'icon' => 'fa-paint-roller',
                            'title' => 'Interior Decoration',
                            'desc' => 'Modern, classic & luxury décor solutions.',
                            'proof' => 'Curated palettes, artisan metal and glass.',
                        ],
                        [
                            'icon' => 'fa-compass-drafting',
                            'title' => 'Custom Design',
                            'desc' => 'Tailored solutions for homes & commercial spaces.',
                            'proof' => 'Concept-to-handover, single point of contact.',
                        ],
                    ];
                @endphp

                @foreach ($services as $service)
                    <div class="col-lg-3 col-md-6">
                        <div class="akg-card text-center">
                            <i class="fa {{ $service['icon'] }} akg-card-icon"></i>
                            <h5 class="mt-3 text-gold">{{ $service['title'] }}</h5>
                            <p class="text-muted small mb-1">{{ $service['desc'] }}</p>
                            <p class="text-gold small fw-semibold mb-0">{{ $service['proof'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= ABOUT ================= --}}
    <section id="about" class="container-xxl py-5  ">
        <div class="container akg-newcard">
            <div class="row g-5 align-items-center">

                <!-- ============== IMAGES SIDE ============== -->
                <div class="col-lg-6">
                    <div class="row g-3 about-lux-grid">

                        <div class=" col-6 d-flex align-items-end">
                            <!-- صورة 1 -->
                            <img src="{{ asset('assets/img/pexels-cottonbro-7492889.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Craftsman at work"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-end">
                            <!-- صورة 2 -->
                            <img src="{{ asset('assets/img/pexels-pixabay-159375.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Premium carpentry tools"
                                style="height: 240px; object-fit: cover; margin-top: 40px;" loading="lazy">
                        </div>

                        <!-- الصف الثاني -->
                        <div class="col-6 d-flex align-items-start">
                            <!-- صورة 3 -->
                            <img src="{{ asset('assets/img/pexels-enginakyurt-1463917.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Luxury interior details"
                                style="height: 240px; object-fit: cover; margin-bottom: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <!-- صورة 4 -->
                            <img src="{{ asset('assets/img/pexels-ivan-s-4491884.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Custom wood paneling"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                    </div>
                </div>

                <!-- ============== TEXT SIDE ============== -->
                <div class="col-lg-6">
                    <h5 class="akg-hero-title text-gold mb-3">{{ __('messages.home.about_title') }}</h5>

                    <h2 class="text-light mb-4">
                        Craftsmanship at <span class="text-gold">Its Finest</span>
                    </h2>

                    <p class="text-muted mb-3">
                        Ali Krecht Group specializes in carpentry, construction, interiors, and decorative designs.
                        We combine modern techniques with traditional craftsmanship to deliver premium results.
                    </p>

                    <p class="text-muted mb-4">
                        Every project we create reflects precision, creativity, and quality that lasts for years.
                        From bespoke wardrobes to full-scale builds — excellence is our standard.
                    </p>

                    <div class="akg-why-box">
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            Turnkey delivery: design, build, interiors under one roof.
                        </div>
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            Cost and timeline transparency from day one.
                        </div>
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            Dedicated project lead with 24/7 support.
                        </div>
                    </div>

                    <!-- ============== EXPERIENCE BOXES ============== -->
                    <div class="row g-4 mb-4">

                        <!-- BOX 1 -->

                        <div class=" col-sm-6">
                            <div class="experience-box">
                                <h1 class="experience-number">15</h1>
                                <div class="ps-3">
                                    <p class="mb-0 small text-muted">Years of</p>
                                    <h6 class="text-uppercase text-light mb-0">Experience</h6>
                                </div>
                            </div>
                        </div>


                        <!-- BOX 2 -->
                        <div class="col-sm-6">
                            <div class="experience-box">
                                <h1 class="experience-number">50</h1>
                                <div class="ps-3">
                                    <p class="mb-0 small text-muted">Successful</p>
                                    <h6 class="text-uppercase text-light mb-0">Projects Completed</h6>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('about') }}" class="btn btn-gold px-4 mt-2">{{ __('messages.home.about_cta_learn') }}</a>
                        <a href="#contact" class="btn btn-outline-gold px-4 mt-2">{{ __('messages.home.about_cta_visit') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </section>




    {{-- ================= PROJECTS ================= --}}
    <section id="projects" class="container-xxl py-5  ">
        <div class="container akg-newcard">
            <h2 class="akg-section-head text-center">{{ __('messages.home.projects_title') }}</h2>
            <p class="text-center text-muted mb-5">{{ __('messages.home.projects_sub') }}</p>

            <div class="row g-4">
                @forelse($projects as $project)
                    @php
                        // Choose image
                        $imagePath = $project->main_image ?? ($project->images->first()->image_path ?? null);

                        if ($imagePath && file_exists(public_path('storage/' . $imagePath))) {
                            $img = asset('storage/' . $imagePath);
                        } else {
                            $img = asset('assets/img/default.jpg');
                        }

                        // Gallery images
                        $gallery = $project->images->pluck('image_path')->map(fn($img) => asset('storage/' . $img));
                    @endphp

                    <div class="col-lg-4 col-md-6">
                        <div class="akg-card">
                            <img src="{{ $img }}" class="akg-project-img" alt="{{ $project->title }}" loading="lazy">

                            <div class="p-3 text-center">
                                <h4 class="text-gold">{{ $project->title }}</h4>
                                <p class="small text-muted">{{ Str::limit($project->description, 80) }}</p>

                                <button class="btn btn-outline-gold px-4 mt-2 view-project-btn"
                                    data-id="{{ $project->id }}" data-title="{{ e($project->title) }}"
                                    data-description="{{ e($project->description) }}" data-image="{{ $img }}"
                                    data-gallery='@json($gallery)'>
                                    {{ __('messages.home.projects_view') }}
                                </button>
                            </div>
                        </div>
                    </div>

                @empty
                    <p class="text-center text-light">No projects available.</p>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="{{ url('/projects') }}" class="btn btn-outline-gold px-4">{{ __('messages.home.projects_all') }}</a>
            </div>
        </div>
    </section>
    <!-- Project Modal -->
    <div id="projectModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark-soft text-light rounded-4 border border-gold">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-gold" id="projectTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="lightboxGallery" class="d-flex flex-wrap justify-content-center gap-3"></div>
                    <p id="projectDescription" class="mt-4 text-muted"></p>
                </div>

                <div class="modal-footer border-0 justify-content-between">
                    <button class="btn btn-outline-gold" data-bs-dismiss="modal">Close</button>
                    <a id="fullProjectLink" href="#" class="btn btn-gold">Full Project</a>
                </div>

            </div>
        </div>
    </div>

    <!-- Lightbox Overlay -->
    <div id="lightboxOverlay">
        <div class="lightbox-content">
            <img id="lightboxImage" src="">
            <span class="lightbox-close">&times;</span>
            <button class="lightbox-prev">&#10094;</button>
            <button class="lightbox-next">&#10095;</button>
        </div>
    </div>



    {{-- ================= PRODUCTS ================= --}}
    <section id="products" class="container-xxl py-5">
        <div class="container akg-newcard">

            <!-- SECTION HEAD -->
            <div class="text-center mb-5">
                <h5 class="akg-section-label">{{ __('messages.home.products_label') }}</h5>
                <h2 class="akg-section-head">{{ __('messages.home.products_title') }}</h2>
            </div>

            <!-- CATEGORY TABS -->
            <ul class="nav akg-tabs justify-content-center mb-5">
                @foreach ($categories as $category)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="pill"
                            href="#tab-{{ $category->id }}">
                            {{ ucfirst($category->name) }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- PRODUCTS -->
            <div class="tab-content">
                @foreach ($categories as $category)
                    <div id="tab-{{ $category->id }}" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}">
                        <div class="row g-4 justify-content-center">

                            @foreach ($productsByCategory[$category->id] as $product)
                                @php
                                    $img = $product->image
                                        ? asset('storage/' . $product->image)
                                        : ($product->images->first()
                                            ? asset('storage/' . $product->images->first()->image)
                                            : asset('assets/img/default.jpg'));
                                @endphp

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="akg-product-card">
                                        @if ($loop->first)
                                            <span class="akg-product-badge">New</span>
                                        @endif

                                        <div class="akg-product-img-box">
                                            <img src="{{ $img }}" class="akg-product-img" alt="{{ $product->title }}"
                                                loading="lazy">
                                            <div class="akg-product-overlay">
                                                <a href="{{ route('products.show', $product->id) }}"
                                                    class="btn-gold-small">
                                                    {{ __('messages.home.products_view') }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="akg-product-info text-center">
                                            <h5 class="akg-product-title">{{ $product->title }}</h5>
                                            <p class="akg-product-desc">{{ Str::limit($product->description, 55) }}</p>
                                            <span class="akg-product-price">${{ $product->price }}</span>
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ================= TEAM ================= --}}
    <section id="team" class="container-xxl py-5">
        <div class="container akg-newcard">

            <!-- TITLE -->
            <div class="text-center mb-5">
                <h5 class="akg-section-label">{{ __('messages.home.team_label') }}</h5>
                <h2 class="akg-section-head">{{ __('messages.home.team_title') }}</h2>
            </div>
            @php
                $team = [
                    [
                        'name' => 'John Smith',
                        'job' => 'Master Carpenter',
                        'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
                    ],
                    [
                        'name' => 'Emily Johnson',
                        'job' => 'Interior Designer',
                        'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
                    ],
                    [
                        'name' => 'Michael Brown',
                        'job' => 'Construction Lead',
                        'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
                    ],
                    [
                        'name' => 'Sarah Davis',
                        'job' => 'Project Manager',
                        'photo' => asset('assets/img/pexels-brett-sayles-1073097.jpg'),
                    ],
                ];
            @endphp

            <div class="row g-4">
                @foreach ($team as $member)
                    <div class="col-lg-3 col-md-6">
                        <div class="akg-card">

                            <div class="akg-team-img-box">
                                <img src="{{ $member['photo'] }}" class="akg-team-img" alt="{{ $member['name'] }}"
                                    loading="lazy">
                            </div>

                            <div class="akg-team-info text-center mt-3">
                                <h5 class="akg-team-name">{{ $member['name'] }}</h5>
                                <p class="akg-team-job">{{ $member['job'] }}</p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ================= TESTIMONIALS ================= --}}
    <section id="testimonials" class="container-xxl py-5">
        <div class="container akg-newcard text-center ">

            <!-- Section Title -->
            <h5 class="akg-section-label">{{ __('messages.home.testimonials_label') }}</h5>
            <h2 class="akg-section-head mb-5">{{ __('messages.home.testimonials_title') }}</h2>
            <p class="text-muted mb-4">Average rating: <span class="text-gold fw-bold">4.9 / 5</span></p>

            <div class="row g-4 justify-content-center">

                <!-- Testimonial 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="akg-card text-center">
                        <div class="akg-testimonial-icon">
                            <i class="fa fa-quote-left"></i>
                        </div>
                        <div class="akg-testimonial-stars mb-2">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                        </div>

                        <p class="akg-testimonial-text">
                            “Amazing craftsmanship and outstanding service. They transformed our home beautifully.”
                        </p>

                        <div class="akg-testimonial-client mt-3">
                            <img src="{{ asset('assets/img/clients/client1.jpg') }}" class="akg-client-img"
                                alt="Client - Sarah Williams" loading="lazy">
                            <h6 class="text-gold mt-2">Sarah Williams</h6>
                            <span class="text-muted small">Home Owner</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="akg-card text-center">
                        <div class="akg-testimonial-icon">
                            <i class="fa fa-quote-left"></i>
                        </div>
                        <div class="akg-testimonial-stars mb-2">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                class="fa fa-star"></i><i class="fa fa-star"></i>
                        </div>

                        <p class="akg-testimonial-text">
                            “Professional, fast, and excellent quality. Highly recommended for carpentry and interior work.”
                        </p>

                        <div class="akg-testimonial-client mt-3">
                            <img src="{{ asset('assets/img/clients/client2.jpg') }}" class="akg-client-img"
                                alt="Client - Michael Anderson" loading="lazy">
                            <h6 class="text-gold mt-2">Michael Anderson</h6>
                            <span class="text-muted small">Business Owner</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="akg-card text-center">
                        <div class="akg-testimonial-icon">
                            <i class="fa fa-quote-left"></i>
                        </div>
                        <div class="akg-testimonial-stars mb-2">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                        </div>

                        <p class="akg-testimonial-text">
                            “Their attention to detail is incredible. The interior design service was beyond expectations.”
                        </p>

                        <div class="akg-testimonial-client mt-3">
                            <img src="{{ asset('assets/img/clients/client3.jpg') }}" class="akg-client-img"
                                alt="Client - Layla Khoury" loading="lazy">
                            <h6 class="text-gold mt-2">Layla Khoury</h6>
                            <span class="text-muted small">Interior Client</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ================= CONTACT ================= --}}

    <section id="contact" class="container-xxl py-5">
        <div class="container akg-newcard text-center">
            <h2 class="akg-section-head">{{ __('messages.home.contact_title') }}</h2>
            <p class="text-muted mb-3">{{ __('messages.home.contact_sub') }}</p>
            <div class="row justify-content-center mb-4">
                <div class="col-md-4">
                    <a class="akg-quick-contact d-inline-flex align-items-center justify-content-center"
                        href="tel:+971501234567">
                        <i class="fa fa-phone me-2"></i> +971 50 123 4567
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="akg-quick-contact d-inline-flex align-items-center justify-content-center"
                        href="https://wa.me/971501234567" target="_blank" rel="noopener">
                        <i class="fa fa-whatsapp me-2"></i> WhatsApp available
                    </a>
                </div>
            </div>
            <div class="akg-card p-4">

                <form action="{{ route('contact.send') }}" method="POST" class="row g-3 mt-4">
                    @csrf

                    <div class="col-md-6">
                        <input type="text" name="name" class="form-control akg-input" placeholder="Your Name"
                            required>
                    </div>

                    <div class="col-md-6">
                        <input type="email" name="email" class="form-control akg-input" placeholder="Your Email"
                            required>
                    </div>

                    <div class="col-12">
                        <textarea name="message" class="form-control akg-input" rows="5" placeholder="Your Message" required></textarea>
                    </div>

                    <!-- Google reCAPTCHA -->
                    <div class="col-12 mt-3">
                        <div class="g-recaptcha d-flex justify-content-center"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-gold px-5 py-3">{{ __('messages.home.contact_send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = new bootstrap.Modal(document.getElementById('projectModal'));
            const modalTitle = document.getElementById('projectTitle');
            const modalDesc = document.getElementById('projectDescription');
            const modalLink = document.getElementById('fullProjectLink');
            const galleryBox = document.getElementById('lightboxGallery');

            const lightbox = document.getElementById('lightboxOverlay');
            const lightboxImage = document.getElementById('lightboxImage');
            const nextBtn = document.querySelector('.lightbox-next');
            const prevBtn = document.querySelector('.lightbox-prev');
            const closeBtn = document.querySelector('.lightbox-close');

            let allImages = [];
            let currentIndex = 0;

            // Open project popup
            document.querySelectorAll('.view-project-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const title = btn.dataset.title;
                    const desc = btn.dataset.description;
                    const image = btn.dataset.image;
                    const gallery = JSON.parse(btn.dataset.gallery || '[]');
                    const id = btn.dataset.id;

                    modalTitle.textContent = title;
                    modalDesc.textContent = desc;
                    modalLink.href = `/projects/${id}`;

                    galleryBox.innerHTML = "";

                    allImages = [image, ...gallery];

                    allImages.forEach((src, index) => {
                        const img = document.createElement('img');
                        img.src = src;
                        img.classList.add('lightbox-thumb', 'rounded', 'shadow');
                        img.style.width = '170px';
                        img.style.height = '130px';
                        img.style.objectFit = 'cover';
                        img.onclick = () => openLightbox(index);
                        galleryBox.appendChild(img);
                    });

                    modal.show();
                });
            });

            // Lightbox
            function openLightbox(index) {
                currentIndex = index;
                lightboxImage.src = allImages[currentIndex];

                // Ensure center positioning
                lightbox.style.display = 'flex';
                document.body.style.overflow = "hidden"; // Lock background scroll
            }

            function closeLightbox() {
                lightbox.style.display = "none";
                document.body.style.overflow = "auto"; // Restore scroll
            }


            nextBtn.onclick = () => {
                currentIndex = (currentIndex + 1) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            };

            prevBtn.onclick = () => {
                currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            };

            closeBtn.onclick = closeLightbox;

            lightbox.onclick = (e) => {
                if (e.target === lightbox) closeLightbox();
            };
        });
    </script>
@endsection
