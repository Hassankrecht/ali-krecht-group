@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        <div class="card shadow-lg border-warning">

            <div class="card-header bg-dark text-warning fw-bold">
                ✏️ Edit Product — {{ $product->title }}
            </div>

            <div class="card-body bg-light">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

                {{-- EDIT FORM --}}
                <form action="{{ route('admin.products.update', $product->id) }}" 
                      method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" 
                               class="form-control" 
                               value="{{ $product->title }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ $product->description }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price</label>
                        <input type="number" name="price" step="0.01" 
                               class="form-control"
                               value="{{ $product->price }}" required>
                    </div>

                    {{-- Main Image --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Main Image</label><br>

                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="img-thumbnail mb-3"
                                 style="max-width:200px;">
                        @endif

                        <input type="file" name="image" class="form-control mt-2">
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="btn btn-warning fw-semibold px-4">
                        🔄 Update Product
                    </button>

                    <a href="{{ route('admin.products.index') }}" 
                       class="btn btn-secondary px-4">❌ Cancel</a>

                </form>

                {{-- DELETE MAIN IMAGE --}}
                @if ($product->image)
                    <form action="{{ route('admin.products.mainimage.delete', $product->id) }}"
                          method="POST" 
                          class="mt-3"
                          onsubmit="return confirm('Delete ONLY the main image?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            🗑️ Delete Main Image
                        </button>
                    </form>
                @endif

                {{-- GALLERY SECTION --}}
                <h4 class="fw-bold mt-5">Gallery Images</h4>

                <div class="d-flex flex-wrap gap-3">

                    @foreach ($product->images as $img)
                        <div class="position-relative">

                            <img src="{{ asset('storage/' . $img->image) }}"
                                 style="width:120px; height:90px; object-fit:cover;"
                                 class="rounded shadow-sm">

                            {{-- Delete one gallery image --}}
                            <form action="{{ route('admin.products.image.delete', $img->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this image?')"
                                  class="position-absolute top-0 end-0 m-1">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                            </form>

                        </div>
                    @endforeach

                </div>

                {{-- ADD MORE GALLERY IMAGES --}}
                <form action="{{ route('admin.products.addImages', $product->id) }}"
                      method="POST"
                      class="mt-4"
                      enctype="multipart/form-data">

                    @csrf

                    <label class="form-label fw-semibold">Add More Images</label>
                    <input type="file" name="gallery[]" class="form-control" multiple>

                    <button class="btn btn-dark btn-sm mt-2">
                        ➕ Upload Images
                    </button>

                </form>

            </div>
        </div>

    </div>
@endsection
