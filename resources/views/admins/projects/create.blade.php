@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-warning fw-bold">
                ➕ Add New Project
            </h3>

            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-warning fw-semibold px-4">
                ← Back
            </a>
        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <strong>Whoops!</strong> Fix the issue below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        {{-- CARD FORM --}}
        <div class="card shadow-lg border-warning bg-dark text-light">
            <div class="card-body p-5">

                <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">

                        {{-- TITLE --}}
                        <div class="col-md-6">
                            <label class="form-label text-warning fw-semibold">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-control bg-transparent text-light border-warning" required>
                        </div>

                        {{-- LOCATION --}}
                        <div class="col-md-6">
                            <label class="form-label text-warning fw-semibold">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                class="form-control bg-transparent text-light border-warning">
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-12">
                            <label class="form-label text-warning fw-semibold">Description</label>
                            <textarea name="description" rows="4"
                                class="form-control bg-transparent text-light border-warning">{{ old('description') }}</textarea>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-4">
                            <label class="form-label text-warning fw-semibold">Status</label>
                            <select name="status" class="form-select bg-transparent text-light border-warning">
                                <option value="1">Active</option>
                                <option value="2">Pending</option>
                                <option value="3">Completed</option>
                            </select>
                        </div>

                        {{-- DATE --}}
                        <div class="col-md-4">
                            <label class="form-label text-warning fw-semibold">Date</label>
                            <input type="date" name="date" value="{{ old('date') }}"
                                class="form-control bg-transparent text-light border-warning">
                        </div>

                        {{-- MAIN IMAGE --}}
                        <div class="col-md-4">
                            <label class="form-label text-warning fw-semibold">Main Image</label>
                            <input type="file" name="main_image"
                                class="form-control bg-transparent text-light border-warning" accept="image/*">
                        </div>

                        {{-- GALLERY --}}
                        <div class="col-12">
                            <label class="form-label text-warning fw-semibold">Gallery Images (Multiple)</label>
                            <input type="file" name="gallery[]" multiple
                                class="form-control bg-transparent text-light border-warning" accept="image/*">
                            <small class="text-muted">Optional — You can upload several images.</small>
                        </div>

                    </div>

                    {{-- SUBMIT --}}
                    <div class="text-center mt-4">
                        <button class="btn btn-warning fw-semibold text-dark px-5 py-2">
                            ✔ Save Project
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
