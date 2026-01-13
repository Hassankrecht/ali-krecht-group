@can('manage', \App\Models\Product::class)
    <div class="admin-tools">
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">{{ __('New Product') }}</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">{{ __('Reports') }}</a>
    </div>
@endcan