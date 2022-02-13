@extends('layouts.master')

@section('title') EXCEL LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
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
                            <a href="{{ route('download_template') }}" class="btn btn-primary w-md" target="_blank">Download Excel Template</a>
                            <button type="submit" class="btn btn-primary w-md" id="add_button">Add Excel</button>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">Refresh</button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0">#</th>
                                    <th data-priority="1">Requester</th>
                                    <th data-priority="1">Filename</th>
                                    {{--<th data-priority="3">Upload Date</th>--}}
                                    <th data-priority="3">Submission Date</th>
                                    <th data-priority="3">Supporting Documents</th>
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
                                        <td>{{ $upload->file_name }}</td>
                                        {{--<td>{{ $upload->upload_date ? date('d-m-Y', strtotime($upload->upload_date)) : ''}}</td>--}}
                                        <td>{{ $upload->submit_date ? date('d-m-Y', strtotime($upload->submit_date)) : '' }}</td>

                                        <td>
                                            @if($upload->status == '0' || $upload->status == '1' || $upload->status == '99')
                                                <p>-</p>
                                            @else 
                                                @if ($upload->supp_doc == null)
                                                    <p>Not Uploaded</p>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($upload->status == '3')
                                                <p>INVOICE READY</p>
                                            @elseif($upload->status == '5')
                                                <p>PAID</p>
                                            @else 
                                                <p>-</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($upload->status == '0' || $upload->status == '2')
                                                Pending AKC Approval
                                            @elseif ($upload->status == '2.1')
                                                <p>Pending AKC (Invoice) </p>
                                            @elseif ($upload->status == '3')
                                                Pending Payment
                                            @elseif ($upload->status == '4')
                                                Pending AKC (Payment) Endorsement
                                            @elseif ($upload->status == '5')
                                                COMPLETED
                                            @elseif ($upload->status == '99')
                                                REJECTED
                                            @endif
                                        </td>
                                        <td>
                                            @if ($upload->status == '2')
                                                <a href="{{ route('update_excel_status_admin', [$upload->id, '2.1']) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-paper-plane font-size-24" title="Approve" onclick="return confirm('Do you really want to APPROVE?');"></i>
                                                </a>
                                                <a href="{{ route('update_excel_status_admin', [$upload->id, '99']) }}" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-no-entry font-size-24" title="Reject" onclick="return confirm('Do you really want to REJECT?');"></i>
                                                </a>
                                            @elseif ($upload->status == '3')
                                               <a href="{{ route('admin_payment_detail', $upload->id) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-money font-size-24" title="Show Invoice"></i>
                                                </a>
                                            @elseif ($upload->status == '4')
                                                <a href="{{ route('admin_payment_detail', $upload->id) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-money font-size-24" title="Show Invoice"></i>
                                                </a>
                                            @elseif ($upload->status == '5')
                                                {{-- <a href="#" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-check-double font-size-24" title="Completed"></i>
                                                </a> --}}
                                                <a href="{{ route('create_invoice', $upload->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                            @elseif ($upload->status == '99')
                                                <a href="#" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-no-entry font-size-24" title="Rejected"></i>
                                                </a>
                                            @endif

                                            @if($upload->status == '2' || $upload->status == '2.1')
                                                <a href="{{ route('delete_excel_ta', $upload->id)}}" onclick="return confirm('Do you really want to delete?');" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-trash-alt font-size-24" title="Delete Excel"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('download_excel', $upload->id) }}" class="waves-effect" style="color: blue;">
                                                <i class="bx bxs-cloud-download font-size-24" title="Download Excel"></i>
                                            </a>

                                            @if ($upload->status != '0' && $upload->status != '1' && $upload->status != '2')
                                                <a href="{{ route('excel_detail_admin', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('upload_detail', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a> 
                                                {{-- <a href="{{ route('excel_detail_admin', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                                </a> --}}
                                            @endif                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th data-priority="0"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    {{--<th data-priority="3"></th>--}}
                                    <th data-priority="3"></th>
                                    <th data-priority="3"></th>
                                    <th data-priority="3"></th>
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

    <div class="modal fade bs-example-modal-xl" id="modal-excel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
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
                                                        {{-- <th data-priority="3">Add. Days</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label class="form-label">User Name</label>
                                                    <select id="user" name="user" class="form-control select2-search-disable" required>
                                                        @foreach ($users as $user)
                                                            <option value="{{$user->id}}">{{ strtoupper($user->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div>
                                                    <label class="form-label">Travel Agent Name</label>
                                                    {{-- <select id="travel_agent" name="travel_agent" class="form-control select2-search-disable" required> --}}
                                                        {{-- @if (auth()->user()->hasAnyRole('tra'))
                                                            <input class="form-control" type="text" name="travel_agent" value="{{ auth()->user()->name }}" readonly>
                                                        @elseif (auth()->user()->hasAnyRole('ag')) --}}
                                                    <input class="form-control" type="text" name="travel_agent" id="travel_agent" value="" placeholder="Please Insert Travel Agent">
                                                        {{-- @endif --}}
                                                    {{-- </select> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input class="form-check-input" type="checkbox" id="agreement">
                                                <label class="form-check-label" for="agreement">
                                                    Rekod telah disemak dan disahkan kesemua maklumat adalah betul dan lengkap
                                                </label>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <button type="button" class="btn btn-primary w-md" onclick="post_data()" id="submit_form">Submit</button>
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

        function clicked(e, id)
        {
            if(!confirm('Confirm to submit this Excel?')) {
                e.preventDefault();
            } else {
                var form_data = new FormData();
                form_data.append("id", id);
                $.ajax({
                    url: '/submit_post_admin',
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
                        url: '/supp_doc_post_admin',
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
            if (!confirm('Confirm to submit this Excel?')) {
                e.preventDefault();
            } else {

                var form_data = new FormData();
                form_data.append("travel_agent", $('#travel_agent').val());
                form_data.append("user", $('#user').val());
                form_data.append("file", $('#add_excel')[0].files[0]);
                form_data.append("file_name", $('#add_excel').val().split('\\').pop());
                form_data.append("json_post", JSON.stringify(json_post));
                
                if ($('#agreement').is(':checked') && $('#travel_agent').val()) {
                    $.ajax({
                    url: '/excel_post_admin',
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

        //enabling datatable filters
        $(document).ready(function() {
            $('#datatable').DataTable( {
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if (column[0]==1 || column[0]==2 || column[0]==3 || column[0]==6) {
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
