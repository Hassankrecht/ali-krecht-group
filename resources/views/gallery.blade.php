@extends('layouts.app')

@section('title', 'Gallery')
@section('meta_description', 'Browse our gallery of completed luxury carpentry and interior design projects by Ali Krecht Group.')

@section('content')
    <div class="akg-hero-img-box position-relative">
        <img src="{{ asset('assets/img/services/hero.jpg') }}" class="akg-hero-img" alt="Gallery" loading="lazy">
        <div class="akg-hero-overlay"></div>
        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-2">{{ __('messages.nav.gallery') ?? 'Gallery' }}</h1>
            <p class="text-light small">{{ __('messages.home.projects_sub') ?? 'Our work snapshots' }}</p>
        </div>
    </div>

    <section class="container-xxl py-5">
        <div class="container akg-newcard">

            @php
                $parents = $parents ?? collect();
                $activeParent = $parents->first();
                $activeChildSlug = optional($activeParent['children'][0] ?? null)['slug'] ?? null;
            @endphp

            @if($parents->count())
                <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav">
                    <ul class="nav nav-pills justify-content-center mb-3 flex-wrap gap-2" id="galParentNav">
                        @foreach($parents as $parent)
                            <li class="nav-item">
                                <a class="nav-link {{ ($activeParent['slug'] ?? '') === $parent['slug'] ? 'active' : '' }}" href="#"
                                   data-parent="{{ $parent['slug'] }}">
                                    {{ $parent['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @foreach($parents as $parent)
                        <ul class="nav nav-pills justify-content-center flex-wrap gap-2 gal-child {{ ($activeParent['slug'] ?? '') === $parent['slug'] ? '' : 'd-none' }}" id="gal-child-{{ $parent['slug'] }}">
                            @foreach($parent['children'] as $child)
                                <li class="nav-item">
                                    <a class="nav-link {{ $child['slug'] === $activeChildSlug ? 'active' : '' }}"
                                       data-bs-toggle="pill"
                                       href="#gal-tab-{{ $child['slug'] }}">
                                        {{ $child['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            @endif

            <div class="tab-content">
                @foreach($parents as $parent)
                    @foreach($parent['children'] as $child)
                        <div id="gal-tab-{{ $child['slug'] }}" class="tab-pane fade {{ $child['slug'] === $activeChildSlug ? 'show active' : '' }}">
                            <div class="row g-3">
                                @forelse($child['images'] as $img)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="akg-card p-2">
                                            <img src="{{ $img }}" class="w-100 rounded shadow-sm" style="height:200px; object-fit:cover;" loading="lazy">
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="akg-card text-center py-4">
                                            <p class="text-muted mb-0">{{ __('messages.projects.no_projects') ?? 'No images available.' }}</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const parentLinks = document.querySelectorAll('#galParentNav .nav-link');
            parentLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const parentSlug = link.dataset.parent;

                    parentLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    document.querySelectorAll('.gal-child').forEach(el => el.classList.add('d-none'));
                    const childRow = document.getElementById(`gal-child-${parentSlug}`);
                    if (childRow) {
                        childRow.classList.remove('d-none');
                        const firstChild = childRow.querySelector('.nav-link');
                        if (firstChild) firstChild.click();
                    }
                });
            });
        });
    </script>
@endsection
