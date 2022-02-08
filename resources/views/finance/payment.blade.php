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
                    <form action="{{ route('endorse_payment', $uploads->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Invoice Summary</h4>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Invoice No: {{ $invoice_num }}</label>
                                <br>
                                <label for="plan">Total Record: {{ $tot_rec }}</label>
                            </div>
                        </div>
                        <br>
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
                                <label for="plan">Payment Total</label>
                                <input class="form-control" type="text" name="pay_total" value="RM {{ number_format((float)$tot_inv, 2, '.', ',') }}" required readonly="readonly">
                                <br>

                                @if(isset($pay))
                                <label for="plan">Payment Method</label>
                                <select id="pay_by" name="pay_by" class="form-control select2-search-disable" required readonly="readonly" disabled>
                                    <option value="">Please Select</option>
                                    <option value="fpx" {{ isset($pay) ? $pay->pay_by == 'fpx' ? 'selected' : '' : '' }}>FPX - Online Banking</option>
                                    <option value="cc" {{ isset($pay) ? $pay->pay_by == 'cc' ? 'selected' : '' : ''}}>Credit Card / Debit Card</option>
                                    <option value="other" {{ isset($pay) ? $pay->pay_by == 'other' ? 'selected' : '' : '' }}>Others</option>
                                </select>
                                <br>
                                @endif

                                <p>
                                    <a href="{{ route('create_invoice', $uploads->id) }}" target="_blank" class="btn btn-primary waves-effect waves-light">
                                        Download Invoice
                                    </a>
                                </p>

                                @if(isset($pay))
                                    <label for="plan">Payment Receipt</label>
                                    @if ($pay->pay_file == null)
                                        <p>File not found</p>
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

                            <div class="col-md-4">
                                <div class="col-lg-12" style="display: {{ $uploads->status == '5' || $uploads->status == '2.1' ? 'none' : 'block' }}">
                                    <input class="form-check-input" type="checkbox" id="agreement">
                                    <label class="form-check-label" for="agreement" style="color:red;">
                                        &nbsp;&nbsp;Payment telah disemak dan amaun adalah betul 
                                    </label>
                                    <br>

                                    <br>
                                    {{-- <input type="hidden" value="{{ $id }}" name="id"> --}}
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Confirm Payment Endorsement</button>
                                    <a href="#" class="btn btn-primary waves-effect waves-light">Cancel</a>

                                </div>

                                <div class="col-lg-12" style="display: {{ $uploads->status == '2.1' ? 'block' : 'none' }}">
                                    <input class="form-check-input" type="checkbox" id="agreement">
                                    <label class="form-check-label" for="agreement" style="color:red;">
                                        &nbsp;&nbsp;Invois telah disemak dan amaun adalah betul 
                                    </label>
                                    <br>

                                    <br>
                                    {{-- <input type="hidden" value="{{ $id }}" name="id"> --}}
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Confirm Invoice Endorsement</button>
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
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
