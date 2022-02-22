@extends('layouts.master')

@section('title') PAYMENT @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') PAYMENT @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('endorse_payment', $uploads->id) }}" method="POST" id="formSubmit" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Invoice Summary FIN</h4>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Travel Agent Name: {{ $uploads->ta_name }}</label>                                
                            </div>  
                            <div class="col-md-4">
                                <label for="plan">Filename: {{ $uploads->file_name }}</label>
                            </div>  
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Invoice No: {{ $invoice_num }}</label>
                            </div>
                            <div class="col-md-4">
                                <label for="plan">Total Jemaah: {{ $tot_rec }}</label>
                            </div>
                        </div>
                        <br><br>

                        <div class="row">
                            <div class="col-md-3">
                                {{--
                                <label for="plan">Plan</label>
                                <p>
                                    @foreach ($plan_arr as $i => $plan_a)
                                        {{ $plan_a }} {{ $i }},
                                    @endforeach
                                </p>
                                --}}

                                <label for="plan">&raquo; Plan E-CARE</label>
                                <p>
                                    @foreach ($invoice_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>
                                <label for="plan">&raquo; Plan PCR</label>
                                <p>
                                    {{-- @foreach ($invoice_arr as $inv) --}}
                                    {{ $pcr_detail->cnt }} x <b>{{ $pcr_detail->name }}</b> = {{ number_format((float)$pcr_detail->price, 2, '.', ',') }} <br>
                                    {{-- @endforeach --}}
                                </p>
                                <label for="plan">&raquo; Plan TPA</label>
                                <p>
                                    @foreach ($tpa_total_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>
                            </div>

                            <div class="col-md-3">

                                <label for="disc">Total</label>
                                <input class="form-control" type="text" name="tempTotal2" id="tempTotal2" value="{{ number_format((float)$tot_inv + (float)$uploads->discount, 2, '.', ',') }}" required {{ $uploads->status == '2.1' ? '' : 'readonly' }}>
                                <input class="form-control" type="hidden" name="tempTotal" id="tempTotal" value="{{ number_format((float)$tot_ecert + (float)$uploads->discount, 2, '.', ',') }}">
                                <br>

                                <label for="disc">ECare Discount</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select style="pointer-events: {{ isset($pay) ? 'none' : '' }}" id="percent_disc" name="percent_disc" class="form-control select2-search-disable" required {{ isset($pay) ? 'readonly' : '' }}>
                                            <option value="0" {{ ($uploads->percent == '0' || $uploads->percent == 0) ? 'selected' : '' }}>0%</option>
                                            <option value="10" {{ ($uploads->percent == '10' || $uploads->percent == 10) ?'selected' : '' }}>10%</option>
                                            <option value="15" {{ ($uploads->percent == '15' || $uploads->percent == 15) ?'selected' : '' }}>15%</option>
                                            <option value="20" {{ ($uploads->percent == '20' || $uploads->percent == 20) ?'selected' : '' }}>20%</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" type="text" name="discount" id="discount" value="{{ number_format((float)$uploads->discount, 2, '.', ',') }}" readonly>
                                    </div>
                                </div>
                                <br>

                                <label for="plan">Payment Total</label>
                                <input class="form-control" type="text" name="pay_total" id="pay_total" value="RM {{ number_format((float)$tot_inv, 2, '.', ',') }}" required readonly="readonly">
                                <br>

                                @if(isset($pay))
                                    <label for="plan">Payment Method</label>
                                    <select id="pay_by" name="pay_by" class="form-control select2-search-disable" required readonly="readonly" disabled>
                                        <option value="">Please Select</option>
                                        <option value="fpx" {{ isset($pay) ? $pay->pay_by == 'fpx' ? 'selected' : '' : '' }}>FPX - Online Banking</option>
                                        <option value="cc" {{ isset($pay) ? $pay->pay_by == 'cc' ? 'selected' : '' : ''}}>Credit Card / Debit Card</option>
                                        <option value="OTHER" {{ isset($pay) ? $pay->pay_by == 'OTHER' ? 'selected' : '' : '' }}>Others</option>
                                    </select>
                                    <br>
                                @endif

                                @if($uploads->json_inv && $uploads->json_inv!=null && $uploads->json_inv!='')
                                <p>
                                    <a href="{{ route('create_invoice', $uploads->id) }}" target="_blank" class="btn btn-primary waves-effect waves-light">
                                        Download Invoice
                                    </a>
                                </p>
                                @endif

                                @if(isset($pay))
                                    <label for="plan">Payment Receipt</label>
                                    @if ($pay->pay_file == null)
                                        <p>File not found</p>
                                        <a href="#" class="btn btn-primary waves-effect waves-light">
                                            Upload Payment Receipt
                                        </a>
                                        <br>
                                    @else
                                        <p>
                                            {{--<a href="{{ route('create_invoice', $uploads->id) }}" target="_blank" class="btn btn-primary waves-effect waves-light">
                                                Download Invoice
                                            </a>--}}
                                            <a href="{{ route('download_payment', [$uploads->user_id, $uploads->id]) }}" class="btn btn-primary waves-effect waves-light">
                                                Download Receipt
                                            </a>
                                            <a href="{{ route('excel_list_finance') }}" class="btn btn-primary waves-effect waves-light">Cancel</a>
                                        </p>
                                    @endif
                                @endif
                                <br>

                            </div>

                            <div class="col-md-1"></div>

                            <input type="hidden" name="checkStatus" id="checkStatus" value="{{ $uploads->status }}">
                            <div class="col-md-4">
                                <div class="col-lg-12" style="display: {{ $uploads->status == '5' || $uploads->status == '2.1' ? 'none' : 'block' }}">
                                    <input class="form-check-input" type="checkbox" id="agreementPay">
                                    <label class="form-check-label" for="agreementPay" style="color:red;">
                                        &nbsp;&nbsp;Payment telah disemak dan amaun adalah betul 
                                    </label>
                                    <br>

                                    <br>
                                    {{-- <input type="hidden" value="{{ $id }}" name="id"> --}}
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Confirm Payment Endorsement</button>
                                    <a href="{{ route('excel_list_finance') }}" class="btn btn-primary waves-effect waves-light">Cancel</a>

                                </div>

                                <div class="col-lg-12" style="display: {{ $uploads->status == '2.1' ? 'block' : 'none' }}">
                                    <input class="form-check-input" type="checkbox" id="agreementInv">
                                    <label class="form-check-label" for="agreementInv" style="color:red;">
                                        &nbsp;&nbsp;Invois telah disemak dan amaun adalah betul 
                                    </label>
                                    <br>

                                    <br>
                                    {{-- <input type="hidden" value="{{ $id }}" name="id"> --}}
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Confirm Invoice Endorsement</button>
                                    @if($uploads->status == '2.1')
                                        <a href="{{ route('invoice_reject', $uploads->id) }}" id="reject_data" class="btn btn-warning waves-effect waves-light">Reject Invoice Endorsement</a>
                                    @endif
                                    <a href="{{ route('excel_list_finance') }}" class="btn btn-primary waves-effect waves-light">Cancel</a>

                                </div>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#formSubmit').submit(function(){ 
            stat = $('#checkStatus').val();
            if (stat !== '5' && stat !== '2.1') {
                if (!$('#agreementPay')[0].checked){
                    alert('Please tick the checkbox given!');
                return false;
                }
            } else if (stat === '2.1') {
                if (!$('#agreementInv')[0].checked){
                    alert('Please tick the checkbox given!');
                return false;
                }
            } 
            
        }); 

        $("#percent_disc").change(function() {
            var percent = $("#percent_disc").val();
            var subtotal = $("#tempTotal").val().replace(",", "");
            var total = percent / 100 * subtotal;

            if (percent != '0') {
                //console.log(total)
                $("#discount").val(total);

                var formatter = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });
                let currency = formatter.format(total).replace("$", "").replace(" ", "");
                let currencySub = formatter.format($("#tempTotal2").val().replace(",", "") - total).replace("$", "").replace(" ", "");
                //console.log("Formatter: " + currency);
                $("#discount").val(currency);
                $("#pay_total").val('RM ' + (currencySub));
            } else {
                $("#discount").val('0.00');
                $("#pay_total").val('RM ' + $("#tempTotal2").val());
            }
        });
        
        $('#reject_data').click(function() {
            alert("Confirm to Reject Invoice Endorsement ?");
        });

    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
