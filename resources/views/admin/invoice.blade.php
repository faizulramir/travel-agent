@extends('layouts.master')

@section('title') INVOICE @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') INVOICE @endslot
    @endcomponent

    @php
        $sub_total = 0.00;
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{--
                        <h4 class="card-title">Invoice Summary</h4>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="plan">Invoice No: </label>
                                <br>
                                <label for="plan">Total Jemaah: {{ $tot_rec }}</label>
                            </div>
                        </div>
                        <br>
                        --}}

                        <h4 class="card-title">Invoice Summary</h4>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Invoice No:</label>
                            </div>
                            <div class="col-md-4">
                                <label for="plan">Total Jemaah: {{ $tot_rec }}</label>
                            </div>   
                            <div class="col-md-3">
                                <label for="plan">Travel Agent Name: {{ $uploads->ta_name }}</label>                                
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

                                <label for="plan">Plan: E-CARE</label>
                                <p>
                                    @foreach ($invoice_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>
                                <label for="plan">Plan: PCR</label>
                                <p>
                                    {{-- @foreach ($invoice_arr as $inv) --}}
                                    {{ $pcr_detail->cnt }} x <b>{{ $pcr_detail->name }}</b> = {{ number_format((float)$pcr_detail->price, 2, '.', ',') }} <br>
                                    {{-- @endforeach --}}
                                </p>
                                <label for="plan">Plan: TPA</label>
                                <p>
                                    @foreach ($tpa_total_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>

                            </div>
                            <div class="col-md-3">
                                {{--
                                @foreach ($plan_arr as $i => $plan_a)
                                    @foreach ($plans as $x => $plan)
                                        @if ($plan->name == strtolower($i))
                                            @php
                                                $sub_total = $plan_a*$plan->price;
                                            @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                                <label for="plan">Total (RM)</label>
                                <input class="form-control" type="text" name="pay_total" value="RM {{ $pay !== null ? $pay->pay_total : $sub_total }}" required readonly="readonly">
                                --}}


                                <label for="disc">Total</label>
                                <input class="form-control" type="text" name="tempTotal" value="{{ number_format((float)$tot_inv + (float)$uploads->discount, 2, '.', ',') }}" required {{ $uploads->status == '2.1' ? '' : 'readonly' }}>
                                <br>

                                <label for="disc">Discount</label>
                                <input class="form-control" type="text" name="discount" value="{{ number_format((float)$uploads->discount, 2, '.', ',') }}" required {{ $uploads->status == '2.1' ? '' : 'readonly' }}>
                                <br>

                                <label for="plan">Payment Total</label>
                                <input class="form-control" type="text" name="pay_total" value="RM {{ number_format((float)$tot_inv, 2, '.', ',') }}" required readonly="readonly">
                                <br>

                                <label for="plan">Status</label>
                                <input class="form-control" type="text"  name="status" value="{{ $uploads->status == '5' || $uploads->status == '4' ? 'PAID' : 'UNPAID' }}" readonly>
                                <br>

                                @if ($uploads->json_inv && $uploads->json_inv!=null && $uploads->json_inv!='')
                                <div class="col-md-12">
                                    <a href="{{ route('create_invoice', $uploads->id) }}" target="_blank" class="btn btn-primary waves-effect waves-light">Download Invoice</a>
                                </div>
                                @endif

                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-3">

                                <label for="plan">Payment Method</label>
                                <select id="pay_by" name="pay_by" class="form-control select2-search-disable" required disabled>
                                    <option value="">Please Select</option>
                                    <option value="FPX" {{ $pay !== null ? $pay->pay_by == 'FPX' ? 'selected' : '' : '' }}>FPX - Online Banking</option>
                                    <option value="CC" {{ $pay !== null ? $pay->pay_by == 'CC' ? 'selected' : '' : '' }}>Credit Card / Debit Card</option>
                                    <option value="OTHER" {{ $pay !== null ? $pay->pay_by == 'OTHER' ? 'selected' : '' : '' }}>Others</option>
                                </select>
                                <br>

                                <label for="plan">Upload Receipt Payment</label>
                                @if ($pay !== null)
                                    @if ($pay->pay_file == null)
                                        <p>File not found</p>
                                        <a href="#" class="btn btn-primary waves-effect waves-light">
                                            Upload Payment Receipt
                                        </a>
                                        <br>
                                    @else
                                        <p>
                                            <br><br><br>
                                            <a href="{{ route('download_payment', [$uploads->user_id, $uploads->id]) }}" class="btn btn-primary waves-effect waves-light">
                                                Download Receipt
                                            </a>
                                        </p>
                                    @endif
                                @endif
                                <br><br>

                                {{-- <input type="hidden" value="{{ $id }}" name="id"> --}}
                                <div class="row" style="display: {{ $uploads->status == '5' || $uploads->status == '4' ? 'none' : 'block' }}">
                                    <div class="col-md-12">
                                        <a href="{{ route('payment', $uploads->id) }}" class="btn btn-primary waves-effect waves-light">Confirm Payment for Customer</a>
                                        <a href="{{ route('excel_list_admin') }}" class="btn btn-primary waves-effect waves-light">Cancel</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-4">

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
