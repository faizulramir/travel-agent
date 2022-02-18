@extends('layouts.master')

@section('title') PAYMENT @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') PAYMENT @endslot
        @slot('title') PAYMENT @endslot
    @endcomponent

    @php
        $sub_total = 0.00;
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('submit_payment') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Invoice Summary</h4>
                        <br>
                        {{--
                        <div class="row">
                            <div class="col-md-4">
                                <label for="plan">Invoice No: {{ $invoice_num }}</label>
                                <br>
                                <label for="plan">Total Jemaah: {{ $tot_rec }}</label>
                            </div>
                        </div>
                        --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Invoice No: {{ $invoice_num }}</label>
                            </div>
                            <div class="col-md-4">
                                <label for="plan">Total Jemaah: {{ $tot_rec }}</label>
                            </div>   
                            <div class="col-md-3">
                                <label for="plan">Requester Name: {{ $uploads->ta_name }}</label>                                
                            </div>  
                        </div>



                        <br>
                        <div class="row">
                            <div class="col-md-3">

                                <label for="plan">&raquo;Plan E-CARE</label>
                                <p>
                                    @foreach ($invoice_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>
                                <label for="plan">&raquo;Plan PCR</label>
                                <p>
                                    {{-- @foreach ($invoice_arr as $inv) --}}
                                        {{ $pcr_detail->cnt }} x <b>{{ $pcr_detail->name }}</b> = {{ number_format((float)$pcr_detail->price, 2, '.', ',') }} <br>
                                    {{-- @endforeach --}}
                                </p>
                                <label for="plan">&raquo;Plan TPA</label>
                                <p>
                                    @foreach ($tpa_total_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>

                            </div>
                            <div class="col-md-3">

                                <label for="disc">Total</label>
                                <input class="form-control" type="text" name="tempTotal" value="{{ number_format((float)$tot_inv + (float)$uploads->discount, 2, '.', ',') }}" required {{ $uploads->status == '2.1' ? '' : 'readonly' }}>
                                <br>

                                <label for="disc">Discount</label>
                                <input class="form-control" type="text" name="discount" value="{{ number_format((float)$uploads->discount, 2, '.', ',') }}" required {{ $uploads->status == '2.1' ? '' : 'readonly' }}>
                                <br>
                                
                                <label for="plan">Payment Total</label>
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
                                --}}
                                
                                <input class="form-control" type="text" name="pay_total" value="RM {{ number_format((float)$tot_inv, 2, '.', ',') }}" required readonly="readonly">
                                <br>

                                <div class="col-md-12">
                                    <a href="{{ route('create_invoice', $id) }}" target="_blank" class="btn btn-primary waves-effect waves-light">Download Invoice</a>
                                </div>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-3">

                                <label for="plan">Payment Method</label>
                                <select id="pay_by" name="pay_by" class="form-control select2-search-disable" required>
                                    <option value="">Please Select</option>
                                    <option value="OTHER">Others</option>
                                    <!-- <option value="fpx">FPX - Online Banking (Coming Soon)</option>
                                    <option value="cc">Credit Card / Debit Card (Coming Soon)</option> -->
                                </select>
                                <br>

                                <label for="plan">Upload Payment Receipt</label>
                                @if (auth()->user()->hasAnyRole('akc') || auth()->user()->hasAnyRole('fin'))
                                    <input class="form-control" type="file" name="pay_file">
                                @else 
                                    <input class="form-control" type="file" name="pay_file" required>
                                @endif
                                <br>

                                <input type="hidden" value="{{ $id }}" name="id">
                                @if($uploads->status != '4' && $uploads->status != '5')
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Make Payment</button>
                                @endif

                                <a href="{{ route('excel_list') }}" class="btn btn-primary waves-effect waves-light">Cancel</a>

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
