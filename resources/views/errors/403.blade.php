@extends('layouts.app')

@section('title', '403 - Forbidden')
@section('meta_description', 'Access denied. You do not have permission to view this page. Return to Ali Krecht Group home or contact support.')

@section('content')
<div class="error-container min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="error-content p-5">
                    <h1 class="display-1 fw-bold text-warning mb-3">403</h1>
                    <h2 class="fs-3 mb-3">Access Forbidden</h2>
                    <p class="fs-5 text-muted mb-4">
                        You don't have permission to access this resource. 
                        @auth
                            If you believe this is an error, please contact support.
                        @else
                            Please <a href="{{ route('login') }}">log in</a> to continue.
                        @endauth
                    </p>
                    
                    <div class="error-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        @else
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-shopping-bag"></i> Browse
                            </a>
                        @endguest
                    </div>

                    <div class="mt-5 pt-5 border-top">
                        <p class="text-muted">
                            <small>
                                Have questions? <a href="{{ route('contact') }}">Contact us</a>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-container {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .error-content {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .error-content h1 {
        font-size: 5rem;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .error-content h1 {
            font-size: 3rem;
        }
    }
</style>
@endsection
