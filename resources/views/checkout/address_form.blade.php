<div class="row g-3">
    <div class="col-md-6">
        <div class="form-floating">
                 <input type="text" name="phone_number" class="form-control akg-input" placeholder="{{ __('messages.forms.phone') }}"
                   value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" required>
                 <label>{{ __('messages.forms.phone') }}</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
                 <input type="text" name="town" class="form-control akg-input" placeholder="{{ __('messages.forms.town') }}"
                   value="{{ old('town', $prefill['town'] ?? '') }}" required>
                 <label>{{ __('messages.forms.town') }}</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
                 <input type="text" name="country" class="form-control akg-input" placeholder="{{ __('messages.forms.country') }}"
                   value="{{ old('country', $prefill['country'] ?? '') }}" required>
                 <label>{{ __('messages.forms.country') }}</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
                 <input type="text" name="zipcode" class="form-control akg-input" placeholder="{{ __('messages.forms.zip') }}"
                   value="{{ old('zipcode', $prefill['zipcode'] ?? '') }}" required>
                 <label>{{ __('messages.forms.zip') }}</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating">
            <textarea name="address" class="form-control akg-input" style="height: 100px" placeholder="{{ __('messages.forms.address') }}" required>{{ old('address', $prefill['address'] ?? '') }}</textarea>
            <label>{{ __('messages.forms.address') }}</label>
        </div>
    </div>
</div>
