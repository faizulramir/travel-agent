@extends('layouts.master')

@section('title') EXCEL LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />


    <style>

        .action_icon {
            font-size: 1.55rem;
        }

    </style>

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') TRAVEL AGENT @endslot
        @slot('title') EXCEL LIST @endslot
    @endcomponent

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
                        <div class="col-md-6">
                            <input type="file" name="add_excel" id="add_excel" style="display: none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <a href="{{ route('download_template') }}" class="btn btn-primary w-md" target="_blank" title="Download current template">Download Excel Template</a>
                            <button type="submit" class="btn btn-primary w-md" id="add_button" title="Upload a new Excel">Add Excel</button>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">
                                Refresh
                                <!--<i class="bx bx-loader-circle font-size-24" title="Refresh"></i>-->
                            </button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Filename</th>
                                    <th data-priority="3">Upload Date</th>
                                    <th data-priority="1">Submission Date</th>
                                    <th data-priority="1">Supp. Docs</th>
                                    <th data-priority="1">Payment</th>
                                    <th data-priority="1">Status</th>
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uploads as $i => $upload)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $upload->file_name }}</td>
                                        <td>{{ $upload->upload_date ? date('d-m-Y', strtotime($upload->upload_date)) : ''}}</td>
                                        <td>{{ $upload->submit_date ? date('d-m-Y H:i:s', strtotime($upload->submit_date)) : '' }}</td>
                                        <td>
                                            @if($upload->status == '0' || $upload->status == '1' || $upload->status == '99')
                                                <span>-</span>
                                            @else 
                                                @if ($upload->supp_doc == null)
                                                    <span>Not Uploaded</span>
                                                @else
                                                    <span>UPLOADED</span>
                                                @endif
                                                &nbsp;&nbsp;
                                                <a href="#" class="waves-effect" style="color: black;">
                                                    <input type="file" name="add_supp_doc{{$upload->id}}" id="add_supp_doc{{$upload->id}}" style="display: none;" accept=".zip,.rar,.7zip">
                                                    <i onclick="openDetail({{$upload->id}})" class="bx bxs-cloud-upload font-size-24" title="Supporting Documents"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($upload->status == '3')
                                                <p>INVOICE READY</p>
                                            @elseif($upload->status == '4' || $upload->status == '5')
                                                <p>PAID</p>
                                            @else 
                                                <p>-</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($upload->status == '0')
                                                Pending Submission
                                            @elseif ($upload->status == '2')
                                                <p>Pending AKC Approval</p>
                                            @elseif ($upload->status == '2.1')
                                                <p>Pending AKC Invoice</p>
                                            @elseif ($upload->status == '3')
                                                Pending Payment
                                            @elseif ($upload->status == '4')
                                                <p>Pending AKC (Payment) Endorsement</p>
                                            @elseif ($upload->status == '5')
                                                COMPLETED
                                            @elseif ($upload->status == '99')
                                                REJECTED
                                            @endif
                                        </td>
                                        <td>
                                            {{-- <input type="hidden" name="id" value="{{ $upload->id }}" id="upload_id"> --}}
                                            @if ($upload->status == '0')
                                                <a class="waves-effect">
                                                    <a href="#" class="waves-effect" style="color: green;">
                                                        <i class="bx bx-paper-plane font-size-24" title="Submit to AKC" onclick="clicked(event, {{$upload->id}})"></i>
                                                    </a>
                                                </a>
                                            {{-- @elseif ($upload->status == '1')
                                                <a href="#" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-paper-plane font-size-24" title="Submit to AKC" onclick="clicked(event, {{$upload->id}})"></i>
                                                </a> --}}
                                                {{-- <a href="#" class="waves-effect" style="color: yellow;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a> --}}
                                            @elseif ($upload->status == '2')
                                                {{-- <p>Pending AKC Approval</p> --}}
                                            @elseif ($upload->status == '3')
                                                <a href="{{ route('payment', $upload->id) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-money font-size-24" title="Make Payment"></i>
                                                </a>
                                            @elseif ($upload->status == '4')
                                                {{-- <p>Pending AKC (Payment) Endorsement</p> --}}
                                            @elseif ($upload->status == '5')
                                                {{-- <a href="{{ route('download_invoice') }}" class="waves-effect" style="color: blue;">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a> --}}
                                                <a href="{{ route('create_invoice', $upload->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                            @elseif ($upload->status == '99')
                                                <a href="#" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-no-entry font-size-24" title="Rejected"></i>
                                                </a>
                                            @endif

                                            @if($upload->status == '0' || $upload->status == '1' || $upload->status == '2' || $upload->status == '2.1' || $upload->status == '3')
                                                <a href="{{ route('delete_excel_ta', $upload->id)}}" onclick="return confirm('Do you really want to delete?');" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-trash-alt font-size-24" title="Delete Excel"></i>
                                                </a>
                                            @endif
                                            
                                            {{--
                                            @if ($upload->supp_doc == null)
                                                <a href="#" class="waves-effect" style="color: black;">
                                                    <input type="file" name="add_supp_doc{{$upload->id}}" id="add_supp_doc{{$upload->id}}" style="display: none;" accept=".zip,.rar,.7zip">
                                                    <i onclick="openDetail({{$upload->id}})" class="bx bxs-cloud-upload font-size-24" title="Upload Supporting Documents"></i>
                                                </a>
                                            @endif
                                            --}}
                                            
                                            @if ($upload->status != '0' && $upload->status != '1' && $upload->status != '2')
                                                <a href="{{ route('excel_detail_ta', $upload->id) }}" class="waves-effect" style="color:#ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('upload_detail', $upload->id) }}" class="waves-effect" style="color:#ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a>
                                            @endif
                                            

                                            {{-- @if ($upload->status == '4')
                                                <a href="#" class="waves-effect" style="color:#ed2994;">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                            @endif
                                            @if ($upload->status == '4')
                                                <a href="#" class="waves-effect" style="color:#ed2994;">
                                                    <i class="bx bxs-printer font-size-24" title="Print Receipt"></i>
                                                </a>
                                            @endif --}}
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

    <div class="modal fade bs-example-modal-xl" id="modal-excel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Excel Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Filename: <s style="text-decoration: none" id="filename"></s></h5>
                                    <h5 class="card-title">Total Jemaah: <s style="text-decoration: none" id="total_records"></s></h5>
                                    <div style="max-height:420px;overflow-y:scroll;overflow-h:hidden;">
                                        <div class="table-rep-plugin">
                                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                <table id="exceltable" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th data-priority="1">Name</th>
                                                            <th data-priority="3">Passport No</th>
                                                            <th data-priority="1">IC No</th>
                                                            <th data-priority="1">E-Care</th>
                                                            <th data-priority="1">DEP Date</th>
                                                            <th data-priority="1">RTN Date</th>
                                                            <th data-priority="1">PCR</th>
                                                            <th data-priority="1">TPA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label class="form-label">Travel Agent Name</label>
                                                    @if (auth()->user()->hasAnyRole('tra'))
                                                        <input class="form-control" type="text" name="travel_agent" id="travel_agent" value="{{ strtoupper(auth()->user()->name) }}" readonly>
                                                    @elseif (auth()->user()->hasAnyRole('ag'))
                                                        <input class="form-control" type="text" name="travel_agent" id="travel_agent" value="" placeholder="Please Insert Travel Agent">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <br>
                                            <div class="col-lg-12">
                                                <input class="form-check-input" type="checkbox" id="agreement">
                                                <label class="form-check-label" style="color:red;" for="agreement">
                                                    &nbsp;&nbsp;<b>Rekod telah disemak dan disahkan kesemua maklumat adalah betul dan lengkap</b>
                                                </label>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-primary w-md" onclick="post_data()" id="submit_form">Confirm</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade bs-example-modal-center" id="showSuppDoc" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Supporting Documents</h5>
                    <button type="button" id="btnClose" onclick="closeDetail()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-left">
                    <div class="row text-left">
                        <div class="col-md-12">
                            <input type="hidden" id="suppId" name="suppId">

                            {{--
                            <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                            <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                            <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                            <input type="file" name="pay_file_name" id="payreceipt_file" style="display: none;">
                            <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">E-Ticket</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Visa</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Passport</button>
                            <button class="btn btn-primary" onclick="chooseSupDoc('payreceipt')" type="submit" id="payreceipt">Pay Receipt</button>
                            <br><br>
                            --}}
                            
                            <table border="0" width="100%">
                                <tr>
                                    <td width="50%">Document Passport</td>
                                    <td width="25%">
                                        <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Upload</button>
                                    </td>    
                                    <td width="25%">
                                        {{--
                                        <input type="file" name="passport_file_name" id="passport_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('passport')" type="submit" id="passport">Download</button>
                                        --}}
                                    </td> 
                                </tr>  
                                <tr>
                                    <td>Document E-Ticket</td>
                                    <td>
                                        <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">Upload</button>
                                    </td>    
                                    <td>
                                        {{--
                                        <input type="file" name="eticket_file_name" id="eticket_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('eticket')" type="submit" id="eticket">Download</button>
                                        --}}
                                    </td> 
                                </tr>       
                                <tr>
                                    <td>Document E-Visa</td>
                                    <td>
                                        <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Upload</button>
                                    </td>    
                                    <td>
                                        {{--
                                        <input type="file" name="visa_file_name" id="visa_file" style="display: none;">
                                        <button class="btn btn-primary" onclick="chooseSupDoc('visa')" type="submit" id="visa">Download</button>
                                        --}}
                                    </td> 
                                </tr>                                                              
                                <tr><td colspan="3">&nbsp;</td></tr>                                                                  
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--
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
    --}}

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

        $('#refreshBtn').click(function() {
            location.reload();
        });

        $(document).ready(function() {
            $('#datatable').DataTable( {
                stateSave: true,
            });
        });

        function clicked(e, id)
        {
            if(!confirm('Confirm to submit this Excel?')) {
                e.preventDefault();
            } else {
                var form_data = new FormData();
                form_data.append("id", id);
                $.ajax({
                    url: '/submit_post_ta',
                    type: 'POST',
                    data: form_data,
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        alert(data.Data)
                        location.reload()
                    }
                });
            }
        }

        {{--
        function openDetail (id) {
            $(document).ready(function() {
                var supp_id = id;
                $("#add_supp_doc" + id).val(null);
                $("#add_supp_doc" + id).trigger("click");

                $("#add_supp_doc" + supp_id).change(function () {
                    var form_data = new FormData();
                    form_data.append("file", $("#add_supp_doc" + supp_id)[0].files[0]);
                    form_data.append("id", supp_id);
                    $.ajax({
                        url: '/supp_doc_post_ta',
                        type: 'POST',
                        data: form_data,
                        dataType: 'JSON',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            alert(data.Data)
                            location.reload()
                        }
                    });
                });
            });

            return supp_id;
        }
        --}}

        $("#add_button").click(function () {
            $("#add_excel").val(null);
            $("#add_excel").trigger("click");
        });

        $("#add_excel").change(function () {
            openModal();
        });

        function openModal(){
            var filename = $('#add_excel').val().split('\\').pop();
            $("#filename").text(filename);
            $("#exceltable > tbody").empty();
            ExportToTable();
        }

        function ExportToTable() {
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xlsx|.xls)$/;
            /*Checks whether the file is a valid excel file*/
            if (regex.test($("#add_excel").val().toLowerCase())) {
                var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/
                if ($("#add_excel").val().toLowerCase().indexOf(".xlsx") > 0) {
                    xlsxflag = true;
                }
                /*Checks whether the browser supports HTML5*/
                if (typeof (FileReader) != "undefined") {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var data = e.target.result;
                        /*Converts the excel data in to object*/
                        if (xlsxflag) {
                            var workbook = XLSX.read(data, { type: 'binary' });
                        }
                        else {
                            var workbook = XLS.read(data, { type: 'binary' });
                        }
                        /*Gets all the sheetnames of excel in to a variable*/
                        var sheet_name_list = workbook.SheetNames;

                        var cnt = 0; /*This is used for restricting the script to consider only first sheet of excel*/
                        sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/
                            /*Convert the cell value to Json*/
                            if (xlsxflag) {
                                var exceljson = XLSX.utils.sheet_to_json(workbook.Sheets[y]);
                            }
                            else {
                                var exceljson = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y]);
                            }
                            if (exceljson.length > 0 && cnt == 0) {
                                BindTable(exceljson, '#exceltable');
                                cnt++;
                            }
                        });
                        $('#exceltable').show();
                    }
                    if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/
                        reader.readAsArrayBuffer($("#add_excel")[0].files[0]);
                    }
                    else {
                        reader.readAsBinaryString($("#add_excel")[0].files[0]);
                    }
                    $('#modal-excel').modal('show');
                }
                else {
                    alert("Sorry! Your browser does not support HTML5!");
                }
            }
            else {
                alert("Please upload a valid Excel file!");
            }
        }

        function BindTable(jsondata, tableid) {/*Function used to convert the JSON array to Html Table*/
            var rowCount = 0;
            var columns = BindTableHeader(jsondata, tableid); /*Gets all the column headings of Excel*/
            for (var i = 0; i < jsondata.length; i++) {
                var row$ = $('<tr/>');
                /*
                for (var colIndex = 0; colIndex < 4; colIndex++) {
                    var cellValue = jsondata[i][columns[colIndex]];
                    if (cellValue == null)
                        cellValue = "";
                    row$.append($('<td/>').html(cellValue));
                }
                */
                if (jsondata[i][columns[0]] == null) {}
                else {
                    for (var colIndex = 0; colIndex < 13; colIndex++) {
                        if (colIndex!=4 && colIndex!=5 && colIndex!=6 && colIndex!=8) {
                            var cellValue = jsondata[i][columns[colIndex]];
                            if (cellValue == null) cellValue = "";
                            else {
                                if (colIndex==9 || colIndex==10) {
                                    //console.log(i, colIndex,jsondata[i][columns[colIndex]]);
                                    //console.log(new Date(Math.round((cellValue - 25569)*86400*1000)));
                                    cellValue = ExcelDateToJSDate(cellValue);
                                }
                            }
                            
                            row$.append($('<td/>').html(cellValue));
                        }
                    }
                    $(tableid).append(row$);
                    ++rowCount;
                }
            }
            $("#total_records").text(rowCount);
        }

        var json_post

        function BindTableHeader(jsondata, tableid) {/*Function used to get all column names from JSON and bind the html table header*/
            //$("#total_records").text(jsondata.length);
            json_post = jsondata;
            var columnSet = [];
            var headerTr$ = $('<tr/>');
            for (var i = 0; i < 4; i++) {
                var rowHash = jsondata[i];
                for (var key in rowHash) {
                    if (rowHash.hasOwnProperty(key)) {
                        if ($.inArray(key, columnSet) == -1) {/*Adding each unique column names to a variable array*/
                            // console.log(key);
                            // if (key == 'NO' || key == 'NAME' || key == 'PASSPORT NO' || key == 'IDENTITY CARD NO')  {
                                columnSet.push(key);
                            // }
                            // headerTr$.append($('<th/>').html(key));
                        }
                    }
                }
            }
            // $(tableid).append(headerTr$);
            return columnSet;
        }


        function post_data() {
            var form_data = new FormData();
            form_data.append("travel_agent", $('#travel_agent').val());
            form_data.append("file", $('#add_excel')[0].files[0]);
			form_data.append("file_name", $('#add_excel').val().split('\\').pop());
			form_data.append("json_post", JSON.stringify(json_post));

            if ($('#agreement').is(':checked') && $('#travel_agent').val()) {
                $.ajax({
				url: '/excel_post_ta',
				type: 'POST',
				data: form_data,
				dataType: 'JSON',
                cache: false,
				contentType: false,
                processData: false,
                    success: function (data) {
                        alert(data.Data)
                        location.reload()
                    }
                });
            } else {
                alert("Please check all input to submit!");
            }

			return false;
		}


        function ExcelDateToJSDate(serial) {
            var utc_days  = Math.floor(serial - 25569);
            var utc_value = utc_days * 86400;                                        
            var date_info = new Date(utc_value * 1000);

            var fractional_day = serial - Math.floor(serial) + 0.0000001;

            var total_seconds = Math.floor(86400 * fractional_day);

            var seconds = total_seconds % 60;

            total_seconds -= seconds;

            var hours = Math.floor(total_seconds / (60 * 60));
            var minutes = Math.floor(total_seconds / 60) % 60;

            //return new Date(date_info.getFullYear(), date_info.getMonth(), date_info.getDate(), hours, minutes, seconds);
            return ''+( date_info.getDate()>9?date_info.getDate():'0'+date_info.getDate())+'-'+((date_info.getMonth()+1)>9? (date_info.getMonth()+1):'0'+(date_info.getMonth()+1))+'-'+date_info.getFullYear();
        }




        //supporting documents ....
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

        $("#payreceipt_file").change(function () {
            var form_data = new FormData();
            form_data.append("file", $("#payreceipt_file")[0].files[0]);
            form_data.append("type", 'payreceipt');
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
                    alert(data.Data)
                    //location.reload()
                }
            });
        });

        {{--
        function downloadDetail (id) {
            $("#eticketDown").attr("href", "/supp_doc_download_admin/" + id + "/eticket")
            $('#downloadSuppDoc').modal('show');
            $("#idDownload").val(id);
        }
        --}}

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

        function openDetail(id) {
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
