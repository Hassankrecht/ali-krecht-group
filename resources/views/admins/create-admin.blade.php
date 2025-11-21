@extends('layouts.admin')
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-5 d-inline">Create Admins</h5>
          <form method="POST" action="{{ route('admin.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-outline mb-4 mt-4">
              <input type="email" name="email" class="form-control" placeholder="Email" required />
            </div>
            <div class="form-outline mb-4">
              <input type="text" name="name" class="form-control" placeholder="Username" required />
            </div>
            <div class="form-outline mb-4">
              <input type="password" name="password" class="form-control" placeholder="Password" required />
            </div>
            <button type="submit" class="btn btn-primary mb-4 text-center">Create</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
