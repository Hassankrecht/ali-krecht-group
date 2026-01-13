@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 text-gold"><i class="bi bi-table me-2"></i>Advanced Orders Report</h5>
            <p class="text-muted small mb-0">Filter, toggle columns, and export orders</p>
        </div>
        <a class="btn btn-sm btn-outline-gold" href="{{ route('admin.reports.orders.export', request()->query()) }}"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>

    @if(isset($summary))
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Orders</div>
                <div class="fw-bold fs-5 text-gold">{{ $summary['orders'] ?? 0 }}</div>
                <div class="small text-muted">Reg: {{ $summary['registered'] ?? 0 }} / Guest: {{ $summary['guests'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Net revenue</div>
                <div class="fw-bold fs-5 text-gold">${{ number_format($summary['net'] ?? 0,2) }}</div>
                <div class="small text-muted">Discounts: ${{ number_format($summary['discounts'] ?? 0,2) }} | Refunds: ${{ number_format($summary['refunds'] ?? 0,2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">AOV</div>
                <div class="fw-bold fs-5 text-gold">${{ number_format($summary['aov'] ?? 0,2) }}</div>
                <div class="small text-muted">Avg order value</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Coupons</div>
                <div class="fw-bold fs-6 text-gold">With: {{ $summary['with_coupon'] ?? 0 }} / Without: {{ $summary['without_coupon'] ?? 0 }}</div>
                <div class="small text-muted">
                    Top status: {{ $summary['top_status']->status ?? '—' }} ({{ $summary['top_status']->cnt ?? 0 }})
                </div>
            </div>
        </div>
    </div>
    @endif

    <form id="orderFilters" class="row g-2 mb-3" method="GET" action="{{ route('admin.reports.orders.index') }}">
        <input type="hidden" name="account" value="{{ request('account') }}">
        <input type="hidden" name="before_value" value="{{ request('before_value') }}">
        <input type="hidden" name="discount_value" value="{{ request('discount_value') }}">
        <input type="hidden" name="refund_value" value="{{ request('refund_value') }}">
        <div class="col-md-3">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Quick range</label>
            <select name="range" class="form-select">
                <option value="">Custom</option>
                @php $ranges = ['daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly','3m'=>'Last 3 months','6m'=>'Last 6 months','1y'=>'Last 1 year']; @endphp
                @foreach($ranges as $key=>$label)
                    <option value="{{ $key }}" {{ request('range')===$key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Discount filter</label>
            <select name="discount_filter" class="form-select">
                <option value="">All</option>
                <option value="with" {{ request('discount_filter')==='with'?'selected':'' }}>With discount</option>
                <option value="without" {{ request('discount_filter')==='without'?'selected':'' }}>Without discount</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Min total</label>
            <input type="number" step="0.01" name="min_total" class="form-control" value="{{ request('min_total') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Max total</label>
            <input type="number" step="0.01" name="max_total" class="form-control" value="{{ request('max_total') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Has refund</label>
            <select name="refund_filter" class="form-select">
                <option value="">All</option>
                <option value="yes" {{ request('refund_filter')==='yes'?'selected':'' }}>Yes</option>
                <option value="no" {{ request('refund_filter')==='no'?'selected':'' }}>No</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Order ID</label>
            <input type="text" name="order_id" class="form-control" value="{{ request('order_id') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">User (id/name/email)</label>
            <input type="text" name="user_search" class="form-control" value="{{ request('user_search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Coupon code</label>
            <input type="text" name="coupon_code" class="form-control" value="{{ request('coupon_code') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-gold w-100" type="submit">Apply filters</button>
        </div>

    </form>

    <div class="card card-dark p-3 mb-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="text-muted small">Toggle columns:</span>
            @php
                $cols = [
                    'col-userid' => 'User ID',
                    'col-username' => 'User Name',
                    'col-email' => 'Email',
                    'col-before' => 'Total before',
                    'col-discount' => 'Discount',
                    'col-refund' => 'Refund',
                    'col-coupon' => 'Coupon',
                ];
            @endphp
            @foreach($cols as $class => $label)
                <div class="form-check form-check-inline">
                    <input class="form-check-input toggle-col" type="checkbox" data-col="{{ $class }}" checked>
                    <label class="form-check-label small">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </div>

    @php
        $badges = [
            'status' => 'Status',
            'range' => 'Range',
            'from' => 'From',
            'to' => 'To',
            'discount_filter' => 'Discount',
            'min_total' => 'Min total',
            'max_total' => 'Max total',
            'refund_filter' => 'Refund',
            'order_id' => 'Order ID',
            'user_search' => 'User',
            'coupon_code' => 'Coupon',
        ];
    @endphp
    <div class="mb-2 d-flex flex-wrap gap-2">
        @foreach($badges as $field => $label)
            @if(request($field))
                <span class="badge bg-secondary">{{ $label }}: {{ request($field) }}</span>
            @endif
        @endforeach
        @if(collect($badges)->keys()->some(fn($f)=>request($f)))
            <a href="{{ route('admin.reports.orders.index') }}" class="badge bg-danger text-decoration-none">Reset filters</a>
        @endif
    </div>

    <div class="card card-dark p-3">
        <div class="table-responsive">
            <table class="table table-sm table-dark align-middle" id="ordersTable">
                <thead>
                    <tr>
                        <th>ID
                            @if(!empty($facets['ids']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter ID"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="order_id" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['ids'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="order_id" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-userid">User ID
                            @if(!empty($facets['user']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter user"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['user'] as $item)
                                        @php $label = ($item->value ?? '—').' - '.($item->name ?? '—'); @endphp
                                        <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="{{ $item->value }}">{{ $label }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-username">User
                            @if(!empty($facets['user']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter user name"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['user'] as $item)
                                        @php $label = ($item->name ?? '—'); @endphp
                                        <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="{{ $item->name }}">{{ $label }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-email">Email
                            @if(!empty($facets['emails']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter email"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['emails'] as $item)
                                        @if($item->value)
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>Account
                            @if(!empty($facets['account']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter account"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small">
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['account'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="account" data-value="{{ $item['value'] }}">{{ $item['label'] }} <span class="text-light">({{ $item['count'] }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>Status
                            @if(!empty($facets['status']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter status"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="status" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['status'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="status" data-value="{{ $item->value }}">{{ $item->value ?? '—' }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-before">Total before
                            @if(!empty($facets['before']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter total before"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="before_value" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['before'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="before_value" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-discount">Discount
                            @if(!empty($facets['discounts']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter discount"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="discount_value" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['discounts'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="discount_value" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>Net total
                            @if(!empty($facets['totals']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter net total"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="min_total" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['totals'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="min_total" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-refund">Refund
                            @if(!empty($facets['refunds']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter refund"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="refund_value" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['refunds'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="refund_value" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th class="col-coupon">Coupon
                            @if(!empty($facets['coupon']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter coupon"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="coupon_code" data-value="">{{ __('All') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['coupon'] as $item)
                                        @if($item->value)
                                        <a href="#" class="dropdown-item facet-option" data-field="coupon_code" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                      @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $statusLower = strtolower($o->status ?? '');
                            $rowClass = str_contains($statusLower, 'cancel') ? 'table-dark' : '';
                            $statusColor = 'secondary';
                            if(str_contains($statusLower,'paid')) $statusColor = 'success';
                            elseif(str_contains($statusLower,'pending')) $statusColor = 'warning';
                            elseif(str_contains($statusLower,'ship')) $statusColor = 'info';
                            elseif(str_contains($statusLower,'refund')) $statusColor = 'danger';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $o->id }}</td>
                            <td class="col-userid">{{ $o->user_id ?? '—' }}</td>
                            <td class="col-username">{{ $o->user_name ?? $o->guest_name ?? '—' }}</td>
                            <td class="col-email">{{ $o->user_email ?? $o->guest_email ?? '—' }}</td>
                            <td>
                                @if($o->user_id)
                                    <span class="badge bg-success">Registered</span>
                                @else
                                    <span class="badge bg-secondary">Guest</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $statusColor }}">{{ $o->status }}</span></td>
                            @php
                                $subtotal = $o->total_before_discount ?? ($o->total_price + $o->discount_amount);
                            @endphp
                            <td class="col-before" title="Subtotal ${{ number_format($subtotal,2) }}">${{ number_format($subtotal,2) }}</td>
                            <td class="col-discount">${{ number_format($o->discount_amount ?? 0,2) }}</td>
                            <td title="Net after discount">${{ number_format($o->total_price ?? 0,2) }}</td>
                            <td class="col-refund">${{ number_format($o->refund_amount ?? 0,2) }}</td>
                            <td class="col-coupon">{{ $o->coupon_code ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="11">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.toggle-col');
    toggles.forEach(chk => {
        chk.addEventListener('change', () => {
            const colClass = chk.dataset.col;
            document.querySelectorAll('.' + colClass).forEach(cell => {
                cell.style.display = chk.checked ? '' : 'none';
            });
        });
    });

    // facet dropdown: click to set filter and submit
    const facetOptions = document.querySelectorAll('.facet-option');
    const form = document.getElementById('orderFilters');
    facetOptions.forEach(opt => {
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
