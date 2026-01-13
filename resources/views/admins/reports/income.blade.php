@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 text-gold"><i class="bi bi-cash-coin me-2"></i>Income Dashboard</h5>
            <p class="text-muted small mb-0">Revenue & orders overview</p>
        </div>
    </div>

    <form class="row g-2 mb-3 align-items-end" method="GET" action="{{ route('admin.income.index') }}">
        <div class="col-md-3">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}" {{ $status===$st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-gold w-100">Filter</button>
        </div>
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                @php
                    $ranges = [
                        'today' => 'Today',
                        'last_7' => 'Last 7 days',
                        'last_30' => 'Last 30 days',
                        'last_90' => 'Last 3 months',
                        'last_180' => 'Last 6 months',
                        'last_365' => 'Last year',
                    ];
                @endphp
                @foreach($ranges as $key => $label)
                    <a href="{{ route('admin.income.index', ['range' => $key]) }}"
                       class="btn btn-outline-dark btn-sm {{ $range === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </form>
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
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Total discounts</div>
                <h4 class="mb-0 text-gold">${{ number_format($discounts,2) }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Refunds</div>
                <h4 class="mb-0 text-gold">${{ number_format($refunds,2) }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Net revenue with discount</div>
                <h4 class="mb-0 text-gold">${{ number_format($revenueWithCoupon,2) }}</h4>
                <small class="text-muted">Orders: {{ $ordersWithCoupon }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Net revenue without discount</div>
                <h4 class="mb-0 text-gold">${{ number_format($revenueWithoutCoupon,2) }}</h4>
                <small class="text-muted">Orders: {{ $ordersWithoutCoupon }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Orders (count)</div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0 text-gold">{{ $summary->orders_count ?? 0 }}</h4>
                    @if(!is_null($growthOrders))
                        <span class="badge {{ $growthOrders >=0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growthOrders >=0 ? '↑' : '↓' }} {{ number_format(abs($growthOrders),1) }}%
                        </span>
                    @endif
                </div>
                <small class="text-muted">All orders in range</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Subtotal (before discount)</div>
                <h4 class="mb-0 text-gold">${{ number_format($subtotalAll,2) }}</h4>
                <small class="text-muted">Sum of total_before_discount</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4 col-12">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">AOV (Avg order value)</div>
                <h5 class="mb-0 text-gold">${{ number_format($avgOrderValue,2) }}</h5>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">ADR (Avg daily revenue)</div>
                <h5 class="mb-0 text-gold">${{ number_format($avgDailyRevenue,2) }}</h5>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">Avg daily orders</div>
                <h5 class="mb-0 text-gold">{{ number_format($avgDailyOrders,2) }}</h5>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0">Revenue by day</h6>
                    <small class="text-muted">Line chart</small>
                </div>
                <canvas id="incomeChart" height="140"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0">Orders count</h6>
                    <small class="text-muted">Bar chart</small>
                </div>
                <canvas id="ordersChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <h6 class="text-gold mb-3">By status</h6>
                <ul class="list-group list-group-flush">
                    @forelse($byStatus as $row)
                        <li class="list-group-item bg-dark text-light d-flex justify-content-between">
                            <span>{{ $row->status }}</span>
                            <span>${{ number_format($row->revenue,2) }} ({{ $row->orders }})</span>
                        </li>
                    @empty
                        <li class="list-group-item bg-dark text-light">No data</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <h6 class="text-gold mb-3">Recent orders</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Discount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>{{ $r->status }}</td>
                                    <td>${{ number_format($r->total_price,2) }}</td>
                                    <td>${{ number_format($r->discount_amount,2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-dark p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">All orders in range</h6>
            <small class="text-muted">Paginated</small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-dark align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Discount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->status }}</td>
                            <td>${{ number_format($order->total_price,2) }}</td>
                            <td>${{ number_format($order->discount_amount,2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No data</td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const labels = {!! json_encode($chartLabels) !!};
    const revenueData = {!! json_encode($chartRevenue) !!};
    const ordersData = {!! json_encode($chartOrdersSeries) !!};
    const ordersWithCoupon = {!! json_encode($chartOrdersWithCoupon) !!};
    const ordersWithoutCoupon = {!! json_encode($chartOrdersWithoutCoupon) !!};
    const incomeCtx = document.getElementById('incomeChart');
    if (incomeCtx) {
        const ctx = incomeCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, incomeCtx.height);
        gradient.addColorStop(0, 'rgba(199,149,75,0.35)');
        gradient.addColorStop(1, 'rgba(199,149,75,0.02)');

        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#c7954b',
                    backgroundColor: gradient,
                    tension: 0.35,
                    fill: true,
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const revenue = ctx.raw ?? 0;
                                const date = labels[ctx.dataIndex] ?? '';
                                const orders = ordersData[ctx.dataIndex] ?? 0;
                                return `Revenue: $${revenue.toFixed(2)} | Orders: ${orders} | ${date}`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        const maxOrders = Math.max(...ordersData, 1);
        const baseColor = '#c7954b';
        const barColors = ordersData.map(v => {
            const intensity = Math.max(0.2, v / maxOrders);
            return `rgba(199,149,75,${intensity})`;
        });

        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders (all)',
                    data: ordersData,
                    backgroundColor: barColors,
                    borderColor: '#c7954b',
                    borderWidth: 1
                },{
                    label: 'Orders with discount',
                    data: ordersWithCoupon,
                    backgroundColor: 'rgba(220,53,69,0.5)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                },{
                    label: 'Orders without discount',
                    data: ordersWithoutCoupon,
                    backgroundColor: 'rgba(13,110,253,0.5)',
                    borderColor: '#0d6efd',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const value = ctx.raw ?? 0;
                                const date = labels[ctx.dataIndex] ?? '';
                                return `${ctx.dataset.label}: ${value} (${date})`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

});
</script>
@endpush
