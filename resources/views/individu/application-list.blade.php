@extends('layouts.master')

@section('title') APPLICATION LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') INDIVIDU @endslot
        @slot('title') APPLICATION LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <input type="file" name="add_excel" id="add_excel" style="display: none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <div class="col-md-6 text-left">
                            <a href="#" class="btn btn-primary w-md" id="button-choose">Apply</a>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn">
                                <i class="bx bx-loader-circle font-size-20" title="Refresh"></i>
                            </button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Applicant Name</th>
                                    <th data-priority="3">Upload Date</th>
                                    <th data-priority="1">Submission Date</th>
                                    <th data-priority="1">Status</th>
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uploads as $i => $upload)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $upload->user->name }}</td>
                                        <td>{{ $upload->upload_date ? date('d-m-Y', strtotime($upload->upload_date)) : ''}}</td>
                                        <td>{{ $upload->submit_date ? date('d-m-Y', strtotime($upload->submit_date)) : '' }}</td>
                                        <td>
                                            @if ($upload->status == '0')
                                                Pending Submission
                                            @elseif ($upload->status == '2')
                                                <p>Waiting Approval</p>
                                            @elseif ($upload->status == '3')
                                                Pending Payment
                                            @elseif ($upload->status == '4')
                                                <p>Waiting Finance Endorse</p>
                                            @elseif ($upload->status == '5')
                                                Finished
                                            @elseif ($upload->status == '99')
                                                Rejected
                                            @endif
                                        </td>
                                        <td>
                                            {{-- <input type="hidden" name="id" value="{{ $upload->id }}" id="upload_id"> --}}
                                            @if ($upload->status == '0')
                                                <a class="waves-effect">
                                                    <a href="#" class="waves-effect" style="color: green;">
                                                        <i class="bx bx-paper-plane font-size-20" title="Submit" onclick="clicked(event, {{$upload->id}})"></i>
                                                    </a>
                                                </a>
                                            {{-- @elseif ($upload->status == '1')
                                                <a href="#" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-paper-plane font-size-20" title="Submit" onclick="clicked(event, {{$upload->id}})"></i>
                                                </a> --}}
                                                {{-- <a href="#" class="waves-effect" style="color: yellow;">
                                                    <i class="bx bxs-collection font-size-20" title="Detail"></i>
                                                </a> --}}
                                            @elseif ($upload->status == '2')
                                                {{-- <p>Waiting Approval</p> --}}
                                            @elseif ($upload->status == '3')
                                                <a href="{{ route('payment', $upload->id) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-money font-size-20" title="Pay"></i>
                                                </a>
                                            @elseif ($upload->status == '4')
                                                {{-- <p>Waiting Finance Endorse</p> --}}
                                            @elseif ($upload->status == '5')
                                                {{-- <a href="{{ route('download_cert') }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-food-menu font-size-20" title="Print E-Cert"></i>
                                                </a> --}}
                                            @elseif ($upload->status == '99')
                                                <a href="#" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-no-entry font-size-20" title="Rejected"></i>
                                                </a>
                                            @endif

                                            @if ($upload->supp_doc == null)
                                                <a href="#" class="waves-effect" style="color: blue;">
                                                    <input type="file" name="add_supp_doc{{$upload->id}}" id="add_supp_doc{{$upload->id}}" style="display: none;" accept=".zip,.rar,.7zip">
                                                    <i onclick="openDetail({{$upload->id}})" class="bx bxs-cloud-upload font-size-20" title="Upload"></i>
                                                </a>
                                            @endif

                                            @if ($upload->status != '0' && $upload->status != '1' && $upload->status != '2')
                                                <a href="{{ route('application_detail', $upload->id) }}" class="waves-effect" style="color: pink;">
                                                    <i class="bx bxs-collection font-size-20" title="Detail"></i>
                                                </a>
                                            @elseif($upload->file_name === null)
                                                <a href="{{ route('application_detail', $upload->id) }}" class="waves-effect" style="color: pink;">
                                                    <i class="bx bxs-collection font-size-20" title="Detail"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('upload_detail', $upload->id) }}" class="waves-effect" style="color: pink;">
                                                    <i class="bx bxs-collection font-size-20" title="Detail"></i>
                                                </a>
                                            @endif
                                            
                                            @if($upload->status == '0' || $upload->status == '1' || $upload->status == '3')
                                                <a href="{{ route('application_delete', $upload->id)}}" onclick="return confirm('Do you really want to delete?');" class="waves-effect" style="color: red;">
                                                    <i class="bx bx-trash-alt font-size-20" title="Delete"></i>
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

    <div class="modal fade bs-example-modal-xs" id="modal-choose" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs">
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
                                <div class="card-body text-center">
                                    <h4 class="card-title">Please Choose</h4>
                                    <p>If more than 1 pax, please choose excel</p>
                                    <br>
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary w-md" id="add_button">Excel</button>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('application') }}" class="btn btn-primary w-md" id="download_cert">Form</a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a href="{{ route('download_template') }}" class="btn btn-primary w-md" target="_blank">Download Excel Template</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
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
                                    <h5 class="card-title">Total Record: <s style="text-decoration: none" id="total_records"></s></h5>
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                                            <table id="exceltable" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th data-priority="1">Traveller Name</th>
                                                        <th data-priority="3">Passport</th>
                                                        <th data-priority="1">ID Number</th>
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
                                                    <label class="form-label">Travel Agent Name</label>
                                                    <input class="form-control" type="text" name="travel_agent" id="travel_agent" value="{{ auth()->user()->name }}" readonly>
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
            if(!confirm('Are you sure to submit?')) {
                e.preventDefault();
            } else {
                var form_data = new FormData();
                form_data.append("id", id);
                $.ajax({
                    url: '/submit_post_ind',
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
                        url: '/supp_doc_post_ind',
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

        $("#button-choose").click(function () {
            $('#modal-choose').modal('show');
        });
        

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
                    $('#modal-choose').modal('hide');
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
            var columns = BindTableHeader(jsondata, tableid); /*Gets all the column headings of Excel*/
            for (var i = 0; i < jsondata.length; i++) {
                var row$ = $('<tr/>');
                for (var colIndex = 0; colIndex < 4; colIndex++) {
                    var cellValue = jsondata[i][columns[colIndex]];
                    if (cellValue == null)
                        cellValue = "";
                    row$.append($('<td/>').html(cellValue));
                }
                $(tableid).append(row$);
            }
        }

        var json_post

        function BindTableHeader(jsondata, tableid) {/*Function used to get all column names from JSON and bind the html table header*/
            $("#total_records").text(jsondata.length);
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
            form_data.append("file", $('#add_excel')[0].files[0]);
			form_data.append("file_name", $('#add_excel').val().split('\\').pop());
			form_data.append("json_post", JSON.stringify(json_post));
            form_data.append("travel_agent", $('#travel_agent').val());
            
            if ($('#agreement').is(':checked')) {
                $.ajax({
				url: '/application_post_excel',
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
                alert("Please tick agreement to submit!");
            }

			return false;
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
