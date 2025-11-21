@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 11_59_33 AM.png') }}" alt="Projects hero"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">Our Projects</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Projects</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <h2 class="akg-section-head mb-2">{{ __('messages.projects_page.section_title') }}</h2>
            <p class="text-muted mb-4">{{ __('messages.projects_page.section_sub') }}</p>

            <div class="row g-4">
                @forelse($projects as $project)
                    <div class="col-lg-4 col-md-6">
                        <div class="akg-project-card h-100">

                            <!-- IMAGE -->
                            <img src="{{ $project->main_image_url }}" class="akg-project-img"
                                alt="{{ $project->title }}" loading="lazy">

                            <!-- CONTENT -->
                            <div class="text-center mt-3 px-3 pb-3">
                                <h5 class="text-gold fw-bold">{{ $project->title }}</h5>

                                <p class="text-muted small">
                                    {{ Str::limit($project->description, 90) }}
                                </p>

                                <button class="btn btn-gold-small view-project-btn" data-id="{{ $project->id }}"
                                    data-title="{{ e($project->title) }}"
                                    data-description="{{ e($project->description) }}"
                                    data-image="{{ $project->main_image_url }}"
                                    data-url="{{ route('projects.show', $project->id) }}"
                                    data-gallery='@json($project->gallery_urls)'>
                                    <i class="bi bi-eye me-2"></i> {{ __('messages.projects_page.view_details') }}
                                </button>
                            </div>

                        </div>
                    </div>

                @empty
                    <p class="text-center text-light">No projects available.</p>
                @endforelse
            </div>

            <div class="row g-3 mt-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="akg-card">
                        <span class="akg-trust-number">15+</span>
                        <span class="akg-trust-label">Years Mastery</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akg-card">
                        <span class="akg-trust-number">50+</span>
                        <span class="akg-trust-label">Turnkey Projects</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akg-card">
                        <span class="akg-trust-number">24/7</span>
                        <span class="akg-trust-label">Client Support</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akg-card">
                        <span class="akg-trust-number">QA</span>
                        <span class="akg-trust-label">European Standards</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('contact') }}" class="btn btn-gold px-4 py-2 me-2">{{ __('messages.projects_page.cta_consult') }}</a>
                <a href="https://wa.me/971501234567" class="btn btn-outline-gold px-4 py-2" target="_blank"
                    rel="noopener">{{ __('messages.projects_page.cta_whatsapp') }}</a>
            </div>
        </div>
    </div>

    {{-- ✅ نافذة المشروع المنبثقة --}}
    <!-- 🖼️ Modal for Lightbox -->
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



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('projectModal'));
            const modalTitle = document.getElementById('projectTitle');
            const modalDesc = document.getElementById('projectDescription');
            const modalLink = document.getElementById('fullProjectLink');
            const galleryContainer = document.getElementById('lightboxGallery');

            // Lightbox overlay element
            const lightbox = document.createElement('div');
            lightbox.id = 'lightboxOverlay';
            lightbox.style.display = 'none';
            lightbox.innerHTML = `
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
            <button class="lightbox-prev">&#10094;</button>
            <button class="lightbox-next">&#10095;</button>
            <span class="lightbox-close">&times;</span>
        </div> `;
            document.body.appendChild(lightbox);

            const lightboxImage = lightbox.querySelector('#lightboxImage');
            const prevBtn = lightbox.querySelector('.lightbox-prev');
            const nextBtn = lightbox.querySelector('.lightbox-next');
            const closeBtn = lightbox.querySelector('.lightbox-close');
            let currentIndex = 0,
                allImages = [];

            // Open project modal
            document.querySelectorAll('.view-project-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const title = this.dataset.title;
                    const desc = this.dataset.description;
                    const mainImage = this.dataset.image;
                    const gallery = JSON.parse(this.dataset.gallery || '[]');
                    const projectId = this.dataset.id || '';
                    const projectUrl = this.dataset.url || `/projects/${projectId}`;

                    modalTitle.textContent = title;
                    modalDesc.textContent = desc;
                    modalLink.href = projectUrl;
                    galleryContainer.innerHTML = '';

                    // Combine main image + gallery
                    allImages = [mainImage, ...gallery].filter(Boolean);

                    if (!allImages.length) {
                        const empty = document.createElement('p');
                        empty.classList.add('text-muted', 'small', 'w-100', 'text-center');
                        empty.textContent = 'No gallery images available.';
                        galleryContainer.appendChild(empty);
                    } else {
                        allImages.forEach((src, index) => {
                            const img = document.createElement('img');
                            img.src = src;
                            img.alt = `${title} - ${index}`;
                            img.loading = 'lazy';
                            img.classList.add('lightbox-thumb', 'rounded', 'shadow');
                            img.style.width = '180px';
                            img.style.height = '130px';
                            img.style.objectFit = 'cover';
                            img.style.cursor = 'pointer';
                            img.addEventListener('click', () => openLightbox(index));
                            galleryContainer.appendChild(img);
                        });
                    }

                    modal.show();
                });
            });

            // Lightbox controls
            function openLightbox(index) {
                currentIndex = index;
                lightboxImage.src = allImages[currentIndex];
                lightboxImage.alt = modalTitle.textContent || 'Project image';
                lightbox.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            }

            function showPrev() {
                currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            }

            // Buttons
            nextBtn.onclick = showNext;
            prevBtn.onclick = showPrev;
            closeBtn.onclick = closeLightbox;
            lightbox.onclick = (e) => {
                if (e.target === lightbox) closeLightbox();
            };

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (lightbox.style.display === 'flex') {
                    if (e.key === 'ArrowRight') showNext();
                    if (e.key === 'ArrowLeft') showPrev();
                    if (e.key === 'Escape') closeLightbox();
                }
            });
        });
    </script>
@endsection
