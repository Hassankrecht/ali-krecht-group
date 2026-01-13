<form action="{{ route('coupon.apply') }}" method="POST" class="coupon-input d-flex">
    @csrf
    <input type="text" name="code" placeholder="{{ __('Coupon code') }}" class="form-control me-2" value="{{ old('code') }}">
    <button class="btn btn-success" type="submit">{{ __('Apply') }}</button>
</form>