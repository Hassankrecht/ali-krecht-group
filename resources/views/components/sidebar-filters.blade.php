<aside class="sidebar-filters">
    <form method="GET" action="{{ url()->current() }}">
        <div class="mb-3">
            <label class="form-label">{{ __('Price range') }}</label>
            <div class="d-flex">
                <input type="number" name="price_min" value="{{ request('price_min') }}" class="form-control me-2">
                <input type="number" name="price_max" value="{{ request('price_max') }}" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Sort by') }}</label>
            <select name="sort" class="form-select">
                <option value="newest">{{ __('Newest') }}</option>
                <option value="price_asc">{{ __('Price: Low to High') }}</option>
                <option value="price_desc">{{ __('Price: High to Low') }}</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
    </form>
</aside>