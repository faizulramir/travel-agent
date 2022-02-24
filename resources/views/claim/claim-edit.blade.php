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
                                <p class="data-jemaah">{{ $jemaah->dep_date }}</p>
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
                                <p class="data-jemaah">{{ $jemaah->pcr }}</p>
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
                                <p class="data-jemaah">{{ $jemaah->return_date }}</p>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <h5>Claim Entries</h5>
                                        <table id="table_claim" class="table table-striped table-advance table-hover w-100">
                                            <form class="form-inline" role="form">
                                            <tbody>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Date</td>
                                                    <td>Pt. File#</td>
                                                    <td>Invoice#</td>
                                                    <td>Consultation</td>
                                                    <td>Drugs</td>
                                                    <td>Services</td>
                                                    <td>Others</td>
                                                    <td>Action</td>
                                                </tr>
                                                <tr id='addr0'>
                                                    <td>1</td>
                                                    <td>
                                                    <div class="form-group">
                                                            <input class="form-control" id="rowInput0" placeholder="Enter Date" type="date">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput1"  placeholder="Enter Pt. File#" type="number">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput2" placeholder="Enter Invoice#" type="number">
                                                        </div>
                                                    </td>  
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput3" placeholder="Enter Consultation" type="number">
                                                        </div>
                                                    </td>    
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput4" placeholder="Enter Drugs" type="number">
                                                        </div>
                                                    </td>    
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput5" placeholder="Enter Services" type="number">
                                                        </div>
                                                    </td>    
                                                    <td>
                                                        <div class="form-group">
                                                            <input class="form-control" id="rowInput6" placeholder="Enter Others" type="number">
                                                        </div>
                                                    </td>                                     
                                                    <td>
                                                        <a id='delete_row' class="pull-right waves-effect waves-light" style="color: red;" onclick="return confirm('Do you really want to delete?');"><i class="bx bx-trash-alt font-size-24" title="Delete Row"></i></a>
                                                    </td>
                                                </tr>
                                                <tr id='row1'></tr>
                                            </tbody>
                                            </form> 
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9">
                                                        <a id="add_row" class="btn btn-primary pull-left waves-effect waves-light">+ Add Row</a>
                                                        &nbsp;&nbsp;
                                                        <a id="save_btn" class="btn btn-primary pull-left waves-effect waves-light">Save</a>
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

        $(document).ready(function(){
            var i=1;
            $("#add_row").click(function(){
                html = '';
                html += '<td>' + (i+1) + '</td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput0" name="rowInput0" placeholder="Enter Date" type="date"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput1" name="rowInput1" placeholder="Enter Pt. File#" type="number"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput2" name="rowInput2" placeholder="Enter Invoice#" type="number"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput3" name="rowInput3" placeholder="Enter Consultation" type="number"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput4" name="rowInput4" placeholder="Enter Drugs" type="number"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput5" name="rowInput5" placeholder="Enter Services" type="number"></div></td>';
                html += '<td><div class="form-group"><input class="form-control" id="rowInput6" name="rowInput6" placeholder="Enter Other" type="number"></div></td>';
                html += '<td><a id="delete_row" data-delete=' + (i+1) + ' class="pull-right waves-effect waves-light" style="color: red;"  onclick="return confirm(\'Do you really want to delete?\');"><i class="bx bx-trash-alt font-size-24" title="Delete Row"></i></a></td>';               
                //$('#addr'+i).html("<td>"+ (i+1) +"</td><td><input name='name"+i+"' type='text' placeholder='Name' class='form-control input-md'  /> </td><td><input  name='mail"+i+"' type='text' placeholder='Mail'  class='form-control input-md'></td><td><input  name='mobile"+i+"' type='text' placeholder='Mobile'  class='form-control input-md'></td>");
                $('#row'+i).html(html);
                $('#table_claim').append('<tr id="row'+(i+1)+'"></tr>');
                i++; 
            });

            $("#delete_row").click(function(){
                if(i>1){
                $("#row"+(i-1)).html('');
                i--;
                }
            });

        });


    </script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
