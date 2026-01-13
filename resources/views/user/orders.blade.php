@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="container py-5" style="margin-top: 82px">

        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-gold">My Coupons</a>
            <a href="{{ route('dashboard.orders') }}" class="btn btn-gold text-dark">My Orders</a>
            <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-gold">Profile</a>
        </div>

        <div class="akg-card p-4 mb-4 user-card">
            <h4 class="text-gold mb-2">My Orders</h4>
            <p class="text-muted mb-0">Track your recent orders and applied coupons.</p>
        </div>

        <div class="akg-card p-4 user-card">
            @if ($orders->isEmpty())
                <p class="text-muted mb-0">You have no orders yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Discount</th>
                                <th>Coupon</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at?->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-secondary">{{ $order->status ?? 'Pending' }}</span></td>
                                    <td class="fw-bold text-gold">${{ number_format($order->total_price ?? 0, 2) }}</td>
                                    <td class="text-success">- ${{ number_format($order->discount_amount ?? 0, 2) }}
                                    </td>
                                    <td>{{ $order->coupon?->code ?? '—' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-gold" data-bs-toggle="modal"
                                            data-bs-target="#orderModal{{ $order->id }}">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>


@foreach ($orders as $order)
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content card-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Order #{{ $order->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ $order->status ?? 'Pending' }}</span>
                        <span class="ms-2 text-muted small">{{ $order->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-dark table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price ?? 0, 2) }}</td>
                                        <td class="text-end">
                                            ${{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small">
                            Coupon: {{ $order->coupon?->code ?? '—' }}<br>
                            Discount: ${{ number_format($order->discount_amount ?? 0, 2) }}
                        </div>
                        <div class="text-end">
                            <div class="text-muted">Subtotal:
                                ${{ number_format($order->total_before_discount ?? ($order->total_price ?? 0), 2) }}
                            </div>
                            <div class="fw-bold text-gold fs-5">Total:
                                ${{ number_format($order->total_price ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
