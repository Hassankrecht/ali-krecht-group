@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-5 d-inline">Edit Admin</h5>
                    <form method="POST" action="{{ route('admin.update', $admin->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-outline mb-4 mt-4">
                            <input type="email" name="email" value="{{ $admin->email }}" class="form-control" placeholder="Email" />
                        </div>

                        <div class="form-outline mb-4">
                            <input type="text" name="name" value="{{ $admin->name }}" class="form-control" placeholder="Username" />
                        </div>

                        <div class="form-outline mb-4">
                            <input type="password" name="password" class="form-control" placeholder="New Password (Leave blank to keep current)" />
                        </div>

                        <button type="submit" class="btn btn-primary">Update Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
