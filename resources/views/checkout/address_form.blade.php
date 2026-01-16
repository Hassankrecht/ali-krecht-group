<div class="row g-3">
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="phone_number" class="form-control akg-input" placeholder="Phone Number"
                   value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" required>
            <label>Phone Number</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="town" class="form-control akg-input" placeholder="Town / City"
                   value="{{ old('town', $prefill['town'] ?? '') }}" required>
            <label>Town / City</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="country" class="form-control akg-input" placeholder="Country"
                   value="{{ old('country', $prefill['country'] ?? '') }}" required>
            <label>Country</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="zipcode" class="form-control akg-input" placeholder="Zip Code"
                   value="{{ old('zipcode', $prefill['zipcode'] ?? '') }}" required>
            <label>Zip Code</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating">
            <textarea name="address" class="form-control akg-input" style="height: 100px" placeholder="Address" required>{{ old('address', $prefill['address'] ?? '') }}</textarea>
            <label>Address</label>
        </div>
    </div>
</div>
