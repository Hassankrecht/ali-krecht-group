@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="Checkout"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.hero_title') }}</h1>
            <p class="text-light">{{ __('messages.checkout.hero_sub') }}</p>
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

            <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm" class="text-light">
                @csrf
                <input type="hidden" name="total" value="{{ old('total', $total ?? 0) }}">

                {{-- ✅ إذا المستخدم مسجل دخول --}}
                @if (Auth::check())
                    <div class="alert alert-success mb-4">
                        Logged in as <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                    </div>
                    @include('checkout.address_form')

                {{-- 🚫 المستخدم غير مسجل دخول --}}
                @else
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="name" class="form-control akg-input" id="name"
                                    placeholder="Your Name" required>
                                <label for="name">Your Name</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control akg-input" id="email"
                                    placeholder="Your Email">
                                <label for="email">Your Email (optional)</label>
                            </div>
                        </div>
                    </div>

                    @include('checkout.address_form')

                    {{-- خيار إنشاء الحساب --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" value="1" id="createAccount" name="create_account">
                        <label class="form-check-label text-light" for="createAccount">
                        {{ __('messages.checkout.account_option') }}
                        </label>
                    </div>

                    {{-- الحقول التي تظهر عند التفعيل --}}
                    <div id="accountFields" class="row g-3 mt-2 d-none">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control akg-input" id="password"
                                    placeholder="Password">
                                <label for="password">Password</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password_confirmation" class="form-control akg-input"
                                    id="password_confirmation" placeholder="Confirm Password">
                                <label for="password_confirmation">Confirm Password</label>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- زر الإرسال --}}
                <div class="col-12 mt-4">
                    <button class="btn btn-gold text-dark fw-semibold w-100 py-3">
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
            if (chk && accountFields) {
                chk.addEventListener('change', () => {
                    accountFields.classList.toggle('d-none', !chk.checked);
                });
            }
        });
    </script>
@endsection
