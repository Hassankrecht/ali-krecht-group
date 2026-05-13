@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 text-gold"><i class="bi bi-cash-coin me-2"></i>Income Dashboard</h5>
            <p class="text-muted small mb-0">Revenue & orders overview</p>
        </div>
        <form class="d-flex flex-wrap align-items-center gap-2" method="GET" action="{{ route('admin.income.index') }}" id="incomeFilterForm">
            <select name="range" class="form-select form-select-sm w-auto" id="rangeSelect" onchange="handleRangeChange(this)">
                <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                <option value="last_7" {{ $range === 'last_7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="last_30" {{ $range === 'last_30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="last_90" {{ $range === 'last_90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="last_180" {{ $range === 'last_180' ? 'selected' : '' }}>Last 6 months</option>
                <option value="last_365" {{ $range === 'last_365' ? 'selected' : '' }}>Last year</option>
                <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom range</option>
            </select>

            <select name="source_platform" class="form-select form-select-sm w-auto" onchange="document.getElementById('incomeFilterForm').submit()">
                <option value="" {{ empty($sourcePlatform) ? 'selected' : '' }}>All platforms</option>
                <option value="web" {{ $sourcePlatform === 'web' ? 'selected' : '' }}>Website</option>
                <option value="android" {{ $sourcePlatform === 'android' ? 'selected' : '' }}>Android app</option>
                <option value="ios" {{ $sourcePlatform === 'ios' ? 'selected' : '' }}>iOS app</option>
                <option value="unknown" {{ $sourcePlatform === 'unknown' ? 'selected' : '' }}>Unknown</option>
            </select>
            <div id="customRange" class="d-flex gap-2 {{ $range === 'custom' ? '' : 'd-none' }}">
                <input type="date" name="from" id="fromDate" value="{{ $from }}" class="form-control form-control-sm">
                <span class="text-muted">to</span>
                <input type="date" name="to" id="toDate" value="{{ $to }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-gold btn-sm">Apply</button>
            </div>
            <span class="badge bg-dark text-gold ms-auto">{{ $from }} → {{ $to }}</span>
        </form>
    </div>

    <div class="mb-3">
        <a class="btn btn-outline-gold btn-sm" href="{{ route('admin.income.export', request()->query()) }}">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Gross revenue</div>
                <h4 class="mb-0 text-gold">${{ number_format($gross,2) }}</h4>
                <small class="text-muted">Paid orders (before discount)</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-muted small">Net revenue</div>
                    @if(!is_null($growthRevenue))
                        <span class="badge {{ $growthRevenue >=0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growthRevenue >=0 ? '↑' : '↓' }} {{ number_format(abs($growthRevenue),1) }}%
                        </span>
                    @endif
                </div>
                <h4 class="mb-0 text-gold">${{ number_format($net,2) }}</h4>
                <small class="text-muted">Paid orders (after discount)</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Total discounts</div>
                <h4 class="mb-0 text-gold">${{ number_format($discounts,2) }}</h4>
                <small class="text-muted">Coupon applied</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Refunds</div>
                <h4 class="mb-0 text-gold">${{ number_format($refunds,2) }}</h4>
                <small class="text-muted">Refunded orders</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Gross revenue with discount</div>
                <h4 class="mb-0 text-gold">${{ number_format($grossWithDiscount,2) }}</h4>
                <small class="text-muted">{{ $ordersWithDiscount }} orders</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Net revenue with discount</div>
                <h4 class="mb-0 text-gold">${{ number_format($netWithDiscount,2) }}</h4>
                <small class="text-muted">After coupon applied</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Revenue without discount</div>
                <h4 class="mb-0 text-gold">${{ number_format($revenueWithoutDiscount,2) }}</h4>
                <small class="text-muted">{{ $ordersWithoutDiscount }} orders</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Orders paid</div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0 text-gold">{{ $ordersCount }}</h4>
                    @if(!is_null($growthOrders))
                        <span class="badge {{ $growthOrders >=0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growthOrders >=0 ? '↑' : '↓' }} {{ number_format(abs($growthOrders),1) }}%
                        </span>
                    @endif
                </div>
                <small class="text-muted">Total paid orders</small>
            </div>
        </div>
    </div>


    <div class="row g-3 mb-3">
        @php
            $webPlatform = $platformSummary['web'] ?? null;
            $androidPlatform = $platformSummary['android'] ?? null;
            $iosPlatform = $platformSummary['ios'] ?? null;
            $unknownPlatform = $platformSummary['unknown'] ?? null;
        @endphp
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Website revenue</div>
                <h5 class="mb-0 text-gold">${{ number_format($webPlatform->revenue ?? 0,2) }}</h5>
                <small class="text-muted">{{ $webPlatform->orders ?? 0 }} paid orders</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Android revenue</div>
                <h5 class="mb-0 text-gold">${{ number_format($androidPlatform->revenue ?? 0,2) }}</h5>
                <small class="text-muted">{{ $androidPlatform->orders ?? 0 }} paid orders</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">iOS revenue</div>
                <h5 class="mb-0 text-gold">${{ number_format($iosPlatform->revenue ?? 0,2) }}</h5>
                <small class="text-muted">{{ $iosPlatform->orders ?? 0 }} paid orders</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Unknown revenue</div>
                <h5 class="mb-0 text-gold">${{ number_format($unknownPlatform->revenue ?? 0,2) }}</h5>
                <small class="text-muted">{{ $unknownPlatform->orders ?? 0 }} old paid orders</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">AOV</div>
                <h5 class="mb-0 text-gold">${{ number_format($avgOrderValue,2) }}</h5>
                <small class="text-muted">Average order value (paid orders)</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">ADR</div>
                <h5 class="mb-0 text-gold">${{ number_format($avgDailyRevenue,2) }}</h5>
                <small class="text-muted">Average daily revenue</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0">Revenue by day</h6>
                    <small class="text-muted">Paid orders only</small>
                </div>
                <canvas id="revenueChart" height="140"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0">Orders by day</h6>
                    <small class="text-muted">Paid orders only</small>
                </div>
                <canvas id="ordersChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="card card-dark p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Orders overview</h6>
            <small class="text-muted">Paid orders only - Paginated</small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-dark align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Platform
                            @if(!empty($platformBreakdown))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter platform"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item platform-filter-option" data-value="">{{ __('All') }}</a>
                                    @foreach($platformBreakdown as $item)
                                        @php
                                            $platformValue = $item->platform ?? 'unknown';
                                            $platformLabel = match($platformValue) {
                                                'web' => 'Website',
                                                'android' => 'Android app',
                                                'ios' => 'iOS app',
                                                default => 'Unknown',
                                            };
                                        @endphp
                                        <a href="#" class="dropdown-item platform-filter-option" data-value="{{ $platformValue }}">{{ $platformLabel }} <span class="text-light">({{ $item->orders }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Net total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @php
                                    $status_lower = strtolower($order->status ?? '');
                                    $badge_class = match($status_lower) {
                                        'paid', 'completed' => 'bg-success',
                                        'refunded' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge_class }}">{{ $order->status }}</span>
                            </td>
                            <td><span class="badge bg-dark text-gold">{{ $order->source_platform ?: 'unknown' }}</span></td>
                            <td>${{ number_format($order->total_before_discount ?? ($order->total_price + $order->discount_amount), 2) }}</td>
                            <td>${{ number_format($order->discount_amount,2) }}</td>
                            <td><strong class="text-gold">${{ number_format($order->total_price,2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-3">No data</td></tr>
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
    document.querySelectorAll('.platform-filter-option').forEach(option => {
        option.addEventListener('click', (event) => {
            event.preventDefault();
            const select = document.querySelector('#incomeFilterForm [name="source_platform"]');
            if (select) {
                select.value = option.dataset.value ?? '';
                document.getElementById('incomeFilterForm').submit();
            }
        });
    });
});

function handleRangeChange(select) {
    const customRange = document.getElementById('customRange');
    const fromInput = document.getElementById('fromDate');
    const toInput = document.getElementById('toDate');
    
    if (select.value === 'custom') {
        customRange.classList.remove('d-none');
        fromInput.focus();
    } else {
        customRange.classList.add('d-none');
        // Clear custom date inputs so the range parameter is used
        fromInput.value = '';
        toInput.value = '';
        // Submit the form immediately
        setTimeout(() => {
            document.getElementById('incomeFilterForm').submit();
        }, 0);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const labels = {!! json_encode($chartLabels) !!};
    const revenueTotal = {!! json_encode($chartRevenue) !!};
    const revenueWithDisc = {!! json_encode($chartRevenueWithDiscount) !!};
    const revenueWithoutDisc = {!! json_encode($chartRevenueWithoutDiscount) !!};
    const ordersTotal = {!! json_encode($chartOrders) !!};
    const ordersWithDisc = {!! json_encode($chartOrdersWithDiscount) !!};
    const ordersWithoutDisc = {!! json_encode($chartOrdersWithoutDiscount) !!};

    // Revenue Chart: Total + with/without discount
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const ctx = revenueCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, revenueCtx.height);
        gradient.addColorStop(0, 'rgba(199,149,75,0.35)');
        gradient.addColorStop(1, 'rgba(199,149,75,0.02)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total revenue',
                        data: revenueTotal,
                        borderColor: '#c7954b', // warm gold
                        backgroundColor: gradient,
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    },
                    {
                        label: 'Revenue with discount',
                        data: revenueWithDisc,
                        borderColor: '#5b8def', // blue
                        backgroundColor: 'rgba(91,141,239,0.15)',
                        borderWidth: 1.8,
                        tension: 0.25,
                        fill: false,
                    },
                    {
                        label: 'Revenue without discount',
                        data: revenueWithoutDisc,
                        borderColor: '#16c79a', // green
                        backgroundColor: 'rgba(22,199,154,0.15)',
                        borderWidth: 1.8,
                        tension: 0.25,
                        fill: false,
                    }
                ]
            },
            options: {
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                return `${ctx.dataset.label}: $${Number(ctx.raw ?? 0).toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Orders Chart: Total + with/without discount (stacked bars + total line)
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Orders with discount',
                        data: ordersWithDisc,
                        backgroundColor: 'rgba(133, 94, 255, 0.70)', // purple
                        borderColor: '#855eff',
                        borderWidth: 1
                    },
                    {
                        label: 'Orders without discount',
                        data: ordersWithoutDisc,
                        backgroundColor: 'rgba(32, 201, 151, 0.70)', // teal
                        borderColor: '#20c997',
                        borderWidth: 1
                    },
                    {
                        type: 'line',
                        label: 'Total orders',
                        data: ordersTotal,
                        borderColor: '#ff9f43', // orange
                        backgroundColor: 'rgba(255, 159, 67, 0.15)',
                        borderWidth: 2,
                        tension: 0.25,
                        fill: false,
                        pointRadius: 3,
                        pointHoverRadius: 4,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                return `${ctx.dataset.label}: ${ctx.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }

});
</script>
@endpush
