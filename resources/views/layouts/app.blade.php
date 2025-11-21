<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Ali Krecht Group'))</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Icons & Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Animations -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- ===================== THE MAIN MERGED THEME FILE ===================== -->
    <link href="{{ asset('assets/css/AKG-Luxury.css') }}" rel="stylesheet">
</head>

<body>

@php
    $cartCount = session('cart_count', 0);
    $cartTotal = session('cart_total', 0);
@endphp

<!-- ===================== TOP GOLD BAR ===================== -->
<div class="akg-navbar-topline"></div>

<!-- ===================== NAVBAR ===================== -->
<nav class="akg-navbar navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/ChatGPT Image Nov 3, 2025, 08_00_27 AM.png') }}"
                 alt="Logo" class="akg-logo">
            <span class="akg-brand-text ms-2">Ali Krecht Group</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#akgNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="akgNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">{{ __('messages.nav.about') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('services') }}">{{ __('messages.nav.services') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ __('messages.nav.products') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">{{ __('messages.nav.projects') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">{{ __('messages.nav.contact') }}</a></li>

                <!-- Cart -->
                <li class="nav-item dropdown mx-lg-3">
                    <a class="nav-link dropdown-toggle text-gold" href="#" data-bs-toggle="dropdown">
                        <i class="fa fa-shopping-cart me-1"></i>
                        {{ __('messages.nav.cart') }}
                        <span class="badge bg-gold cart-count">{{ $cartCount }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                        <li class="d-flex justify-content-between">
                            <span>Items:</span> <span class="fw-bold cart-count">{{ $cartCount }}</span>
                        </li>
                        <li class="d-flex justify-content-between mt-2">
                            <span>Total:</span> <span class="fw-bold cart-total">${{ number_format($cartTotal, 2) }}</span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="{{ route('cart.index') }}" class="btn btn-gold w-100 fw-semibold">
                                View Cart
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Auth -->
                @guest
                    <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">{{ __('messages.nav.login') }}</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">{{ __('messages.nav.register') }}</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fa fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                            <li><a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">@csrf
                                    <button class="dropdown-item text-danger">
                                        <i class="fa fa-power-off"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest

                {{-- Language Switcher --}}
                <li class="nav-item dropdown ms-lg-2">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                        @foreach (config('app.supported_locales') as $locale)
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}"
                                   href="{{ route('lang.switch', $locale) }}">
                                    {{ strtoupper($locale) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===================== CONTENT ===================== -->
<main class="akg-main">
    @yield('content')
</main>

<!-- ===================== FOOTER ===================== -->
<footer class="akg-footer text-center">
    <div class="container py-4">
        <p class="mb-2">© {{ date('Y') }} Ali Krecht Group. {{ __('messages.footer.all_rights') }}</p>
        <div class="akg-footer-menu mb-3">
            <a href="{{ route('home') }}">Home</a> |
            <a href="{{ route('about') }}">About</a> |
            <a href="{{ route('services') }}">Services</a> |
            <a href="{{ route('products.index') }}">Products</a> |
            <a href="{{ route('projects.index') }}">Projects</a> |
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div class="akg-footer-social d-flex justify-content-center gap-3">
            <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.facebook.com" target="_blank" rel="noopener" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.instagram.com" target="_blank" rel="noopener" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://wa.me/971501234567" target="_blank" rel="noopener" aria-label="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
    </div>
</footer>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Navbar scroll effect
    document.addEventListener("scroll", () => {
        const nav = document.querySelector(".akg-navbar");
        if (window.scrollY > 10) nav.classList.add("scrolled");
        else nav.classList.remove("scrolled");
    });
</script>

</body>
</html>
