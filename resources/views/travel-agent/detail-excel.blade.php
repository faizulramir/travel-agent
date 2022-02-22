@extends('layouts.master')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') TRAVEL AGENT @endslot
        @slot('title') EXCEL DETAIL @endslot
    @endcomponent

    <div class="modal fade bs-example-modal-center" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Center modal</h5>
                    <button type="button" id="btnClose" onclick="deleteAll({{$id}})" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <button class="btn btn-primary" id="btnBefore" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>

                    <a id="btnAfter" href="{{ route('download_all_cert', $id) }}" class="btn btn-primary">
                        Download
                    </a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if (Session::has('success'))
                            <div class="alert alert-success text-center">
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif
                        @if (Session::has('error'))
                            <div class="alert alert-warning text-center">
                                <p>{{ Session::get('error') }}</p>
                            </div>
                        @endif                        
                        <div class="col-md-3" style="text-align: left;">
                            <a href="{{ route('excel_list') }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-20" title="Back"></i>
                            </a>
                        </div>
                        <div class="col-md-3 text-right">
                            <h6>Filename: {{ $uploads->file_name? strtoupper($uploads->file_name) : $uploads->file_name }}</h6>
                            <h6>Supporting Documents: {{ $uploads->supp_doc ? $uploads->supp_doc == null ||  $uploads->supp_doc == '' ? 'Not Uploaded' : 'UPLOADED' :  'Not Uploaded' }}</h6>
                            <h6>Payment: {{ $payment ? 'PAID' : '-' }}</h6>
                        </div>   
                        <div class="col-md-6" style="text-align: right;">
                            <br>
                            @if ($uploads->status != '99')
                            <a style="display: {{ $uploads->status !== '0' ? 'inline' : 'none' }};" href="#" class="btn btn-primary w-md" onclick="openDetail({{$uploads->id}},'{{$uploads->supp_doc}}')">Supporting Documents</a>
                            @endif
                            @if ($uploads->status === '5')
                            <div class="col-md-6" style="text-align: right; display: {{ count($check) != 0 ? 'block' : 'none' }}">
                                <button class="btn btn-primary w-md" id="download_all_cert" onclick="downloadAll({{$id}})" title="Download all ECert">Download All ECert</button>
                            </div>
                            @endif
                        </div>                        
                    </div>
                    <br>

                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Name</th>
                                    <th data-priority="1">Passport No</th>
                                    <th data-priority="1">IC No</th>
                                    <th data-priority="1">DEP Date (DMY)</th>
                                    <th data-priority="5">RTN Date (DMY)</th>                                    
                                    <th data-priority="1">Plan</th>
                                    <th data-priority="3">PCR</th>
                                    <th data-priority="3">TPA</th>
                                    @if ($uploads->status === '5')
                                        <th data-priority="1">ECert</th>
                                    @endif                                    
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $i => $order)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->passport_no  }}</td>
                                        <td>{{ $order->ic_no }}</td>
                                        <td>{{ $order->dep_date ? date('d-m-Y', strtotime($order->dep_date)) : ''}}</td>
                                        <td>{{ $order->return_date ? date('d-m-Y', strtotime($order->return_date)) : '' }}</td>                                        
                                        <td>{{ $order->plan_type }}</td>
                                        <td>{{ $order->pcr }}</td>
                                        <td>{{ $order->tpa }}</td>
                                        @if ($uploads->status === '5')
                                            <td>
                                                @if ($order->plan_type != 'NO')
                                                    {{ $order->ecert }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if (!$payment && $order->upload->status != '99')
                                                @if ($order->status == '1')
                                                    <a href="{{ route('update_detail_ta', [$order->id, '0'])}}" onclick="return confirm('Confirm to CANCEL Traveller?');" class="waves-effect" style="color: red;">
                                                        <i class="bx bx-trash-alt font-size-24" title="Cancel Traveller"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('update_detail_ta', [$order->id, '1'])}}" onclick="return confirm('Confirm to ENABLE Traveller?');" class="waves-effect" style="color: green;">
                                                        <i class="bx bx-paper-plane font-size-24" title="Enable Traveller"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($order->status == '1' && $payment && $order->upload->status == '5')
                                                {{-- <a href="{{ route('create_invoice_ind', $order->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a> --}}
                                                @if ($order->plan_type != 'NO')
                                                    <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                        <i class="bx bx-food-menu font-size-24" title="Print ECert"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center" id="showSuppDoc" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Supporting Documents</h5>
                    <button type="button" id="btnClose" onclick="closeDetail()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-left">
                    <div class="row text-left">
                        <div class="col-md-12">
                            <input type="hidden" id="suppId" name="suppId">
                            <input type="hidden" id="idDownload" name="idDownload">
                            <input type="hidden" id="suppdocs" name="suppdocs">

                            <table border="0" width="100%" id="tableUploadDownload">
                                <tr>
                                    <td width="30%">Document Passport</td>
                                    <td width="20%">
                                        <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Upload</button>
                                    </td>    
                                    <td width="45%" id="passportdownload"></td> 
                                </tr>  
                                <tr>
                                    <td>Document E-Ticket</td>
                                    <td>
                                        <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">Upload</button>
                                    </td>    
                                    <td id="eticketdownload"></td> 
                                </tr>       
                                <tr>
                                    <td>Document E-Visa</td>
                                    <td>
                                        <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Upload</button>
                                    </td>    
                                    <td id="visadownload"></td> 
                                </tr>  
                                <tr>
                                    <td>Payment Receipt</td>
                                    <td>
                                        {{-- <input type="file" name="pay_file_name" id="payreceipt_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('payreceipt')" type="submit" id="payreceipt">Upload</button> --}}
                                    </td>    
                                    <td id="payreceiptdownload"></td> 
                                </tr>                                      
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>                                                                  
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.min.js" integrity="sha512-BMIFH0QGwPdinbGu7AraCzG9T4hKEkcsbbr+Uqv8IY3G5+JTzs7ycfGbz7Xh85ONQsnHYrxZSXgS1Pdo9r7B6w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.6/xls.min.js" integrity="sha512-Nqu6bagCq6Jp2ZhezdTFaomiZBZYVhzafGww9teXy1xsvhfpw1ZW3FlVqMazRfLKPVWucbeBXNY5MgO925fpoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        

        function downloadAll (id) {
            $('#btnAfter').hide();
            $('#btnClose').hide();
            $('#pleaseWaitDialog').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#pleaseWaitDialog').modal('show');
            $('#modalTitle').text('Generating/Merging all ECert ... Please Wait ');
            
            $.ajax({
                url: '/ecert_all/' + id,
                type: 'GET',
                timeout: 500000, // sets timeout to 500 seconds
                success: function (data) {
                    $('#modalTitle').text('Completed');
                    $('#btnBefore').hide();
                    $('#btnAfter').show();
                    $('#btnClose').show();
                    // $('#pleaseWaitDialog').modal('hide');
                }
            }); 
        }

        function deleteAll (id) {
            $.ajax({
                url: '/delete_all_cert/' + id,
                type: 'GET',
                success: function (data) {
                    // $('#pleaseWaitDialog').modal('hide');
                }
            }); 
        }


        function closeDetail() {
            //alert("close");
            location.reload();
        }        

        function openDetail (id, docs) {
            $("#suppId").val(id);
            $("#suppdocs").val(docs);
            if (docs) {
                $.ajax({
                    url: '/supp_doc_check_ta/' + id + '/' + docs,
                    type: 'GET',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        var objp = data.Data.find(o => o['passport']);
                        var obje = data.Data.find(o => o['eticket']);
                        var objv = data.Data.find(o => o['visa']);
                        var objr = data.Data.find(o => o['payreceipt']);
                        //console.log(objp, obje, objv);

                        if (docs.includes('P') && objp!=null && objp!=undefined) {
                            $('#passportdownload').html('<a target="_blank" href="/supp_doc_download_ta/' + id + '/passport" type="submit">'+ objp.passport +'</a>');
                        } else {
                            $('#passportdownload').html('');
                        }

                        if (docs.includes('T') && obje!=null && obje!=undefined) {
                            $('#eticketdownload').html('<a target="_blank" href="/supp_doc_download_ta/' + id + '/eticket" type="submit">'+ obje.eticket +'</a>');
                        } else {
                            $('#eticketdownload').html('');
                        }

                        if (docs.includes('V') && objv!=null && objv!=undefined) {
                            $('#visadownload').html('<a target="_blank" href="/supp_doc_download_ta/' + id + '/visa" type="submit">'+ objv.visa +'</a>');
                        } else {
                            $('#visadownload').html('');
                        }

                        if (docs.includes('R') && objr!=null && objr!=undefined) {
                            $('#payreceiptdownload').html('<a target="_blank" href="/supp_doc_download_ta/' + id + '/payreceipt" type="submit">'+ objr.payreceipt +'</a>');
                        } else {
                            $('#payreceiptdownload').html('');
                        }

                        $('#showSuppDoc').modal('show');
                    }
                });
            } else {
                $('#passportdownload').html('');
                $('#eticketdownload').html('');
                $('#visadownload').html('');
                $('#payreceiptdownload').html('');
                $('#showSuppDoc').modal('show');
            }
            
        }     

        //supporting documents ....
        function chooseSupDoc (type) {
            if (type == 'eticket') {
                $("#eticket_file").trigger("click");
            } else if (type == 'visa') {
                $("#visa_file").trigger("click");
            } else if (type == 'passport') {
                $("#passport_file").trigger("click");
            } 
        }

        $("#eticket_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#eticket_file")[0].files[0]);
            form_data.append("type", 'eticket');
            form_data.append("id", $("#suppId").val());
            //console.log("Form", form_data);
            $.ajax({
                url: '/supp_doc_post_ta',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert("E-Ticket Docs - " + data.Data)
                    //location.reload()
                }
            });
        });

        $("#visa_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#visa_file")[0].files[0]);
            form_data.append("type", 'visa');
            form_data.append("id", $("#suppId").val());
            $.ajax({
                url: '/supp_doc_post_ta',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert("E-Visa Docs - " + data.Data)
                    //location.reload()
                }
            });
        });

        $("#passport_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#passport_file")[0].files[0]);
            form_data.append("type", 'passport');
            form_data.append("id", $("#suppId").val());
            //console.log("passport-form", form_data);
            $.ajax({
                url: '/supp_doc_post_ta',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert("Passport Docs - " + data.Data)
                    //location.reload()
                }
            });
        });


       

    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <!-- Responsive Table js -->
    <script src="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.js') }}"></script>

    <!-- Init js -->
    <script src="{{ URL::asset('/assets/js/pages/table-responsive.init.js') }}"></script>
@endsection
