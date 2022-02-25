@extends('layouts.master')

@section('title') JEMAAH @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .form-inline .form-control {
            width: 30px;
            display:table-cell;
        }
        .form-group{
            display:table-cell;
        }
        .data-jemaah {
            font-size:1.10rem;
        }
        .hidden {
            display:none;
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') EDIT CLAIM @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3" style="text-align: left;">
                            <a href="{{ route('claim_detail', $jemaah->upload->id) }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                            </a>
                            <br><br><br>
                            <h4 class="card-title">Patient/Jemaah Information</h4>
                        </div>
                        <div class="col-md-9" style="text-align: left;">
                            <h6>Travel Agent Name: {{ $jemaah->upload->ta_name }}</h6>
                            <h6>Filename: {{ $jemaah->upload->file_name }}</h6>
                            <h6>Payment Status: {{ $jemaah->upload->status == '4' || $jemaah->upload->status == '5' ? 'PAID' : 'UNPAID' }}</h6>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-lg-3">
                            <div>
                                <label class="form-label">Name</label>
                                <p class="data-jemaah"><b>{{ $jemaah->name }}</b></p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">Passport No</label>
                                <p class="data-jemaah"><b>{{ $jemaah->passport_no }}</b></p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">IC No</label>
                                <p class="data-jemaah">{{ $jemaah->ic_no }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label class="form-label">Birth</label>
                                <p class="data-jemaah">{{$jemaah->dob ? date('d-m-Y', strtotime($jemaah->dob)): '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">
                            <div>
                                <label class="form-label">Existing Illness</label>
                                <p class="data-jemaah">{{ $jemaah->ex_illness }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">Email</label>
                                <p class="data-jemaah">{{ $jemaah->email }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">HP No</label>
                                <p class="data-jemaah">{{ $jemaah->hp_no }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">Departure Date</label>
                                <p class="data-jemaah">{{$jemaah->dep_date ? date('d-m-Y', strtotime($jemaah->dep_date)): '' }}</p>
                            </div>
                        </div>
                    </div>                    

                    <div class="row">
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">ECare</label>
                                <p class="data-jemaah">{{ $jemaah->plan_type }}   (<b>{{ $jemaah->ecert }}</b>)</p>
                            </div>
                        </div>  
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">PCR</label>
                                <p class="data-jemaah">{{ $jemaah->pcr }} 
                                    @if ($jemaah->pcr != 'NO')
                                        ({{ $jemaah->pcr_date }})
                                    @endif
                                </p>
                            </div>
                        </div> 
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">TPA</label>
                                <p class="data-jemaah">{{ $jemaah->tpa }}</p>
                            </div>
                        </div> 
                        <div class="col-lg-3">
                            <div>
                                <label for="plan">Return Date</label>
                                <p class="data-jemaah">{{$jemaah->return_date ? date('d-m-Y', strtotime($jemaah->return_date)): '' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" value="{{$jemaah->id}}" id="jemaahId" name="jemaahId">
                    <hr/>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <h5>Claim Entries</h5>
                                        <table id="table_claim" class="table table-striped table-advance table-hover w-100">
                                            <form class="form-inline" role="form">
                                                <thead>
                                                    <tr>
                                                        <td>#</td>
                                                        <td>Date</td>
                                                        <td>Pt. File#</td>
                                                        <td>Invoice#</td>
                                                        <td>Consultation</td>
                                                        <td>Drugs</td>
                                                        <td>Services</td>
                                                        <td>Discount</td>
                                                        <td>Action</td>
                                                    </tr>
                                                </thead>
                                            <tbody id="tbody">
                                            </tbody>
                                            </form> 
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9">
                                                        <a id="addBtn" class="btn btn-primary pull-left waves-effect waves-light">+ Add Row</a>
                                                        &nbsp;&nbsp;
                                                        <a id="save_btn" class="btn btn-primary pull-left waves-effect waves-light">Save</a>
                                                        &nbsp;&nbsp;
                                                        <span id="change_alert" class="alert hidden" style="color:red;">Changes made. Please click Save.</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>  
                                    </div>
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
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            var rowIdx = 0;

            $(document).on('change', 'input', function() {
                //console.log("Change: Editing... ");
                $('#change_alert').removeClass('hidden');
            });
            $(document).on('keyup', 'input', function() {
                //console.log("Keyup: Editing... ");
                $('#change_alert').removeClass('hidden');
            });


            $.ajax({
                url: '/get_claim_json/' + $('#jemaahId').val(),
                type: 'GET',
                success: function (data) {
                    if (data.Data) {
                        data.Data.forEach(e => {
                            $('#tbody').append(`<tr id="R${++rowIdx}">
                                <td class="row-index text-center">
                                    <p>${rowIdx}</p>
                                </td>
                                <td><div class="form-group"><input class="form-control" id="rowInput1" name="rowInput1" placeholder="Enter Date" type="date" value="${e.rowInput1}"></div></td>
                                <td><div class="form-group"><input class="form-control" id="rowInput2" name="rowInput2" placeholder="Enter Pt. File#" type="number" value="${e.rowInput2}"></div></td>
                                <td><div class="form-group"><input class="form-control" name="rowInput3" placeholder="Enter Invoice#" type="number" value="${e.rowInput3}"></div></td>
                                <td><div class="form-group"><input class="form-control" name="rowInput4" placeholder="Enter Consultation" type="number" value="${e.rowInput4}"></div></td>
                                <td><div class="form-group"><input class="form-control" name="rowInput5" placeholder="Enter Drugs" type="number" value="${e.rowInput5}"></div></td>
                                <td><div class="form-group"><input class="form-control" name="rowInput6" placeholder="Enter Services" type="number" value="${e.rowInput6}"></div></td>
                                <td><div class="form-group"><input class="form-control" name="rowInput7" placeholder="Enter Discount" type="number" value="${e.rowInput7}">
                                <td><a class="pull-right waves-effect waves-light remove" style="color: red;" type="button"><i class="bx bx-trash-alt font-size-24" title="Delete Row"></i></a></td>
                                </tr>`);
                        });
                    }
                }
            });

            $('#addBtn').on('click', function () {
                $('#tbody').append(`<tr id="R${++rowIdx}">
                    <td class="row-index text-center">
                        <p>${rowIdx}</p>
                    </td>
                    <td><div class="form-group"><input class="form-control" name="rowInput1" placeholder="Enter Date" type="date" value=""></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput2" placeholder="Enter Pt. File#" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput3" placeholder="Enter Invoice#" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput4" placeholder="Enter Consultation" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput5" placeholder="Enter Drugs" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput6" placeholder="Enter Services" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput7" placeholder="Enter Discount" type="number">
                    <td><a class="pull-right waves-effect waves-light remove" style="color: red;" type="button"><i class="bx bx-trash-alt font-size-24" title="Delete Row"></i></a></td>
                    </tr>`);
            });
            

            $('#tbody').on('click', '.remove', function () {
                if (!confirm('Are you sure to delete?')) {
                } else {
                    var child = $(this).closest('tr').nextAll();
                    child.each(function () {
                    var id = $(this).attr('id');
                    var idx = $(this).children('.row-index').children('p');
                    var dig = parseInt(id.substring(1));
                    idx.html(`${dig - 1}`);
                    $(this).attr('id', `R${dig - 1}`);
                    });
                    $(this).closest('tr').remove();
                    rowIdx--;

                    $('#change_alert').removeClass('hidden');
                }
            });

            $("#save_btn").click(function(){
                $('#change_alert').removeClass('hidden').addClass('hidden');
                const data = new Array();
                const dataAll = new Array();
                for (let index = 1; index <= rowIdx; index++) {
                    $('#R' + index + '> td').find("input").each(function() {
                        data.push(this.value);
                    });
                }
                $.ajax({
                    url: '/claim_add',
                    type: 'POST',
                    data: {
                        id: $('#jemaahId').val(),
                        jsonData: JSON.stringify(data),
                    },
                    success: function (data) {
                        alert(data.Data);
                        location.reload();
                    }
                });
            });

        });
        
    </script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
