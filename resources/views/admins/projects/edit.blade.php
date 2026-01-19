@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        <div class="card shadow-lg border-warning">

            {{-- HEADER --}}
            <div class="card-header bg-dark text-warning fw-bold d-flex justify-content-between align-items-center">
                ✏️ Edit Project — {{ $project->title }}

                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary btn-sm">
                    ⬅ Back to Projects
                </a>
            </div>

            <div class="card-body bg-light">

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

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

                {{-- ======================= UPDATE PROJECT FORM ======================= --}}
                <form action="{{ route('admin.projects.update', $project->id) }}" method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        {{-- LEFT SIDE --}}
                        <div class="col-lg-6">

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ $project->title }}" required>
                            </div>

                            {{-- STATUS (NUMERIC VALUES) --}}
                            @php
                                $statusOptions = [
                                    1 => 'Active',
                                    2 => 'In Progress',
                                    3 => 'Completed',
                                    4 => 'Pending',
                                ];
                            @endphp

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach ($statusOptions as $key => $value)
                                        <option value="{{ $key }}" {{ $project->status == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Date</label>
                                <input type="date" name="date" class="form-control" value="{{ $project->date }}">
                            </div>

                            {{-- Categories --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Categories</label>
                                <select name="categories[]" class="form-select" multiple>
                                    @foreach($categories as $cat)
                                        <optgroup label="{{ $cat->name }}">
                                            @foreach($cat->children as $child)
                                                <option value="{{ $child->id }}" {{ $project->categories->pluck('id')->contains($child->id) ? 'selected' : '' }}>
                                                    {{ $child->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </optgroup>
                                </select>
                                <small class="text-muted">اختر فئة أو أكثر للمشروع.</small>
                            </div>

                        </div>

                        {{-- RIGHT SIDE --}}
                        <div class="col-lg-6">

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="6" class="form-control">{{ $project->description }}</textarea>
                        </div>

                        {{-- Translations --}}
                        <div class="akg-newcard p-3">
                            <h6 class="text-warning fw-bold mb-2">Translations</h6>
                            <div class="row g-3">
                                @foreach(config('app.supported_locales', []) as $locale)
                                    @continue($locale === config('app.locale'))
                                    @php $t = $project->translations->firstWhere('locale', $locale); @endphp
                                    <div class="col-md-6">
                                        <label class="form-label">Title ({{ strtoupper($locale) }})</label>
                                        <input type="text" name="translations[{{ $locale }}][title]" class="form-control" value="{{ old('translations.'.$locale.'.title', $t->title ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description ({{ strtoupper($locale) }})</label>
                                        <textarea name="translations[{{ $locale }}][description]" rows="2" class="form-control">{{ old('translations.'.$locale.'.description', $t->description ?? '') }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    </div>

                    {{-- MAIN IMAGE (WITH PREVIEW) --}}
                    <div class="mt-4">
                        <label class="form-label fw-semibold d-block">Main Image</label>

                        <div class="d-flex flex-wrap align-items-center gap-3">

                            {{-- preview --}}
                            <div>
                                @if ($project->main_image)
                                    @php
                                        $imgPath = $project->main_image;
                                        if (str_contains($imgPath, 'storage/public/assets/')) {
                                            $imgPath = str_replace('storage/public/', '', $imgPath);
                                        }
                                        $mainImg = (str_starts_with($imgPath, 'public/') || str_starts_with($imgPath, 'assets/'))
                                            ? asset($imgPath)
                                            : asset('storage/' . $imgPath);
                                    @endphp
                                    <img src="{{ $mainImg }}"
                                         class="img-thumbnail shadow-sm mb-2"
                                         style="max-width: 220px; max-height: 160px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('assets/img/default.jpg') }}"
                                         class="img-thumbnail shadow-sm mb-2"
                                         style="max-width: 220px; max-height: 160px; object-fit: cover;">
                                @endif
                            </div>

                            {{-- upload new --}}
                            <div class="flex-grow-1">
                                <input type="file" name="main_image" class="form-control">
                                <small class="text-muted">Leave empty if you don't want to change the main image.</small>
                            </div>

                        </div>
                    </div>

                    {{-- UPDATE BUTTONS --}}
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-warning fw-semibold px-4">
                            🔄 Update Project
                        </button>

                        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary px-4">
                            ❌ Cancel Edit
                        </a>
                    </div>

                </form>
                {{-- ======================= END UPDATE FORM ======================= --}}



                {{-- ======================= DELETE MAIN IMAGE (SEPARATE FORM) ======================= --}}
                @if ($project->main_image)
                    <form action="{{ route('admin.projects.mainimage.delete', $project->id) }}"
                          method="POST" class="mt-3"
                          onsubmit="return confirm('Delete ONLY the main image?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            🗑️ Delete Main Image
                        </button>

                    </form>
                @endif



                {{-- ======================= GALLERY SECTION ======================= --}}
                <h4 class="fw-bold mt-5">Gallery Images</h4>

                <div class="d-flex flex-wrap gap-3">

                    @forelse ($project->images as $image)
                        <div class="position-relative">

                            @php
                                $gPath = $image->image_path;
                                if (str_contains($gPath, 'storage/public/assets/')) {
                                    $gPath = str_replace('storage/public/', '', $gPath);
                                }
                                $gImg = (str_starts_with($gPath, 'public/') || str_starts_with($gPath, 'assets/'))
                                    ? asset($gPath)
                                    : asset('storage/' . $gPath);
                            @endphp
                            <img src="{{ $gImg }}"
                                 style="width:140px; height:100px; object-fit:cover;"
                                 class="rounded shadow-sm">

                            {{-- DELETE ONE GALLERY IMAGE --}}
                            <form action="{{ route('admin.projects.image.delete', $image->id) }}" method="POST"
                                  class="position-absolute top-0 end-0 m-1"
                                  onsubmit="return confirm('Delete this image?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>

                        </div>
                    @empty
                        <p class="text-muted">No gallery images yet.</p>
                    @endforelse

                </div>



                {{-- ======================= ADD NEW GALLERY IMAGES (SEPARATE FORM) ======================= --}}
                <form action="{{ route('admin.projects.addImages', $project->id) }}" method="POST"
                      enctype="multipart/form-data" class="mt-4">

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
