@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        <div class="card shadow-lg border-warning">

            <div class="card-header bg-dark text-warning fw-bold">
                ➕ Add New Product
            </div>

            <div class="card-body bg-light">

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

                {{-- CREATE PRODUCT FORM --}}
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @php
                                $parents = $categories->whereNull('parent_id');
                            @endphp
                            @foreach ($parents as $parent)
                                <option disabled>— {{ $parent->name }} —</option>
                                @foreach ($parent->children->where('parent_id', $parent->id)->unique('id') as $child)
                                    <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;{{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title') }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    {{-- Translations --}}
                    <div class="mb-4">
                        <h5 class="fw-bold text-warning">Translations</h5>
                        @foreach (config('app.supported_locales', []) as $locale)
                            @continue($locale === config('app.locale'))
                            <div class="border rounded p-3 mb-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-uppercase">{{ $locale }}</span>
                                    @if ($locale === config('app.locale'))
                                        <small class="text-muted">Fallback language</small>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Title ({{ $locale }})</label>
                                    <input type="text" name="translations[{{ $locale }}][title]" class="form-control"
                                        value="{{ old('translations.' . $locale . '.title') }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small mb-1">Description ({{ $locale }})</label>
                                    <textarea name="translations[{{ $locale }}][description]" rows="3" class="form-control">{{ old('translations.' . $locale . '.description') }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price</label>
                        <input type="number" step="0.01" name="price" class="form-control"
                            value="{{ old('price') }}" required>
                    </div>

                    {{-- Main Image --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Main Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    {{-- GALLERY --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gallery Images</label>
                        <input type="file" name="gallery[]" class="form-control" multiple>
                    </div>

                    {{-- SUBMIT --}}
                    <button class="btn btn-warning fw-semibold px-4">
                        ➕ Create Product
                    </button>

                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4">
                        ❌ Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>
@endsection
