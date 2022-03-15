@extends('layouts.master')

@section('title') CREDIT CARD @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') PAYMENT @endslot
        @slot('title') CREDIT CARD @endslot
    @endcomponent

    @php
        $sub_total = 0.00;
    @endphp

    <div class="modal fade bs-example-modal-center" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Loading Data</h5>
                </div>
                <div class="modal-body text-center">
                    <button class="btn btn-primary" id="btnBefore" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <p>{{ Session::get('success') }}</p>
                        </div>
                    @endif
                    @if (Session::has('error'))
                        <div class="alert alert-warning text-center alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <p>{{ Session::get('error') }}</p>
                        </div>
                    @endif 
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                           <div class="panel panel-default credit-card-box">
                              <div class="panel-heading display-table" >
                                 <div class="row display-tr" >
                                    <!-- <h4 class="panel-title display-td">Payment - Card Details</h4> -->
                                    <div class="card-title">Payment - Card Details</div>
                                 </div>
                              </div>
                              <div class="panel-body">
                                 <form
                                    role="form"
                                    action="{{ route('stripePost') }}"
                                    method="post"
                                    class="require-validation"
                                    data-cc-on-file="false"
                                    data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                                    id="payment-form">
                                    @csrf
                                    <br>
                                    <div class='form-row row'>
                                       <div class='col-xs-12 col-md-8 form-group card required'>
                                          <label class='control-label'>Card Number</label>
                                          <input autocomplete='off' class='form-control card-number' placeholder='' maxlength='16' type='text'>
                                       </div>
                                    </div>
                                    <div class='form-row row'>
                                       <div class='col-xs-12 col-md-2 form-group expiration required'>
                                          <label class='control-label'>Expiry Month</label> 
                                          <input class='form-control card-expiry-month' placeholder='MM' maxlength='2' type='text'>
                                       </div>
                                       <div class='col-xs-12 col-md-2 form-group expiration required'>
                                          <label class='control-label'>Expiry Year</label>
                                          <input class='form-control card-expiry-year' placeholder='YYYY' maxlength='4' type='text'>
                                       </div>
                                       <div class='col-xs-12 col-md-2 form-group cvc required'>
                                          <label class='control-label'>CVV</label> 
                                          <input autocomplete='off' class='form-control card-cvc' placeholder='' maxlength='3' type='text'>
                                       </div>
                                    </div>
                                    <br>
                                    <div class='form-row row'>
                                       <div class='col-xs-12 col-md-8 form-group required'>
                                          <label class='control-label'>Name on Card</label>
                                          <input class='form-control' placeholder='' maxlength='40' type='text'>
                                       </div>
                                    </div>
                                    <br>
                                    {{-- <div class='form-row row'>
                                       <div class='col-md-12 error form-group hide'>
                                          <div class='alert-danger alert'>Please correct the errors and try
                                             again.
                                          </div>
                                       </div>
                                    </div> --}}
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input class="form-check-input" type="checkbox" id="agreement">
                                            <label class="form-check-label" style="color:navy;" for="agreement">
                                                &nbsp;&nbsp;<b>All information are correct</b>
                                            </label>
                                        </div>
                                    </div>   
                                    <br>                                 
                                    <div class="row">
                                       <div class="col-xs-12">
                                          <button class="btn btn-primary btn-md btn-block" onclick="doPayment()" type="submit"><b>PAY {{ $pay_total }}</b></button>
                                          <button class="btn btn-primary btn-md btn-block">Cancel</button>
                                       </div>
                                    </div>
                                    <input type="hidden" name="pay_id" id="pay_id" value="{{ $pay_id }}">
                                    <input type="hidden" name="pay_total" id="pay_total" value="{{ $pay_total }}">
                                    <input type="hidden" name="pay_name" id="pay_name" value="cc">
                                 </form>
                              </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">

        function doPayment() {
            //alert("doPayment!");

            //validates here ...
            //validate card number is not empty and number only
            //validate MM in 01 02 03 ... 12 and number only
            //validate YYYY is current year and beyond and number only
            //validate cvc is not empty and number only
            //if validation failed do not submit; this will stop card testing attempt from being submitted to stripe

            $('#pleaseWaitDialog').modal('show');
            setTimeout(function() {
                $('#pleaseWaitDialog').modal('hide');
                $('#pleaseWaitDialog').modal('hide');
                //do something to cancel any active/pending transaction (if any)
            }, 480000); //force to clear loading after 8min
        }

        $(function() {
            var $form = $(".require-validation");
            $('form.require-validation').bind('submit', function(e) {
                var $form = $(".require-validation"),
                    inputSelector = ['input[type=email]', 'input[type=password]',
                        'input[type=text]', 'input[type=file]',
                        'textarea'
                    ].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid = true;
                $errorMessage.addClass('hide');
                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault();
                    }
                });
                console.log($form.data('stripe-publishable-key'));
                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey('pk_test_51KPICVGHIWVASdQSJlWtL4yU0WZVApDzq1EJyKM4PrZSSFXfZTpnuXwTzucCW5DPZAA1MDAPOKipkv5E8sQR37f900eHh5eol2');
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
            });
            function stripeResponseHandler(status, response) {
                //$('#pleaseWaitDialog').modal('hide');

                if (response.error) {
                    console.log("stripeResponseHandler-err: ", response);
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                    alert("Payment Error! " + response.error.message);
                    $('#pleaseWaitDialog').modal('hide');
                    $('#pleaseWaitDialog').modal('hide');
                } else {
                    /* token contains id, last4, and card type */
                    console.log("stripeResponseHandler-succ: ", response);
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.append("pay_id", $('#pay_id').val());
                    $form.append("pay_total", $('#pay_total').val());
                    $form.append("pay_name", $('#pay_name').val());
                    $form.get(0).submit();
                }
            }
        });
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
