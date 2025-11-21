@extends('layouts.admin')

@section('content')
<div class="container py-5">

    <div class="card shadow-lg border-warning">

        {{-- HEADER --}}
        <div class="card-header bg-dark text-warning fw-bold d-flex justify-content-between align-items-center">
            🛠️ Projects Management

            <a href="{{ route('admin.projects.create') }}" class="btn btn-warning fw-semibold">
                ➕ Add New Project
            </a>
        </div>

        <div class="card-body bg-light">

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
            @endif


            {{-- ===================== PROJECTS TABLE ===================== --}}
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Main Image</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($projects as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- MAIN IMAGE --}}
                            <td>
                                @php
                                    $image = $item->main_image;
                                    $src = asset('assets/img/default.jpg');

                                    if ($image) {
                                        if (file_exists(public_path('storage/' . $image))) {
                                            $src = asset('storage/' . $image);
                                        } elseif (file_exists(public_path('assets/img/' . $image))) {
                                            $src = asset('assets/img/' . $image);
                                        }
                                    }
                                @endphp

                                <img src="{{ $src }}" class="rounded shadow-sm"
                                     style="width:80px; height:60px; object-fit:cover;">
                            </td>


                            {{-- TITLE --}}
                            <td class="fw-semibold">{{ $item->title }}</td>

                            {{-- STATUS --}}
                            <td>
                                @php
                                    $statusText = match ($item->status) {
                                        1 => 'Active',
                                        2 => 'Pending',
                                        3 => 'Completed',
                                        default => 'N/A',
                                    };

                                    $statusColor = match ($item->status) {
                                        1 => 'success',
                                        2 => 'warning',
                                        3 => 'secondary',
                                        default => 'dark',
                                    };
                                @endphp

                                <span class="badge bg-{{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            {{-- DATE --}}
                            <td>{{ $item->date ?? '—' }}</td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="d-flex gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.projects.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning fw-semibold px-3">
                                        ✏️ Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.projects.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this project?')">
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
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $projects->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
