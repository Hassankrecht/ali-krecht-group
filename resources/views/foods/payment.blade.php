@extends('layouts.app')

@section('content')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Pay with PayPal</h1>
        </div>
    </div>

    <div class="container mb-5">
        <div class="bg-dark text-white p-5 rounded">
            <h2 class="mb-4">Order #{{ $order->id }} - Total: ${{ number_format($order->total_price, 2) }}</h2>

            <!-- PayPal JS SDK -->
             <script src="https://www.paypal.com/sdk/js?client-id=AQGPBsgMDIpMNJwCS9KGvILCG9T-l6BPFHeFPdRuYczyCee2NiTDn0dKL5XRHTjYuU0Fbva1NWCjUeDE&currency=USD"></script>
            <!-- PayPal Button -->
            <div id="paypal-button-container"></div>

            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '{{ $order->total_price }}' // السعر من Laravel
                                }
                            }]
                        });
                    },
                   onApprove: (data, actions) => {
                            return actions.order.capture().then(function(orderData) {
                          
                             window.location.href = "{{ route('home') }}";
                            });
                        }
                        }).render('#paypal-button-container');
               
            </script>
        </div>
    </div>
@endsection
