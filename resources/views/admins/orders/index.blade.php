@extends('layouts.admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-gold d-flex align-items-center gap-2">
        <i class="bi bi-receipt"></i> Orders
    </h5>
    <form method="GET" id="ordersFilterForm" class="d-flex align-items-center gap-2 flex-wrap">
        <input type="hidden" name="f_name" value="{{ $f['name'] ?? '' }}">
        <input type="hidden" name="f_email" value="{{ $f['email'] ?? '' }}">
        <input type="hidden" name="f_total" value="{{ $f['total'] ?? '' }}">
        <input type="hidden" name="f_discount" value="{{ $f['discount'] ?? '' }}">
        <input type="hidden" name="f_coupon" value="{{ $f['coupon'] ?? '' }}">
        <input type="hidden" name="f_account" value="{{ $f['account'] ?? '' }}">
        <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search id/email/name/coupon">
        <select name="range" class="form-select form-select-sm w-auto">
            <option value="">Custom</option>
            <option value="today" {{ ($range ?? '')==='today' ? 'selected' : '' }}>Today</option>
            <option value="last_7" {{ ($range ?? '')==='last_7' ? 'selected' : '' }}>Last 7 days</option>
            <option value="last_30" {{ ($range ?? '')==='last_30' ? 'selected' : '' }}>Last 30 days</option>
            <option value="last_month" {{ ($range ?? '')==='last_month' ? 'selected' : '' }}>Last month</option>
            <option value="last_6m" {{ ($range ?? '')==='last_6m' ? 'selected' : '' }}>Last 6 months</option>
            <option value="last_year" {{ ($range ?? '')==='last_year' ? 'selected' : '' }}>Last year</option>
        </select>
        <input type="date" name="from" value="{{ $dateFrom }}" class="form-control form-control-sm w-auto">
        <input type="date" name="to" value="{{ $dateTo }}" class="form-control form-control-sm w-auto">
        <select name="status" class="form-select form-select-sm w-auto">
            <option value="">All statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ $filterStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        <select name="has_coupon" class="form-select form-select-sm w-auto">
            <option value="">All orders</option>
            <option value="with" {{ ($hasCoupon ?? '')==='with' ? 'selected' : '' }}>With coupon</option>
            <option value="without" {{ ($hasCoupon ?? '')==='without' ? 'selected' : '' }}>Without coupon</option>
        </select>
        <select name="sort" class="form-select form-select-sm w-auto">
            <option value="date_desc" {{ ($sort ?? '')==='date_desc' ? 'selected' : '' }}>Newest</option>
            <option value="date_asc" {{ ($sort ?? '')==='date_asc' ? 'selected' : '' }}>Oldest</option>
            <option value="total_desc" {{ ($sort ?? '')==='total_desc' ? 'selected' : '' }}>Total high→low</option>
            <option value="total_asc" {{ ($sort ?? '')==='total_asc' ? 'selected' : '' }}>Total low→high</option>
        </select>
        <button class="btn btn-sm btn-outline-gold">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">Reset</a>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">Orders</div>
            <div class="fw-bold fs-5 text-gold">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">Customers</div>
            <div class="fw-bold fs-5 text-gold">{{ $customersCount }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">With coupon</div>
            <div class="fw-bold fs-5 text-gold">{{ $withCouponCount }}</div>
            <div class="text-muted small">Distinct coupons: {{ $distinctCoupons }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">Discount total</div>
            <div class="fw-bold fs-5 text-gold">${{ number_format($discountSum ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">Total amount</div>
            <div class="fw-bold fs-5 text-gold">${{ number_format($totalAmount ?? 0, 2) }}</div>
        </div>
    </div>
    @foreach($statuses as $s)
        <div class="col-md-2 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ $s }}</div>
                <div class="fw-bold fs-5 text-gold">{{ $statusCounts[$s] ?? 0 }}</div>
            </div>
        </div>
    @endforeach
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger small">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card card-dark">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer
                        @if(!empty($facets['names']))
                        <div class="dropdown d-inline">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_name" data-value="">{{ __('All') }}</a>
                                @foreach($facets['names'] as $item)
                                    @if($item->value)
                                        <a href="#" class="dropdown-item facet-option" data-field="f_name" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>Email
                        @if(!empty($facets['emails']))
                        <div class="dropdown d-inline">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_email" data-value="">{{ __('All') }}</a>
                                @foreach($facets['emails'] as $item)
                                    @if($item->value)
                                        <a href="#" class="dropdown-item facet-option" data-field="f_email" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>Total
                        @if(!empty($facets['totals']))
                        <div class="dropdown d-inline">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_total" data-value="">{{ __('All') }}</a>
                                @foreach($facets['totals'] as $item)
                                    <a href="#" class="dropdown-item facet-option" data-field="f_total" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>Discount
                        @if(!empty($facets['discounts']))
                        <div class="dropdown d-inline">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_discount" data-value="">{{ __('All') }}</a>
                                @foreach($facets['discounts'] as $item)
                                    <a href="#" class="dropdown-item facet-option" data-field="f_discount" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>Coupon
                        @if(!empty($facets['coupons']))
                        <div class="dropdown d-inline">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_coupon" data-value="">{{ __('All') }}</a>
                                <a href="#" class="dropdown-item facet-option" data-field="f_coupon" data-value="none">No coupon</a>
                                @foreach($facets['coupons'] as $item)
                                    @if($item->value)
                                        <a href="#" class="dropdown-item facet-option" data-field="f_coupon" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>${{ number_format($order->discount_amount ?? 0, 2) }}</td>
                        <td>{{ $order->coupon?->code ?? '—' }}</td>
                        <td>
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this order?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $orders->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('ordersFilterForm');
    document.querySelectorAll('.facet-option').forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.preventDefault();
            const field = opt.dataset.field;
            const value = opt.dataset.value ?? '';
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.value = value;
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
