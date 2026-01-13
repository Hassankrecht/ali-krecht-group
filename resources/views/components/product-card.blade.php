<div {{ $attributes->merge(['class' => 'product-card']) }}>
    <a href="{{ route('products.show', $product->slug ?? $product->id) }}">
        <img src="{{ $product->image ?? '/assets/img/placeholder.png' }}" alt="{{ $product->title ?? 'Product' }}" class="product-image">
    </a>
    <div class="product-body">
        <h3 class="product-title">{{ $product->title ?? 'Untitled' }}</h3>
        <div class="product-price">{{ money($product->price ?? 0) }}</div>
        <div class="product-rating">@include('components.review-stars', ['rating' => $product->rating ?? 0])</div>
        <a href="{{ route('cart.add', $product->id) }}" class="btn btn-primary">{{ __('Add to cart') }}</a>
    </div>
</div>