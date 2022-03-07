@extends('layouts.master')

@section('title') INVOICE @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .form-inline .form-control {
            /*width: 30px;*/
            /*display:table-cell;*/
        }
        .form-group{
            /*display:table-cell;*/
        }
        .data-jemaah {
            font-size:1.10rem;
        }
        .hidden {
            display:none;
        }

        textarea.form-control {
            /*min-height: calc(1.5em + 0.94rem + 2px);*/
            min-width: calc(25vw + 50px);
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') CREATE INVOICE @endslot
    @endcomponent

    <div class="modal fade bs-example-modal-center" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Loading Data</h5>
                </div>
                <div class="modal-body text-center">
                    <button class="btn btn-primary" id="btnBefore" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form name="form1" id="form1" autocomplete="off">
                        @csrf
                        <h4 class="card-title">Invoice Information</h4>
                        <br>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-label">Invoice No</label>
                                    <input class="form-control" type="text" name="inv_no" value="" placeholder="Enter Invoice Number" required />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="plan">Invoice For (Bill To)</label>
                                    <input class="form-control" type="text" name="inv_company" value="" placeholder="Enter Invoice For (ie. Company Name)" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="plan">Invoice Date</label>
                                    <input class="form-control" type="date" name="inv_date" value="" placeholder="Enter Invoice Date" required>
                                </div>
                            </div>                            
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="plan">Invoice Status</label>
                                    <input class="form-control" type="text" name="inv_status" value="" placeholder="Enter Status (ie. PAID, UNPAID, DRAFT)" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">                         
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="plan">Invoice Remarks</label>
                                    <input class="form-control" type="text" name="inv_remark" value="" placeholder="Enter Remarks (ie. Reason for invoicing)" required>
                                </div>
                            </div>
                        </div> 
                        <br>                       

                        <hr/>
                        <h4 class="card-title">Invoice Entries</h4>
                        <br>

                        <div class="row">
                            <div class="col-12">
                                <table id="table_invoice" class="table table-striped table-advance table-hover w-100">
                                    <thead>
                                        <tr>
                                            <td width="10%">Quantity</td>
                                            <td >Description</td>
                                            <td width="15%">Unit Price (RM)</td>
                                            <td width="20%">Amount (RM)</td>
                                            <td width="5%">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" id="include">
                                                <label class="form-check-label" for="agreement">
                                                &nbsp;&nbsp;<b>Include Total</b>
                                                </label>
                                            </td>
                                            <td><div class="form-group"><input class="form-control" name="inv_total" placeholder="Total Amount" type="number"></div></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <a id="addBtn" class="btn btn-primary pull-left waves-effect waves-light">+ Add Entry</a>
                                                &nbsp;&nbsp;
                                                <!--
                                                <a id="save_btn2" class="btn btn-primary pull-left waves-effect waves-light" target="_blank" type="submit">Save</a>
                                                &nbsp;&nbsp;
                                                -->
                                                <button type="submit" class="btn btn-primary waves-effect waves-light w-md" id="preview_btn">Preview Invoice</button>
                                                &nbsp;&nbsp;

                                                <button type="submit" class="btn btn-primary waves-effect waves-light w-md" id="save_btn">Save Invoice</button>
                                                &nbsp;&nbsp;

                                                &nbsp;&nbsp;
                                                <span id="change_alert" class="alert hidden" style="color:red;">Changes was made.</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>  
                            </div>
                        </div>  
                    </form>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/jquery-validation/jquery-validation.min.js')}}"></script>

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

            $('#pleaseWaitDialog').modal({
                backdrop: 'static',
                keyboard: false
            });

            $(document).on('change', 'input', function() {
                //console.log("Change: Editing... ");
                $('#change_alert').removeClass('hidden');
            });
            $(document).on('keyup', 'input', function() {
                //console.log("Keyup: Editing... ");
                $('#change_alert').removeClass('hidden');
            });

            /*
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
            */

            $('#addBtn').on('click', function () {

                $("#form1").validate();        
                if ($('#form1').valid()) {}
                else {
                    alert("Data entry is not complete!");
                    return false;
                }

                $('#tbody').append(`<tr id="R${++rowIdx}">
                    <td><div class="form-group"><input class="form-control" name="rowInput1" placeholder="Qty" type="text" value=""></div></td>
                    <td><div class="form-group"><textarea class="form-control" style="width:100%" name="rowInput2" placeholder="Enter Description" type="text" value="" rows="3"></textarea></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput3" placeholder="Unit Price" type="number"></div></td>
                    <td><div class="form-group"><input class="form-control" name="rowInput4" placeholder="Amount" type="number"></div></td>
                    <td><a class="pull-right waves-effect waves-light remove" style="color: red;" type="button"><i class="bx bx-trash-alt font-size-24" title="Delete Entry"></i></a></td>
                    </tr>`);
            });
            
            $('#tbody').on('click', '.remove', function () {
                if (!confirm('Confirm to delete this entry ?')) {
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


            $("#preview_btn").click(function(){
                //$('#change_alert').removeClass('hidden').addClass('hidden');
                //const dataAll = new Array();

                $("#form1").validate();        
                if ($('#form1').valid()) {
                    //alert("Validation Error!");
                    if (rowIdx<1) {
                        alert("Invoice entries cannot be empty!");
                        return false;
                    }
                }
                else {
                    alert("Data entry is not complete!");
                    return false;
                }

                $('#pleaseWaitDialog').modal('show');

                //createTimeOut("$('#pleaseWaitDialog').modal('hide')");

                var master = {};    //init json
                params1 = $('#form1').serializeArray();
                $.each(params1, function(i, val) {
                    if (val.name.toLowerCase().startsWith("row")) {}
                    else {
                        //only take master inputs - ignore entry rows
                        master[val.name] = val.value;
                    }
                });
                master["inv_showtotal"] = false;
                master["entries"] = [];
                //console.log("master: ", master);

                console.log("checkbox: ", $('#include').prop("checked"));
                if ($('#include').prop("checked")) {
                    master["inv_showtotal"] = true;
                }

                let detail_arr = [];    //init array
                for (let index = 1; index <= rowIdx; index++) {
                    let detail = {};    //init json
                    //get all input fields
                    $('#R' + index + '> td').find("input").each(function() {
                        //console.log("detail1:", index, this.name, this.value);
                        detail[this.name] = this.value;
                    });
                    //get all textarea fields
                    $('#R' + index + '> td').find("textarea").each(function() {
                        //console.log("detail2:", index, this.name, this.value);
                        detail[this.name] = this.value;
                    });
                    detail_arr.push(detail);
                }

                //console.log("detail_arr: ", detail_arr);
                master["entries"] = detail_arr;
                console.log("master: ", master);

                //return false;
             
                $.ajax({
                    url: '/preview_invoice/man',
                    type: 'POST',
                    data: {
                        jsonData: JSON.stringify(master),
                    },
                    xhr: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        $('#pleaseWaitDialog').modal('hide');

                            if (xhr.readyState == 2) {
                                if (xhr.status == 200) {
                                    xhr.responseType = "blob";
                                } else {
                                    xhr.responseType = "text";
                                }
                            }
                        };
                        return xhr;
                    },
                    success: function (data) {
                        $('#pleaseWaitDialog').modal('hide');

                        // var blob=new Blob([data], { type: "application/pdf", filename: "preview-invoice.pdf" });
                        // var link=document.createElement('a');
                        // link.href=window.URL.createObjectURL(blob);
                        // link.target="_blank";
                        // //link.download="invoice.pdf";
                        // console.log("link: ", link);
                        // link.click();
                        var blob = new Blob([data], {type: 'application/pdf'});
                        var blobURL = URL.createObjectURL(blob);
                        window.open(blobURL, "preview-invoice");

                        return false;
                    }
                });

                return false;
               
            });            

            $("#save_btn").click(function(){
                $('#change_alert').removeClass('hidden').addClass('hidden');
                const dataAll = new Array();

                $("#form1").validate();        
                if ($('#form1').valid()) {
                    //alert("Validation Error!");
                    if (rowIdx<1) {
                        alert("Invoice entries cannot be empty!");
                        return false;
                    }
                }
                else {
                    alert("Data entry is not complete!");
                    return false;
                }

                var master = {};    //init json
                params1 = $('#form1').serializeArray();
                $.each(params1, function(i, val) {
                    if (val.name.toLowerCase().startsWith("row")) {}
                    else {
                        //only take master inputs - ignore entry rows
                        master[val.name] = val.value;
                    }
                });
                master["inv_showtotal"] = false;
                master["entries"] = [];
                //console.log("master: ", master);

                console.log("checkbox: ", $('#include').prop("checked"));
                if ($('#include').prop("checked")) {
                    master["inv_showtotal"] = true;
                }

                let detail_arr = [];    //init array
                for (let index = 1; index <= rowIdx; index++) {
                    let detail = {};    //init json
                    //get all input fields
                    $('#R' + index + '> td').find("input").each(function() {
                        //console.log("detail1:", index, this.name, this.value);
                        detail[this.name] = this.value;
                    });
                    //get all textarea fields
                    $('#R' + index + '> td').find("textarea").each(function() {
                        //console.log("detail2:", index, this.name, this.value);
                        detail[this.name] = this.value;
                    });
                    detail_arr.push(detail);
                }

                //console.log("detail_arr: ", detail_arr);
                master["entries"] = detail_arr;
                console.log("master: ", master);

                $.ajax({
                    url: '/invoice_save',
                    type: 'POST',
                    data: {
                        jsonData: JSON.stringify(master),
                    },
                    success: function (data) {
                        alert(data.Data);
                        location.reload();
                    }
                });                

                return false;
            });

        });
        
    </script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
