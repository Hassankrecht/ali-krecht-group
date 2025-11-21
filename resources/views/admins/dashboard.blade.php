{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-dark p-3">
            <div class="text-muted small">Visitors Today</div>
            <div class="fs-3 fw-bold">{{ $visitsToday }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dark p-3">
            <div class="text-muted small">Orders</div>
            <div class="fs-3 fw-bold">{{ $ordersCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dark p-3">
            <div class="text-muted small">Projects</div>
            <div class="fs-3 fw-bold">{{ $projectsCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dark p-3">
            <div class="text-muted small">Products</div>
            <div class="fs-3 fw-bold">{{ $productsCount }}</div>
        </div>
    </div>
</div>

<div class="card card-dark p-4">
    <h5 class="mb-3">Traffic Overview</h5>
    <canvas id="trafficChart" height="120"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trafficChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{
            label: 'Visits',
            data: [5,9,7,14,11,9,13], // لاحقاً نجيبها من الكنترولر
            borderWidth: 2,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
