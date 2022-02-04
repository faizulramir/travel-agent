@extends('layouts.master')

@section('title') FPX @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') PAYMENT @endslot
        @slot('title') FPX @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="payment-form" action="{{ route('stripePost') }}">
                                @csrf
                                <div class="form-row">
                                  <div>
                                    <label for="fpx-bank-element" method="post">
                                      FPX Bank
                                    </label>
                                    <div id="fpx-bank-element">
                                      <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                  </div>
                                </div>
                                <br>
                                <button id="fpx-button" class="btn btn-primary btn-lg btn-block" data-secret="{{ $clientSecret }}">
                                  PAY {{$pay_total}}
                                </button>
                              
                                <!-- Used to display form errors. -->
                                <div id="error-message" role="alert"></div>
                                <input type="hidden" name="pay_id" id="pay_id" value="{{ $pay_id }}">
                                <input type="hidden" name="pay_total" id="pay_total" value="{{ $pay_total }}">
                                <input type="hidden" name="pay_name" id="pay_name" value="fpx">
                              </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $(function() {
            var stripe = Stripe('pk_test_51KPICVGHIWVASdQSJlWtL4yU0WZVApDzq1EJyKM4PrZSSFXfZTpnuXwTzucCW5DPZAA1MDAPOKipkv5E8sQR37f900eHh5eol2');
            var elements = stripe.elements();

            var style = {
                base: {
                    // Add your base input styles here. For example:
                    padding: '10px 12px',
                    color: '#32325d',
                    fontSize: '16px',
                },
            };

            // Create an instance of the fpxBank Element.
            var fpxBank = elements.create(
                'fpxBank',
                {
                    style: style,
                    accountHolderType: 'individual',
                }
            );

            // Add an instance of the fpxBank Element into the container with id `fpx-bank-element`.
            fpxBank.mount('#fpx-bank-element');

            var form = document.getElementById('payment-form');
            // form.append("pay_id", $('#pay_id').val());
            // form.append("pay_total", $('#pay_total').val());
            // form.append("pay_name", $('#pay_name').val());
            form.addEventListener('submit', function(event) {
            event.preventDefault();
            var fpxButton = document.getElementById('fpx-button');
            // console.log(fpxButton.dataset.secret)
            var clientSecret = fpxButton.dataset.secret;
                stripe.confirmFpxPayment(clientSecret, {
                    payment_method: {
                    fpx: fpxBank,
                    },
                    // Return URL where the customer should be redirected after the authorization
                    return_url: `${window.location.href}`,
                }).then((result) => {
                    if (result.error) {
                    // Inform the customer that there was an error.
                    var errorElement = document.getElementById('error-message');
                    errorElement.textContent = result.error.message;
                    }
                });
            });
        });
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
