@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        <div class="card shadow-lg border-warning">

            {{-- HEADER --}}
            <div class="card-header bg-dark text-warning fw-bold d-flex justify-content-between align-items-center">
                🛒 Products Management

                <div class="d-flex gap-2">

                    {{-- ADD PRODUCT --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-warning fw-semibold">
                        ➕ Add New Product
                    </a>
                </div>
            </div>

            <div class="card-body bg-light">

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif



                {{-- ================= CATEGORIES CHIP STYLE ================= --}}
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-folder text-warning"></i> Categories</span>

                        <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            ➕ Add Category
                        </button>
                    </div>

                    <div class="card-body">

                        <div class="d-flex flex-wrap gap-2">

                            {{-- ALL PRODUCTS BUTTON --}}
                            <a href="{{ route('admin.products.index') }}" class="category-chip {{ !isset($selectedCategory) ? 'active-chip' : '' }}">
                                All
                            </a>

                            {{-- CATEGORY CHIPS --}}
                            @foreach ($categories as $category)
                                <a href="{{ route('admin.products.category', $category->id) }}"
                                    class="category-chip {{ isset($selectedCategory) && $selectedCategory->id == $category->id ? 'active-chip' : '' }}">
                                    
                                    <span class="chip-text">{{ $category->name }}</span>

                                    <span class="badge bg-warning text-dark ms-2">
                                        {{ $category->products()->count() }}
                                    </span>

                                    {{-- EDIT BUTTON --}}
                                    <button class="chip-btn edit" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->id }}">
                                        ✏️
                                    </button>

                                    {{-- DELETE BUTTON --}}
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="chip-btn delete">🗑️</button>
                                    </form>
                                </a>
                            @endforeach

                        </div>

                    </div>
                </div>

                {{-- ========== CATEGORY CHIP CSS ========== --}}
                <style>
                    .category-chip {
                        background: #f5f5f5;
                        padding: 6px 14px;
                        border-radius: 20px;
                        font-weight: 600;
                        border: 1px solid #ddd;
                        transition: all .2s ease;
                        display: inline-flex;
                        align-items: center;
                        text-decoration: none;
                        color: #000;
                    }
                    .category-chip:hover {
                        background: #e3e3e3;
                    }
                    .active-chip {
                        background: #ffe08a !important;
                        border-color: #d6a500 !important;
                    }
                    .chip-btn {
                        border: none;
                        background: transparent;
                        font-size: 14px;
                        cursor: pointer;
                        opacity: .6;
                        margin-left: 6px;
                    }
                    .chip-btn:hover { opacity: 1; }
                    .chip-btn.delete:hover { color: red; }
                    .chip-btn.edit:hover { color: #d4a215; }
                </style>



                {{-- ========== ADD CATEGORY MODAL ========== --}}
                <div class="modal fade" id="addCategoryModal" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <form action="{{ route('admin.categories.store') }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Add Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="text" name="name" class="form-control" placeholder="Category name" required>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-primary">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



                {{-- ========== EDIT CATEGORY MODALS ========== --}}
                @foreach ($categories as $category)
                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $category->name }}" required>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach



                {{-- ===================== PRODUCTS TABLE ===================== --}}
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Main Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- MAIN IMAGE --}}
                                <td>
                                    @php
                                        $src = asset('assets/img/default.jpg');
                                        if ($item->image && file_exists(public_path('storage/' . $item->image))) {
                                            $src = asset('storage/' . $item->image);
                                        }
                                    @endphp

                                    <img src="{{ $src }}" class="rounded shadow-sm"
                                        style="width:80px; height:60px; object-fit:cover;">
                                </td>

                                <td class="fw-semibold">{{ $item->title }}</td>

                                <td>{{ $item->category->name ?? '—' }}</td>

                                <td>${{ number_format($item->price, 2) }}</td>

                                <td>
                                    <div class="d-flex gap-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('admin.products.edit', $item->id) }}"
                                            class="btn btn-sm btn-warning fw-semibold px-3">
                                            ✏️ Edit
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('admin.products.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger fw-semibold px-3">
                                                🗑️ Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // منع تشغيل الرابط عند الضغط على Edit أو Delete داخل ال-chip
        document.querySelectorAll('.chip-btn').forEach(btn => {
            btn.addEventListener('click', function (event) {
                event.stopPropagation();  
                event.preventDefault();  
            });
        });

    });
</script>

@endsection
