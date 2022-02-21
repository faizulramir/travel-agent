@extends('layouts.master')

@section('title') EXCEL LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') EXCEL LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">
                                Refresh
                                <!--<i class="bx bx-loader-circle font-size-24" title="Refresh"></i>-->
                            </button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0">#</th>
                                    <th data-priority="1">Requester</th>
                                    <th data-priority="1">Filename</th>
                                    <th data-priority="1">Jemaah</th>
                                    <th data-priority="1">Submission Date</th>
                                    <th data-priority="3">Supp. Docs</th>
                                    <th data-priority="1">Payment</th>
                                    <th data-priority="1">Status</th>
                                    <th data-priority="1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uploads as $i => $upload)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ strtoupper($upload->user->name) }}</td>
                                        <td>{{ strtoupper($upload->file_name) }}</td>

                                        <td>
                                            @if($rec_count_arr && $rec_count_arr[$i] && $rec_count_arr[$i] > 0)
                                                {{ $rec_count_arr[$i] }}
                                            @endif
                                        </td>

                                        <td>{{ $upload->submit_date ? date('d-m-Y H:i:s', strtotime($upload->submit_date)) : '' }}</td>
                                        <td>
                                            @if ($upload->supp_doc == null)
                                                <span>Not Uploaded</span>
                                            @else
                                                <span>UPLOADED</span>
                                            @endif
                                        </td>
                                        <td>{{ $upload->status == '5' || $upload->status == '4' ? 'PAID' : 'UNPAID' }}</td>
                                        <td>
                                            @if ($upload->status == '4')
                                                Pending (Payment) Endorsement
                                            @elseif ($upload->status == '5')
                                                ENDORSED
                                            @elseif ($upload->status == '2.1')
                                                Pending (Invoice) Endorsement
                                            @else 
                                                -
                                            @endif
                                        </td>
                                        <td>

                                            {{--<a href="{{ route('upload_detail', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                            </a>--}}

                                            @if ($upload->status == '5')
                                                <a href="{{ route('create_invoice', $upload->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('payment_detail', $upload->id) }}" class="waves-effect" style="color: green;">
                                                <i class="bx bx-money font-size-24" title="Payment Detail"></i>
                                            </a>

                                            {{-- @if ($upload->payment)
                                                <a href="{{ route('download_payment', [$upload->user_id, $upload->id]) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-food-menu font-size-24" title="Show Payslip/Receipt"></i>
                                                </a>
                                            @endif --}}

                                            @if($upload->status != '0' && $upload->status != '2')
                                                <a href="#" class="waves-effect" style="color: black;">
                                                    <i onclick="openDetail({{$upload->id}},'{{$upload->supp_doc}}')" class="bx bxs-cloud-upload font-size-24" title="Supporting Documents"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('excel_detail_finance', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th data-priority="0"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                </tr>
                            </tfoot>
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
                    <h5 class="modal-title" id="modalTitle">Supporting Documents AKC ADM</h5>
                    <button type="button" id="btnClose" onclick="closeDetail()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-left">
                    <div class="row text-left">
                        <div class="col-md-12">
                            <input type="hidden" id="suppId" name="suppId">
                            <input type="hidden" id="idDownload" name="idDownload">
                            <input type="hidden" id="suppdocs" name="suppdocs">

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

                            <table border="0" width="100%" id="tableUploadDownload">
                                <tr>
                                    <td width="50%">Document Passport</td>
                                    <td width="25%">
                                        <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Upload</button>
                                    </td>    
                                    <td width="25%" id="passportdownload">
                                        {{--
                                        @if($uploads->supp_doc)
                                            @if(str_contains("P", $uploads->supp_doc))
                                                <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'passport' ]) }}" class="btn btn-success" id="passportDown">Download</a>
                                            @endif
                                        @endif
                                        --}}
                                    </td> 
                                </tr>  
                                <tr>
                                    <td>Document E-Ticket</td>
                                    <td>
                                        <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">Upload</button>
                                    </td>    
                                    <td id="eticketdownload">
                                        {{--
                                            @if($uploads->supp_doc)
                                                @if(str_contains("T", $uploads->supp_doc))
                                                    <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'eticket' ]) }}" class="btn btn-success" id="eticketDown">Download</a>
                                                @endif
                                            @endif
                                        --}}
                                    </td> 
                                </tr>       
                                <tr>
                                    <td>Document E-Visa</td>
                                    <td>
                                        <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Upload</button>
                                    </td>    
                                    <td id="visadownload">
                                        {{--
                                            @if($uploads->supp_doc)
                                                @if(str_contains("V", $uploads->supp_doc))
                                                    <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'visa' ]) }}" class="btn btn-success" id="visaDown">Download</a>
                                                @endif
                                            @endif
                                        --}}
                                    </td> 
                                </tr>       
                                <tr>
                                    <td>Payment Receipt</td>
                                    <td>
                                        <input type="file" name="pay_file_name" id="payreceipt_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('payreceipt')" type="submit" id="payreceipt">Upload</button>
                                    </td>    
                                    <td id="payreceiptdownload">
                                        {{--
                                            @if($uploads->supp_doc)
                                                @if(str_contains("R", $uploads->supp_doc))
                                                    <a  href="{{ route('supp_doc_download_admin', [ $uploads->id, 'visa' ]) }}" class="btn btn-success" id="payreceiptDown">Download</a>
                                                @endif
                                            @endif
                                        --}}
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
                    location.reload()
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
                    location.reload()
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
                    location.reload()
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
                    location.reload()
                }
            });
        });

        function downloadDetail (type, id) {
            $("#eticketDown").attr("href", "/supp_doc_download_admin/" + id + "/" + type)
            // $('#downloadSuppDoc').modal('show');
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
            //location.reload();
        }

        function openDetail (id, docs) {
            $("#suppId").val(id);
            $("#suppdocs").val(docs);
            
            if (docs.includes('P')) {
                $('#passportdownload').html('<a href="/supp_doc_download_admin/' + id + '/passport" class="btn btn-success" type="submit">Download</a>');
            } else {
                $('#passportdownload').html('');
            }

            if (docs.includes('T')) {
                $('#eticketdownload').html('<a href="/supp_doc_download_admin/' + id + '/eticket" class="btn btn-success" type="submit">Download</a>');
            } else {
                $('#eticketdownload').html('');
            }

            if (docs.includes('V')) {
                $('#visadownload').html('<a href="/supp_doc_download_admin/' + id + '/visa" class="btn btn-success" type="submit">Download</a>');
            } else {
                $('#visadownload').html('');
            }

            if (docs.includes('R')) {
                $('#payreceiptdownload').html('<a href="/supp_doc_download_admin/' + id + '/payreceipt" class="btn btn-success" type="submit">Download</a>');
            } else {
                $('#payreceiptdownload').html('');
            }

            $('#showSuppDoc').modal('show');

            
            // $(document).ready(function() {
            //     var supp_id = id;
            //     $("#add_supp_doc" + id).val(null);
            //     $("#add_supp_doc" + id).trigger("click");

            //     $("#add_supp_doc" + supp_id).change(function () {
            //         var form_data = new FormData();
            //         form_data.append("file", $("#add_supp_doc" + supp_id)[0].files[0]);
            //         form_data.append("id", supp_id);
            //         $.ajax({
            //             url: '/supp_doc_post_admin',
            //             type: 'POST',
            //             data: form_data,
            //             dataType: 'JSON',
            //             cache: false,
            //             contentType: false,
            //             processData: false,
            //             success: function (data) {
            //                 alert(data.Data)
            //                 location.reload()
            //             }
            //         });
            //     });
            // });
        }

        $("#add_button").click(function () {
            $("#add_excel").val(null);
            $("#add_excel").trigger("click");
        });

        

        $('#refreshBtn').click(function() {
            location.reload();
        });

        function approved(e)
        {
            if(!confirm('Are you sure to Approve?')) {
                e.preventDefault();
            }
        }

        function reject(e)
        {
            if(!confirm('Are you sure to Reject?')) {
                e.preventDefault();
            }
        }

        //enabling datatable filters
        $(document).ready(function() {
            $('#datatable').DataTable( {
                saveState: true,
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if (column[0]==1 || column[0]==2 || column[0]==4 || column[0]==6) {
                            var select = $('<select><option value=""></option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column
                                        .search( val ? '^'+val+'$' : '', true, false )
                                        .draw();
                                } );
                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            } );
                        }
                    } );
                }
            } );
        } );

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
