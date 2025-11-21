@extends('layouts.app')

@section('title', 'Order Placed')

@section('content')
  
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 08_48_50 AM.png') }}" alt="Order placed"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content" style="padding-top: 220px;">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.thankyou_title') }}</h1>
            <p class="text-light">{{ __('messages.checkout.thankyou_sub') }}</p>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="akg-card p-4">
                <h5 class="akg-section-label">{{ __('messages.checkout.order_summary') }}</h5>
                <h2 class="akg-section-head mb-3">Order #{{ $order->id }}</h2>
                <p><strong>Name:</strong> {{ $order->name }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                <p><strong>Total:</strong> ${{ number_format($order->total_price, 2) }}</p>

                <table class="table table-dark table-striped mt-4">
                    <thead class="table-warning text-dark">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-center mt-5 d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('checkout.invoice', $order->id) }}"
                        class="btn btn-gold text-dark fw-semibold px-5 py-2">
                        🧾 {{ __('messages.checkout.download_invoice') }}
                    </a>

                    <a href="{{ route('home') }}" class="btn btn-outline-gold fw-semibold px-5 py-2">
                        {{ __('messages.checkout.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
