@extends('layouts.app')

@section('content')

{{-- ========================================================= --}}
{{-- 🔥 PREMIUM HERO SECTION --}}
{{-- ========================================================= --}}

<div class=" akg-hero-img-box">
    <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="Your Cart" class="akg-hero-img"
        loading="lazy">

    <div class="container text-center hero-content">
        <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.cart.hero_title') }}</h1>

        <ol class="breadcrumb justify-content-center text-uppercase">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item text-light active">Cart</li>
        </ol>
    </div>
</div>


{{-- ========================================================= --}}
{{-- PAGE CONTENT --}}
{{-- ========================================================= --}}

<div class="container-xxl py-5">
    <div class="container akg-newcard">

    {{-- SUCCESS / ERROR --}}
    @if (session('success'))
        <div class="alert alert-success text-center fw-semibold shadow-sm">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger text-center fw-semibold shadow-sm">{{ session('error') }}</div>
    @endif
    <div id="cartNotice" class="alert d-none text-center fw-semibold shadow-sm" role="alert"></div>

    {{-- EMPTY CART --}}
    @if (empty($cartItems) || count($cartItems) === 0)

        <div class="akg-card text-center py-5">
            <i class="bi bi-cart-x display-1 text-gold"></i>
            <h3 class="mt-3 text-light fw-bold">{{ __('messages.cart.empty_title') }}</h3>
            <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                <a href="{{ route('products.index') }}" class="btn btn-gold fw-semibold px-4">{{ __('messages.cart.empty_cta_shop') }}</a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-gold fw-semibold px-4">{{ __('messages.cart.empty_cta_projects') }}</a>
            </div>
        </div>

        <style>
            .empty-cart-box {
                background: #111;
                border: 1px solid #333;
                border-radius: 15px;
            }
        </style>

    @else

        {{-- ======================== CART TABLE ======================== --}}
        <div class="table-responsive rounded-4 shadow-lg mb-4">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr class="text-center text-gold">
                        <th>Image</th>
                        <th>Product</th>
                        <th width="180">Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>

                <tbody>
                    @php $grandTotal = 0; @endphp

                    @foreach ($cartItems as $id => $item)
                        @php
                            $title = $item['title'];
                            $imgPath = $item['image'];
                            $itemTotal = $item['price'] * $item['quantity'];
                            $grandTotal += $itemTotal;
                        @endphp

                        <tr data-row="{{ $id }}">
                            {{-- PRODUCT IMAGE --}}
                            <td class="text-center">
                                <img src="{{ $imgPath ? asset('storage/'.$imgPath) : asset('assets/img/default.jpg') }}"
                                     class="rounded shadow-sm"
                                     style="width: 70px; height: 60px; object-fit: cover;" alt="{{ $title }}"
                                     loading="lazy">
                            </td>

                            {{-- TITLE --}}
                            <td class="fw-semibold">{{ $title }}</td>

                            {{-- QUANTITY --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-gold update-cart"
                                            data-action="decrease" data-id="{{ $id }}">−</button>

                                    <span class="fw-bold fs-6 cart-qty px-2 py-1 rounded border border-gold" data-id="{{ $id }}">{{ $item['quantity'] }}</span>

                                    <button class="btn btn-sm btn-outline-gold update-cart"
                                            data-action="increase" data-id="{{ $id }}">+</button>
                                </div>
                            </td>

                            {{-- PRICE --}}
                            <td>${{ number_format($item['price'], 2) }}</td>

                            {{-- ITEM TOTAL --}}
                            <td class="fw-bold text-gold item-total" data-id="{{ $id }}">${{ number_format($itemTotal, 2) }}</td>

                            {{-- REMOVE --}}
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger remove-item" data-id="{{ $id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ======================== SUMMARY BOX ======================== --}}
        <div class="shadow-lg p-4 rounded-4 bg-dark text-light border gold-border mt-4 ms-auto" style="max-width: 420px;">
            <h4 class="fw-bold mb-3">{{ __('messages.cart.order_summary') }}</h4>

            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <span class="gold-text fw-bold" id="grandTotal">${{ number_format($grandTotal, 2) }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span>Tax:</span>
                <span class="text-muted">Included</span>
            </div>

            <hr>

            <div class="d-flex justify-content-between fs-4 mb-3">
                <span class="fw-bold">Total:</span>
                <span class="gold-text fw-bold">${{ number_format($grandTotal, 2) }}</span>
            </div>

            <a href="{{ route('checkout.index') }}" class="btn btn-gold fw-semibold w-100 py-2">
                {{ __('messages.cart.proceed_checkout') }}
            </a>
        </div>

    @endif
</div>
</div>


{{-- ========================================================= --}}
{{-- AJAX CART UPDATE --}}
{{-- ========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = "{{ csrf_token() }}";
    const notice = document.getElementById('cartNotice');
    const emptyBox = document.querySelector('.empty-cart-box');
    const grandTotalEl = document.getElementById('grandTotal');

    function showNotice(message, type = 'success') {
        if (!notice) return;
        notice.classList.remove('d-none', 'alert-success', 'alert-danger');
        notice.classList.add(type === 'error' ? 'alert-danger' : 'alert-success');
        notice.textContent = message;
        setTimeout(() => notice.classList.add('d-none'), 2500);
    }

    function updateCart(action, id) {
        const row = document.querySelector(`tr[data-row="${id}"]`);
        fetch(`/cart/${action}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.removed || data.item_quantity === 0) {
                row?.remove();
            } else {
                const qtyEl = document.querySelector(`.cart-qty[data-id="${id}"]`);
                const itemTotalEl = document.querySelector(`.item-total[data-id="${id}"]`);
                if (qtyEl) qtyEl.textContent = data.item_quantity;
                if (itemTotalEl) itemTotalEl.textContent = `$${data.item_total}`;
            }

            if (grandTotalEl && data.grand_total) {
                grandTotalEl.textContent = `$${data.grand_total}`;
            }

            // If cart is empty, show message
            const rowsLeft = document.querySelectorAll('tr[data-row]').length;
            if (rowsLeft === 0 && emptyBox) {
                emptyBox.classList.remove('d-none');
            }

            showNotice(action === 'remove' ? 'Item removed.' : 'Cart updated.');
        })
        .catch(() => showNotice('Update failed. Try again.', 'error'));
    }

    document.querySelectorAll('.update-cart').forEach(btn => {
        btn.addEventListener('click', () => updateCart(btn.dataset.action, btn.dataset.id));
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', () => updateCart('remove', btn.dataset.id));
    });
});
</script>

@endsection
