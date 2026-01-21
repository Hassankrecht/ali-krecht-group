@extends('layouts.app')

@section('title', __('messages.profile.title'))
@section('meta_description', __('messages.profile.meta_description'))

@section('content')
    <div class="container py-5" style="margin-top: 80px;">
        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.my_coupons') }}</a>
            <a href="{{ route('dashboard.orders') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.my_orders') }}</a>
            <a href="{{ route('dashboard.profile') }}" class="btn btn-gold text-dark">{{ __('messages.dashboard.profile') }}</a>
        </div>

        <div class="akg-card p-4">
            <h4 class="text-gold mb-3">{{ __('messages.profile.heading') }}</h4>

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
                    <label class="form-label">{{ __('messages.profile.name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.profile.email') }}</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.profile.phone') }}</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.profile.country') }}</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.profile.city') }}</label>
                        <input type="text" name="town" class="form-control" value="{{ old('town', $user->town) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.profile.zipcode') }}</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $user->zipcode) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.profile.address') }}</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.profile.new_password') }}</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.profile.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <button class="btn btn-gold text-dark fw-semibold px-4">{{ __('messages.profile.save_changes') }}</button>
            </form>
        </div>
    </div>
@endsection
