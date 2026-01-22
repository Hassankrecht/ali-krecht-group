@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold" style="color: #c7954b;">
                    ➕ Add New Product
                </h3>
                <p class="text-muted small mb-0">Create a new product with details and images</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-gold fw-semibold px-4">
                ← Back
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <strong>Whoops!</strong> Fix the issues below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-dark">

            <div class="card-body p-4">

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

                {{-- CREATE PRODUCT FORM --}}
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    {{-- Category --}}
                    <div class="mb-3">
                        <div class="card border mb-2" style="background: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold" style="color: #c7954b;">Product Category</h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addParentCatModal">+ Parent</button>
                                        <button type="button" class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addChildCatModal">+ Child</button>
                                    </div>
                                </div>
                                <label class="form-label fw-semibold">Select Category</label>
                                <select name="category_id" class="form-select" required id="categorySelect">
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
                        <small class="text-muted">Select one child category for this product.</small>
                            </div>
                        </div>
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
                        <h5 class="fw-bold mb-3" style="color: #c7954b;">Translations</h5>
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
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-gold fw-semibold px-4">
                            ➕ Create Product
                        </button>

                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark px-4">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@push('modals')
{{-- ADD PARENT CATEGORY MODAL --}}
<div class="modal fade" id="addParentCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Parent Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Parent categories organize your product categories.</p>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" id="parentCatName" class="form-control" placeholder="e.g., Doors">
                </div>
                <div class="row g-2">
                    @foreach(config('app.supported_locales', []) as $locale)
                        @continue($locale === config('app.locale'))
                        <div class="col-md-6">
                            <label class="form-label">Name ({{ strtoupper($locale) }})</label>
                            <input type="text" id="parentCatName_{{ $locale }}" class="form-control">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gold" onclick="saveParentCategory()">Save Category</button>
            </div>
        </div>
    </div>
</div>

{{-- ADD CHILD CATEGORY MODAL --}}
<div class="modal fade" id="addChildCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Child Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Parent Category</label>
                    <select id="childCatParent" class="form-select">
                        <option value="">Select Parent</option>
                        @php $parents = $categories->whereNull('parent_id'); @endphp
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" id="childCatName" class="form-control" placeholder="e.g., Sliding Doors">
                </div>
                <div class="row g-2">
                    @foreach(config('app.supported_locales', []) as $locale)
                        @continue($locale === config('app.locale'))
                        <div class="col-md-6">
                            <label class="form-label">Name ({{ strtoupper($locale) }})</label>
                            <input type="text" id="childCatName_{{ $locale }}" class="form-control">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gold" onclick="saveChildCategory()">Save Category</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
function saveParentCategory() {
    const name = document.getElementById('parentCatName').value;
    if (!name) {
        alert('Please enter category name');
        return;
    }
    
    const translations = {};
    @foreach(config('app.supported_locales', []) as $locale)
        @continue($locale === config('app.locale'))
        translations['{{ $locale }}'] = {
            name: document.getElementById('parentCatName_{{ $locale }}').value || name
        };
    @endforeach
    
    fetch('{{ route('admin.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name, translations })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save category'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save category');
    });
}

function saveChildCategory() {
    const parentId = document.getElementById('childCatParent').value;
    const name = document.getElementById('childCatName').value;
    
    if (!parentId || !name) {
        alert('Please select parent and enter category name');
        return;
    }
    
    const translations = {};
    @foreach(config('app.supported_locales', []) as $locale)
        @continue($locale === config('app.locale'))
        translations['{{ $locale }}'] = {
            name: document.getElementById('childCatName_{{ $locale }}').value || name
        };
    @endforeach
    
    fetch('{{ route('admin.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name, parent_id: parentId, translations })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save category'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save category');
    });
}
</script>
@endpush
