@extends('layouts.app')

@section('title', 'My Profile')
@section('meta_description', 'Manage your profile, contact details, and preferences with Ali Krecht Group. Update your information securely.')

@section('content')
    <div class="container py-5" style="margin-top: 80px;">
        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-gold">My Coupons</a>
            <a href="{{ route('dashboard.orders') }}" class="btn btn-outline-gold">My Orders</a>
            <a href="{{ route('dashboard.profile') }}" class="btn btn-gold text-dark">Profile</a>
        </div>

        <div class="akg-card p-4">
            <h4 class="text-gold mb-3">Update your information</h4>

            @if(session('success'))
                <div class="alert alert-success small">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City / Town</label>
                        <input type="text" name="town" class="form-control" value="{{ old('town', $user->town) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zipcode</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $user->zipcode) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password (optional)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <button class="btn btn-gold text-dark fw-semibold px-4">Save Changes</button>
            </form>
        </div>
    </div>
@endsection
