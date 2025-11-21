@extends('layouts.app')

@section('content')

    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">My orders</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">My orders</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">

        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Town</th>
                        <th scope="col">phone_number</th>
                        <th scope="col">price</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($allOrders)
                        @foreach ($allOrders as $orders)
                            <tr>
                                <td>{{ $orders->name }}</td>
                                <td>{{ $orders->email }}</td>
                                <td>{{ $orders->town }}</td>
                                <td>{{ $orders->phone_number }}</td>
                                <td>{{ $orders->total_price }}</td>
                                <td>{{ $orders->created_at->format('Y-m-d') }}</td>
                                <td>{{ $orders->status }}</td>
                                @if ($orders->status == 'Delivered')
                                    <td><a href="{{ route('orders.delivered') }}" class="btn btn-success">Review</a>
                                    </td>
                                @else
                                    <td>not available yet </td>
                                @endif
                        @endforeach
                    @else
                        <h3 class="alrt alert-success">you have no booking table yet</p>
                    @endif

                </tbody>
            </table>

        </div>

    </div>


@endsection
