@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-dark p-4">
                @if(session('success'))
                    <div class="alert alert-success small">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger small mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-gear"></i> Edit Admin
                    </h5>
                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-sm btn-outline-dark">Back</a>
                </div>
                <form method="POST" action="{{ route('admin.admin-users.update', $admin->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-outline mb-3">
                        <label class="form-label small text-muted">Email</label>
                        <input type="email" name="email" value="{{ $admin->email }}" class="form-control" placeholder="Email" required />
                    </div>

                    <div class="form-outline mb-3">
                        <label class="form-label small text-muted">Username</label>
                        <input type="text" name="name" value="{{ $admin->name }}" class="form-control" placeholder="Username" required />
                    </div>

                    <div class="form-outline mb-4">
                        <label class="form-label small text-muted">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password" />
                    </div>

                    <button type="submit" class="btn btn-gold w-100 fw-semibold">Update Admin</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
