@extends('layouts.master')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') EXCEL DETAIL @endslot
    @endcomponent

    <div class="modal fade bs-example-modal-center" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Center modal</h5>
                    <button type="button" id="btnClose" onclick="deleteAll({{$uploads->id}})" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <button class="btn btn-primary" id="btnBefore" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>

                    <a id="btnAfter" href="{{ route('download_all_cert', $uploads->id) }}" class="btn btn-primary">
                        Download
                    </a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade bs-example-modal-center" id="editTaDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Edit Travel Agent Name</h5>
                </div>
                <div class="modal-body text-center">
                    <form action="{{ route('post_edit_ta_name') }}" id="form_edit_ta" method="POST">
                        @csrf
                        <input type="text" class="form-control" id="ta_name" name="ta_name">
                        <input type="hidden" class="form-control" id="ta_id" name="ta_id">
                        <br>
                        <button class="btn btn-primary" name="submit" type="submit" id="edit_ta_submit">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center" id="editEcertNumberDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Edit Ecert Number</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('post_edit_cert_no') }}" id="form_edit_cert_no" method="POST">
                        @csrf
                        <div>
                            <label for="cert_no">First Ecert Number</label>
                            <div class="input-group mb-3">
                                <button class="btn btn-primary" type="button" style="pointer-events: none;">A{{ \Carbon\Carbon::now()->year; }}</button>
                                <input type="text" class="form-control" id="cert_no" name="cert_no" value="{{ $ecert_no }}">
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="id" name="id">
                        <br>
                        <div class="text-center">
                            <button class="btn btn-primary" name="submit" type="submit" id="edit_cert_no_submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center">
                            <p>{{ Session::get('success') }}</p>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6" style="text-align: left;">
                            <a href="{{ route('excel_list_admin') }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4 text-right">
                            <h4 class="card-title">Travel Agent Name: {{ $uploads->ta_name? strtoupper($uploads->ta_name) : $uploads->ta_name }}</h4>
                            <h4 class="card-title">Filename: {{ $uploads->file_name? strtoupper($uploads->file_name) : $uploads->file_name }}</h4>
                            <h4 class="card-title">Supporting Documents: {{ $uploads->supp_doc ? $uploads->supp_doc === '1' ? 'UPLOADED' : 'Not Uploaded' :  'Not Uploaded' }}</h4>
                            <h4 class="card-title">Payment: {{ $payment ? 'PAID' : '-' }}</h4>
                        </div>
                        
                        <div class="col-md-8" style="text-align: right;">
                            {{--
                            @if ($uploads->status === '5')
                                <a style="display: {{ $uploads->status !== '0' ? 'inline' : 'none' }};" href="#" class="btn btn-primary w-md" id="edit_cert_no" onclick="editEcertNumber({{$uploads->id}})">Edit Ecert Number</a>
                            @endif
                            --}}

                            <a style="display: {{ $uploads->status !== '0' ? 'inline' : 'none' }};" href="#" class="btn btn-primary w-md" id="edit_ta_name" onclick="editTaName({{$uploads->id}}, '{{$uploads->ta_name}}')">Edit Travel Agent Name</a>
                            <a style="display: {{ $uploads->status !== '0' ? 'inline' : 'none' }};" href="#" class="btn btn-primary w-md" onclick="openDetail({{$uploads->id}})">Supporting Documents</a>

                            @if ($uploads->status === '5')
                                <a style="display: {{ $uploads->supp_doc ? $uploads->supp_doc === '1' ? 'inline' : 'none' :  'none' }};" href="{{ route('download_supp_doc',  [$uploads->user_id, $uploads->id]) }}" class="btn btn-primary w-md" id="download_cert">Download Supporting Docs</a>
                                <button class="btn btn-primary w-md" id="download_all_cert" onclick="downloadAll({{$uploads->id}})" title="Download all ECert">Download All ECert</button>
                            @endif
                        </div>
                        
                    </div>
                    <br>
                    <div>
                        <table id="dtTable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Name</th>
                                    <th data-priority="3">Passport No</th>
                                    <th data-priority="1">IC No</th>
                                    <th data-priority="1">DEP Date (DMY)</th>
                                    <th data-priority="5">RTN Date (DMY)</th>
                                    <th data-priority="1">Plan</th>
                                    <th data-priority="3">PCR</th>
                                    <th data-priority="3">TPA</th>
                                    @if ($uploads->status === '5')
                                        <th data-priority="1">ECert</th>
                                    @endif                                          
                                    <th data-priority="3">Status</th>
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
                                        <td>{{ $order->plan_type }} {{ $additional_arr[$i]['DIFDAY'] }}</td>
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
                                            @if ($order->status == '0')
                                                <a href="#" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-dislike font-size-24" title="Traveller: Cancelled"></i>
                                                </a>
                                            @else
                                                <a href="#" class="waves-effect" style="color: blue;">
                                                    <i class="bx bx-like font-size-24" title="Traveller: OK"></i>
                                                </a>
                                            @endif
                                            @if ($payment && $order->upload->status == '5')
                                                {{--<a href="{{ route('create_invoice_ind', $order->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>--}}
                                                @if ($order->plan_type != 'NO')
                                                    <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                        <i class="bx bx-food-menu font-size-24" title="Print ECert"></i>
                                                    </a>
                                                @endif                                                
                                            @endif

                                            @if ($order->upload->status != '0')
                                            <a href="{{ route('jemaah_show', $order->id) }}" class="waves-effect" style="color: black;">
                                                <i class="bx bx-edit-alt font-size-24" title="Edit Record"></i>
                                            </a>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Supporting Documents AKC ADM DETAIL</h5>
                    <button type="button" id="btnClose" onclick="closeDetail()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-left">
                    <div class="row text-left">
                        <div class="col-md-12">
                            <input type="hidden" id="suppId" name="suppId">
                            <input type="hidden" id="idDownload" name="idDownload">
                            {{--
                            <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                            <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                            <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                            <input type="file" name="pay_file_name" id="payreceipt_file" style="display: none;">
                            <input type="hidden" id="suppId" name="suppId">
                            <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">E-Ticket</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Visa</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Passport</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('payreceipt')" type="submit" id="payreceipt">Pay Receipt</button>
                            --}}

                            <table border="0" width="100%">
                                <tr>
                                    <td width="50%">Document Passport</td>
                                    <td width="25%">
                                        <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Upload</button>
                                    </td>    
                                    <td width="25%">
                                        @if($uploads->supp_doc)
                                            @if(str_contains("P", $uploads->supp_doc))
                                                <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'passport' ]) }}" class="btn btn-success" id="passportDown">Download</a>
                                            @endif
                                        @endif
                                    </td> 
                                </tr>  
                                <tr>
                                    <td>Document E-Ticket</td>
                                    <td>
                                        <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">Upload</button>
                                    </td>    
                                    <td>
                                        @if($uploads->supp_doc)
                                            @if(str_contains("T", $uploads->supp_doc))
                                                <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'eticket' ]) }}" class="btn btn-success" id="eticketDown">Download</a>
                                            @endif
                                        @endif
                                    </td> 
                                </tr>       
                                <tr>
                                    <td>Document E-Visa</td>
                                    <td>
                                        <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Upload</button>
                                    </td>    
                                    <td>
                                        @if($uploads->supp_doc)
                                            @if(str_contains("V", $uploads->supp_doc))
                                                <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'visa' ]) }}" class="btn btn-success" id="visaDown">Download</a>
                                            @endif
                                        @endif
                                    </td> 
                                </tr>       
                                <tr>
                                    <td>Payment Receipt</td>
                                    <td>
                                        <input type="file" name="pay_file_name" id="payreceipt_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('payreceipt')" type="submit" id="payreceipt">Upload</button>
                                    </td>    
                                    <td>
                                        @if($uploads->supp_doc)
                                            @if(str_contains("R", $uploads->supp_doc))
                                                <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'visa' ]) }}" class="btn btn-success" id="payreceiptDown">Download</a>
                                            @endif
                                        @endif
                                    </td> 
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

    <div class="modal fade bs-example-modal-center" id="downloadSuppDoc" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Download Supporting Documents</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <a href="#" class="btn btn-success" id="eticketDown">E-Ticket</a>
                            <a href="#" class="btn btn-success" id="visaDown">Visa</a>
                            <a href="#" class="btn btn-success" id="passportDown">Passport</a>
                            <a href="#" class="btn btn-success" id="payreceiptDown">Pay Receipt</a>
                            <input type="hidden" id="idDownload" name="idDownload">
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

        $(document).ready(function() {
            $('#dtTable').dataTable({
                stateSave: true,
            });
        } )

        function editTaName (id, name) {
            $('#editTaDialog').modal('show');
            $('#ta_name').val(name);
            $('#ta_id').val(id);
        }

        function editEcertNumber (id) {
            $('#editEcertNumberDialog').modal('show');
            $('#id').val(id);
        }

        function downloadAll (id) {
            $('#btnAfter').hide();
            $('#btnClose').hide();
            $('#pleaseWaitDialog').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#pleaseWaitDialog').modal('show');
            $('#modalTitle').text('Generating/Merging all ECert ... Please Wait');
            
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

        // function deleteAll (id) {
        //     $.ajax({
        //         url: '/delete_all_cert/' + id,
        //         type: 'GET',
        //         success: function (data) {
        //             // $('#pleaseWaitDialog').modal('hide');
        //         }
        //     }); 
        // }

        // function openUploadDoc(id) {
        //     $('#showSuppDoc').modal('show');
        //     // $(document).ready(function() {
        //     //     var supp_id = id;
        //     //     $("#add_supp_doc" + id).val(null);
        //     //     $("#add_supp_doc" + id).trigger("click");

        //     //     $("#add_supp_doc" + supp_id).change(function () {
        //     //         var form_data = new FormData();
        //     //         form_data.append("file", $("#add_supp_doc" + supp_id)[0].files[0]);
        //     //         form_data.append("id", supp_id);
        //     //         $.ajax({
        //     //             url: '/supp_doc_post_admin',
        //     //             type: 'POST',
        //     //             data: form_data,
        //     //             dataType: 'JSON',
        //     //             cache: false,
        //     //             contentType: false,
        //     //             processData: false,
        //     //             success: function (data) {
        //     //                 alert(data.Data)
        //     //                 location.reload()
        //     //             }
        //     //         });
        //     //     });
        //     // });
        // }


        function chooseSupDoc (type) {
            if (type == 'eticket') {
                $("#eticket_file").trigger("click");
            } else if (type == 'visa') {
                $("#visa_file").trigger("click");
            } else if (type == 'passport') {
                $("#passport_file").trigger("click");
            } else if (type == 'payreceipt') {
                $("#payreceipt_file").trigger("click");
            }
        }

        $("#eticket_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#eticket_file")[0].files[0]);
            form_data.append("type", 'eticket');
            form_data.append("id", $("#suppId").val());
            $.ajax({
                url: '/supp_doc_post_admin',
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
                url: '/supp_doc_post_admin',
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
            $.ajax({
                url: '/supp_doc_post_admin',
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

        $("#payreceipt_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#payreceipt_file")[0].files[0]);
            form_data.append("type", 'payreceipt');
            form_data.append("id", $("#suppId").val());
            $.ajax({
                url: '/supp_doc_post_admin',
                type: 'POST',
                data: form_data,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert("Payment Receipt - " + data.Data);
                    //location.reload()
                }
            });
        });

        function downloadDetail (id) {
            $("#eticketDown").attr("href", "/supp_doc_download_admin/" + id + "/eticket")
            $('#downloadSuppDoc').modal('show');
            $("#idDownload").val(id);
        }

        $(document).ready(function() {
            $("#showSuppDoc").modal({
                keyboard: false,
                backdrop: 'static'
            });
        });

        function closeDetail() {
            //alert("close");
            location.reload();
        }

        function openDetail (id) {
            $('#showSuppDoc').modal('show');
            $("#suppId").val(id);
        }

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
