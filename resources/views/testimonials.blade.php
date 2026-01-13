@extends('layouts.app')

@section('title', __('messages.home.testimonials_title') ?? 'Testimonials')

@section('content')
    <div class="akg-hero-img-box position-relative">
        <img src="{{ asset('assets/img/services/hero.jpg') }}" class="akg-hero-img" alt="Testimonials" loading="lazy">
        <div class="akg-hero-overlay"></div>
        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-2">{{ __('messages.home.testimonials_title') ?? 'Testimonials' }}</h1>
            <p class="text-light small">
                {{ __('messages.home.rating_text') ?? 'Average rating' }}:
                <span class="fw-bold text-gold">{{ $reviewsCount ? number_format($reviewsAvg,1) : '—' }} / 5</span>
                @if($reviewsCount)
                    <small class="text-muted">({{ $reviewsCount }})</small>
                @endif
            </p>
        </div>
    </div>

    <section class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="row g-4 justify-content-center">
                @forelse($reviews as $review)
                    @php
                        if ($review->photo) {
                            $path = $review->photo;
                            if (str_contains($path, 'storage/public/assets/reviews/')) {
                                $path = str_replace('storage/', '', $path); // يصبح public/assets/...
                            }
                            if (str_starts_with($path, 'public/')) {
                                $photo = asset($path);
                            } elseif (str_starts_with($path, 'assets/')) {
                                $photo = asset($path);
                            } else {
                                $photo = asset('storage/' . $path);
                            }
                        } else {
                            $photo = asset('assets/img/clients/client1.jpg');
                        }
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="akg-card h-100 text-center p-3">
                            <div class="akg-testimonial-stars mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="akg-testimonial-text small">“{{ $review->review }}”</p>
                            <div class="akg-testimonial-client mt-3">
                                <img src="{{ $photo }}" class="akg-client-img" alt="{{ $review->name }}" loading="lazy">
                                <h6 class="text-gold mt-2 mb-0">{{ $review->name }}</h6>
                                <span class="text-muted small">{{ $review->profession }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="akg-card text-center py-4">
                            <p class="text-muted mb-0">{{ __('messages.projects.no_projects') ?? 'No testimonials available.' }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>

            {{-- Submit a testimonial --}}
            <div class="akg-card mt-4 p-4">
                <h5 class="text-gold mb-3">{{ __('messages.home.testimonial_form.title') }}</h5>
                @if(session('review_success'))
                    <div class="alert alert-success mb-3">{{ session('review_success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reviews.store') }}" method="POST" class="row g-3 js-recaptcha" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.name') }} *</label>
                        <input type="text" name="name" class="form-control akg-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.profession') }}</label>
                        <input type="text" name="profession" class="form-control akg-input">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.rating') }} *</label>
                        <div class="d-flex align-items-center gap-2 akg-rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" class="akg-star-btn" data-value="{{ $i }}">
                                    <i class="fa fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.photo') }}</label>
                        <input type="file" name="photo" class="form-control akg-input" accept="image/*">
                        <small class="text-muted">{{ __('messages.home.testimonial_form.photo_hint') }}</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.review') }} *</label>
                        <textarea name="review" rows="4" class="form-control akg-input" required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-gold px-4"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                            data-size="invisible"
                            data-badge="bottomright"
                            data-loading-text="Sending...">
                            {{ __('messages.home.testimonial_form.submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ratingInput = document.getElementById('ratingInput');
            const starButtons = document.querySelectorAll('.akg-star-btn');
            starButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = btn.dataset.value;
                    ratingInput.value = val;
                    starButtons.forEach(s => s.classList.toggle('active', s.dataset.value <= val));
                });
            });
            starButtons.forEach(s => s.classList.toggle('active', s.dataset.value <= ratingInput.value));
        });
    </script>
    @endpush
@endsection
