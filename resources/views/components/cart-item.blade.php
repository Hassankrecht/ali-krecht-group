<div class="cart-item d-flex align-items-center">
    <img src="{{ $item['image'] ?? '/assets/img/placeholder.png' }}" alt="{{ $item['title'] }}" class="cart-thumb">
    <div class="cart-details ms-2">
        <div class="cart-title">{{ $item['title'] }}</div>
        <div class="cart-qty">{{ $item['quantity'] }} × {{ money($item['price'] ?? 0) }}</div>
    </div>
    <div class="cart-actions ms-auto">
        <form method="POST" action="{{ route('cart.decrease', $item['product_id']) }}">@csrf<button class="btn btn-sm">-</button></form>
        <form method="POST" action="{{ route('cart.increase', $item['product_id']) }}">@csrf<button class="btn btn-sm">+</button></form>
        <form method="POST" action="{{ route('cart.remove', $item['product_id']) }}">@csrf<button class="btn btn-danger btn-sm">{{ __('Remove') }}</button></form>
    </div>
</div>