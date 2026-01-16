@extends('layouts.app')

@section('title', 'Confirm Order')
@section('meta_description', 'Review and confirm your order for luxury products and services from Ali Krecht Group. Secure checkout process.')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="Order confirmation"
             class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">Order Confirmation</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Order Confirmation</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <h5 class="akg-section-label">Review</h5>
            <h2 class="akg-section-head mb-4">Please confirm your details</h2>

            {{-- Customer info --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">Customer info</h6>
                <p><strong>Name:</strong> {{ $data['name'] ?? '—' }}</p>
                <p><strong>Email:</strong> {{ $data['email'] ?? '—' }}</p>
                <p><strong>Phone:</strong> {{ $data['phone_number'] ?? '—' }}</p>
                <p><strong>Address:</strong> {{ $data['address'] ?? '—' }}, {{ $data['town'] ?? '' }}, {{ $data['country'] ?? '' }} - {{ $data['zipcode'] ?? '' }}</p>
            </div>

            {{-- Order items --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">Order summary</h6>
                <table class="table table-dark table-striped align-middle">
                    <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Price</th>
                        <th scope="col">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>{{ $item['title'] ?? $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-warning text-dark fw-bold">
                        <td colspan="3" class="text-end">Subtotal:</td>
                        <td>${{ number_format($total, 2) }}</td>
                    </tr>
                    @if(!empty($applied))
                        <tr class="table-warning text-dark fw-bold">
                            <td colspan="3" class="text-end">Discount ({{ $applied['code'] }}):</td>
                            <td>- ${{ number_format($discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="table-warning text-dark fw-bold">
                        <td colspan="3" class="text-end">Total to pay:</td>
                        <td>${{ number_format($totalAfter ?? $total, 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <h6 class="text-gold mb-2">Payment</h6>
                <p class="text-muted mb-1">Payment method: WiShe Money</p>
                <h6 class="text-gold mb-2">Delivery</h6>
                <p class="text-muted mb-0">Delivery fees/details will be arranged by phone or email after confirmation.</p>
            </div>

            {{-- Final confirm --}}
            <form method="POST" action="{{ route('checkout.process') }}" class="{{ Auth::check() ? '' : 'js-recaptcha' }}">
                @csrf
                @unless(Auth::check())
                    <input type="hidden" name="g-recaptcha-response">
                @endunless
                @foreach ($data as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="total" value="{{ $totalAfter ?? $total }}">

                <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-gold py-2 px-4">← Edit info</a>
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold py-2 px-5"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                            data-size="invisible"
                            data-badge="bottomright">
                        Confirm your order
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
