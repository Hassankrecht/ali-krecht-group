@extends('layouts.app')

@section('title', __('messages.checkout.hero_title'))
@section('meta_description', __('messages.meta.checkout_description'))

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="{{ __('messages.checkout.hero_title') }}"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.hero_title') }}</h1>
            <p class="text-light">{{ __('messages.checkout.hero_sub') }}</p>
            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">{{ __('messages.nav.cart') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.checkout.section_label') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <h5 class="akg-section-label">{{ __('messages.checkout.section_label') }}</h5>
            <h2 class="akg-section-head mb-3">{{ __('messages.checkout.section_head') }}</h2>

            {{-- رسائل الخطأ --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('checkout.confirm') }}" id="checkoutForm" class="text-light {{ Auth::check() ? '' : 'js-recaptcha' }}">
                @csrf
                @unless(Auth::check())
                    <input type="hidden" name="g-recaptcha-response">
                @endunless
                <input type="hidden" name="total" value="{{ old('total', $total ?? 0) }}">

                {{-- ✅ إذا المستخدم مسجل دخول --}}
                @if (Auth::check())
                    <div class="alert alert-success mb-4">
                        {{ __('messages.checkout.logged_in_as') }} <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                    </div>
                    <input type="hidden" name="name" value="{{ old('name', $prefill['name'] ?? Auth::user()->name) }}">
                    <input type="hidden" name="email" value="{{ old('email', $prefill['email'] ?? Auth::user()->email) }}">
                    @include('checkout.address_form')

                {{-- 🚫 المستخدم غير مسجل دخول --}}
                @else
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="name" class="form-control akg-input" id="name"
                                    placeholder="{{ __('messages.forms.name') }}" value="{{ old('name', $prefill['name'] ?? '') }}" required>
                                <label for="name">{{ __('messages.forms.name') }}</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" name="email" class="form-control akg-input" id="email"
                                    placeholder="{{ __('messages.forms.email') }}" value="{{ old('email', $prefill['email'] ?? '') }}" required>
                                <label for="email">{{ __('messages.forms.email') }}</label>
                        </div>
                    </div>
                    </div>

                    @include('checkout.address_form')

                    {{-- خيار إنشاء الحساب --}}
                    <div class="mt-3 border rounded p-3" id="accountBox">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="createAccount" name="create_account">
                            <label class="form-check-label text-light" for="createAccount">
                            {{ __('messages.checkout.account_option') }} ({{ __('messages.checkout.for_new_emails_only') }})
                            </label>
                        </div>
                        <div id="accountFields" class="row g-3 mt-1 d-none">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="password" class="form-control akg-input" id="password"
                                        placeholder="{{ __('messages.forms.password') }}">
                                    <label for="password">{{ __('messages.forms.password') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="password_confirmation" class="form-control akg-input"
                                        id="password_confirmation" placeholder="{{ __('messages.forms.password_confirm') }}">
                                    <label for="password_confirmation">{{ __('messages.forms.password_confirm') }}</label>
                                </div>
                            </div>
                        </div>
                        <div id="loginNotice" class="alert alert-warning mt-2 py-2 small d-none mb-0">
                            {{ __('messages.checkout.email_registered_notice') }}
                        </div>
                    </div>
                @endif

                {{-- زر الإرسال --}}
                <div class="col-12 mt-4">
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold w-100 py-3"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                            data-size="invisible"
                            data-badge="bottomright">
                        {{ __('messages.checkout.continue') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript لإظهار الحقول فقط عند التفعيل --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chk = document.getElementById('createAccount');
            const accountFields = document.getElementById('accountFields');
            const emailInput = document.getElementById('email');
            const loginNotice = document.getElementById('loginNotice');

            function toggleAccountFields(show) {
                accountFields.classList.toggle('d-none', !show);
            }

            if (chk && accountFields) {
                chk.addEventListener('change', () => {
                    // Only allow create account when email is not registered (checked via flag)
                    if (chk.disabled) {
                        chk.checked = false;
                        return;
                    }
                    toggleAccountFields(chk.checked);
                });
            }

            // تحقق سريع عبر AJAX إذا البريد مسجّل
            if (emailInput) {
                let timeout;
                emailInput.addEventListener('blur', checkEmail);
                emailInput.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(checkEmail, 500);
                });
            }

            function checkEmail() {
                const email = emailInput.value.trim();
                if (!email) return;
                fetch(`{{ route('checkout.checkEmail') }}?email=${encodeURIComponent(email)}`, {
                    headers: {'Accept': 'application/json'}
                })
                .then(res => res.json())
                .then(data => {
                    const exists = !!data.exists;
                    if (exists) {
                        // لا يسمح بإنشاء حساب؛ يجب إدخال كلمة المرور
                        if (chk) {
                            chk.checked = false;
                            chk.disabled = true;
                        }
                        toggleAccountFields(true);
                        loginNotice.classList.remove('d-none');
                    } else {
                        if (chk) {
                            chk.disabled = false;
                        }
                        toggleAccountFields(chk && chk.checked);
                        loginNotice.classList.add('d-none');
                    }
                })
                .catch(() => {});
            }
        });
    </script>
@endsection
