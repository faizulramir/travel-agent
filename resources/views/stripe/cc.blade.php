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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                           <div class="panel panel-default credit-card-box">
                              <div class="panel-heading display-table" >
                                 <div class="row display-tr" >
                                    <h3 class="panel-title display-td">Payment Details</h3>
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
                                    <div class='form-row row'>
                                       <div class='col-xs-12 form-group required'>
                                          <label class='control-label'>Name on Card</label> <input
                                             class='form-control' size='4' type='text'>
                                       </div>
                                    </div>
                                    <br>
                                    <div class='form-row row'>
                                       <div class='col-xs-12 form-group card required'>
                                          <label class='control-label'>Card Number</label> <input
                                             autocomplete='off' class='form-control card-number' size='20'
                                             type='text'>
                                       </div>
                                    </div>
                                    <div class='form-row row'>
                                       <div class='col-xs-12 col-md-4 form-group cvc required'>
                                          <label class='control-label'>CVC</label> <input autocomplete='off'
                                             class='form-control card-cvc' placeholder='ex. 311' size='4'
                                             type='text'>
                                       </div>
                                       <div class='col-xs-12 col-md-4 form-group expiration required'>
                                          <label class='control-label'>Expiration Month</label> <input
                                             class='form-control card-expiry-month' placeholder='MM' size='2'
                                             type='text'>
                                       </div>
                                       <div class='col-xs-12 col-md-4 form-group expiration required'>
                                          <label class='control-label'>Expiration Year</label> <input
                                             class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                             type='text'>
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
                                    <div class="row">
                                       <div class="col-xs-12">
                                          <button class="btn btn-primary btn-lg btn-block" type="submit">PAY {{ $pay_total }}</button>
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
                if (response.error) {
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    /* token contains id, last4, and card type */
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
