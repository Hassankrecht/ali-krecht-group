<div class="checkout-summary card">
    <div class="card-body">
        <h4>{{ __('Order Summary') }}</h4>
        <ul class="list-unstyled">
            @foreach($items as $item)
                <li>{{ $item['quantity'] }} × {{ $item['title'] }} <span class="float-end">{{ money($item['price'] * $item['quantity']) }}</span></li>
            @endforeach
        </ul>
        <hr>
        <div class="d-flex justify-content-between"><strong>{{ __('Subtotal') }}</strong><span>{{ money($subtotal) }}</span></div>
        <div class="d-flex justify-content-between"><strong>{{ __('Discount') }}</strong><span>-{{ money($discount) }}</span></div>
        <div class="d-flex justify-content-between"><strong>{{ __('Total') }}</strong><span>{{ money($total) }}</span></div>
    </div>
</div>