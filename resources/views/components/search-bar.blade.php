<form action="{{ route('products.index') }}" method="GET" class="search-bar d-flex">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search products...') }}" class="form-control me-2">
    <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
</form>