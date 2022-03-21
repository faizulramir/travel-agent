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
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <p>{{ Session::get('success') }}</p>
                        </div>
                    @endif
                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Invoice Summary (ADMIN)</h4>
                        <br>

                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Invoice No: <b>{{ $invoice_num }}</b></label>
                                @if ($uploads->status == '5' || $uploads->status == '4')
                                <span style="color:red;">&nbsp;&nbsp;<b>PAID</b></span>
                                @endif
                            </div>                            
                            <div class="col-md-4">
                                <label for="plan">Travel Agent Name: <b>{{ $uploads->ta_name }}</b></label>                                
                            </div>   
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plan">Total Jemaah: <b>{{ $tot_rec }}</b></label>
                            </div>
                            <div class="col-md-4">
                                <label for="plan">Filename: <b>{{ $uploads->file_name }}</b></label>
                            </div>                                                        
                        </div>
                        <br><br>
                        
                        <div class="row">
                            <div class="col-md-3">
                                {{--
                                <label for="plan">Plan: E-CARE</label>
                                <p>
                                    @foreach ($invoice_arr as $inv)
                                        {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                    @endforeach
                                </p>
                                <label for="plan">Plan: PCR</label>
                                <p>
                                    @foreach ($invoice_arr as $inv)
                                    {{ $pcr_detail->cnt }} x <b>{{ $pcr_detail->name }}</b> = {{ number_format((float)$pcr_detail->price, 2, '.', ',') }} <br>
                                    @endforeach 
                                </p>
                                <label for="plan">Plan: TPA</label>
                                <p>
                                    @if ($tpa_total_arr && count($tpa_total_arr)>0)
                                        @foreach ($tpa_total_arr as $inv)
                                            {{ $inv['COUNT'] }} x <b>{{ $inv['PLAN'] }}</b> = RM {{ number_format((float)$inv['COST'], 2, '.', ',') }} <br>
                                        @endforeach
                                    @else
                                        0 x <b>TPA</b> <br>
                                    @endif                                    
                                </p>
                                --}}

                                <table width="100%" class="table table-striped">
                                    @foreach ($invoice_arr as $inv)
                                        <tr>
                                            <td>{{ $inv['COUNT'] }}x</td>
                                            <td><b>{{ $inv['PLAN'] }}</b> {{ isset($inv['ADDT']) ? ($inv['ADDT']>0? '  (+'.$inv['ADDT'].')' : '') : '' }}</td>
                                            <td>= RM {{ number_format((float)$inv['COST'], 2, '.', ',') }}</td>
                                        </tr>
                                    @endforeach

                                    @if ($pcr_detail && $pcr_detail->cnt>0)
                                        <tr>
                                            <td>{{ $pcr_detail->cnt }}x</td>
                                            <td><b>{{ $pcr_detail->name }}</b></td>
                                            <td>= RM {{ number_format((float)$pcr_detail->price, 2, '.', ',') }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>0x</td>
                                            <td><b>PCR</b></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    @endif

                                    @if ($tpa_total_arr && count($tpa_total_arr)>0)
                                        @foreach ($tpa_total_arr as $inv)
                                        <tr>
                                            <td>{{ $inv['COUNT'] }}x</td>
                                            <td><b>{{ $inv['PLAN'] }}</b></td>
                                            <td>= RM {{ number_format((float)$inv['COST'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>0x</td>
                                            <td><b>TPA</b></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    @endif                                    
                                </table>                                

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

                                <label for="disc">ECare Discount</label>
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
                                    <a href="#" id="edit_invoice_name" class="btn btn-primary waves-effect waves-light">Edit Invoice Name</a>
                                    @if ($uploads->status == '3')
                                        <a href="{{ route('cancel_invoice', $uploads->id) }}" id="cancel_invoice" class="btn btn-warning waves-effect waves-light">Cancel Invoice</a>
                                    @endif
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
                                    @if ($pay->pay_file == null && $pay->pay_by == 'OTHER')
                                        <p>File not found</p>
                                        <a href="#" class="btn btn-primary waves-effect waves-light">
                                            Upload Payment Receipt
                                        </a>
                                        <br>
                                    @else
                                        @if ($pay->stripe_link && $pay->pay_by != 'OTHER')
                                            <br>
                                            <a target="_blank" href="{{ $pay->stripe_link }}" class="btn btn-primary waves-effect waves-light">
                                                Download Receipt
                                            </a>
                                        @elseif($pay->pay_by == 'OTHER')
                                            <br>
                                            <a href="{{ route('download_payment', [$uploads->user_id, $uploads->id]) }}" class="btn btn-primary waves-effect waves-light">
                                                Download Receipt
                                            </a>
                                        @else
                                            <p>Stripe receipt not found</p>
                                        @endif
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

    <div class="modal fade bs-example-modal-center" id="editInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Edit Invoice Name</h5>
                </div>
                <div class="modal-body">
                    <div class="row ">
                        <div class="col-md-12">
                            <form action="{{ route('edit_invoice_name') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="editInvoice">Invoice Name</label>
                                        <input type="hidden" name="user_id_edit" value="{{ $uploads->user_id }}">
                                        <input type="hidden" name="uploads_id_edit" value="{{ $uploads->id }}">
                                        <input type="text" name="invoice_name" class="form-control" id="invoice_name" placeholder="Please Input Invoice Name" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#edit_invoice_name').click(function() {
            $('#editInvoiceModal').modal('show');
        })
        
        
        $('#cancel_invoice').click(function() {
            //alert("Confirm to Cancel this Invoice ?");
            if (confirm("Confirm to Cancel this Invoice ?") == true) {
                return true;
            }
            return false;
        });

    </script>
    
@endsection
