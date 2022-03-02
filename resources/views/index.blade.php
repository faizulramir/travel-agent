@extends('layouts.master')

@section('title') @lang('translation.Dashboards') @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .dataTables_filter, .dataTables_info { display: none; }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') DASHBOARD @endslot
        @slot('title') DASHBOARD @endslot
    @endcomponent


    @if (auth()->user()->hasAnyRole('tra', 'ag', 'ind'))
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
            <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Pending Submission</p>
                                    <h4 class="mb-0">{{ $cust_pen_sub }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Pending Supporting Documents</p>
                                    <h4 class="mb-0" style="color:orange;">{{ $cust_pen_doc }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Pending Payment</p>
                                    <h4 class="mb-0" style="color:orange;">{{ $cust_pen_pay }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Total Excel Submission</p>
                                    <h4 class="mb-0">{{ $cust_tot_upl }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    @endif    

    @if (auth()->user()->hasAnyRole('akc', 'fin'))
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    {{-- <h4 class="card-title mb-4"></h4> --}}
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">Admin</p>
                                                <h4 class="mb-0">{{ $akc_tot_sub }}</h4>
                                                </td>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">Travel Agent Excel</p>
                                                <h4 class="mb-0">{{ $tra_tot_sub }}</h4>
                                                </td>                                                                                      
                                            </tr>
                                        </table>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">DIY Agent Excel</p>
                                                <h4 class="mb-0">{{ $agn_tot_sub }}</h4>
                                                </td>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">DIY Individu Request</p>
                                                <h4 class="mb-0">&nbsp;</h4>
                                                </td>                                                                                      
                                            </tr>
                                        </table>                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">PROCESSED EXCEL</p>
                                                <h4 class="mb-0">{{ $tot_sub }}</h4>
                                                </td>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">TOTAL JEMAAH</p>
                                                <h4 class="mb-0">{{ $tot_jemaah }}</h4>
                                                </td>                                                                                      
                                            </tr>
                                        </table>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">RESCHEDULE</p>
                                                <h4 class="mb-0">{{ $tot_res }}</h4>
                                                </td>                                                  
                                                <td width="50%">
                                                <p class="text-muted fw-medium">CANCELLED/UNBOARDING</p>
                                                <h4 class="mb-0">{{ $tot_can }}</h4>
                                                </td>      
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-muted fw-medium">Pending Invoice Endorsement</p>
                                        <h4 class="mb-0" style="color:orange;">{{ $fin_inv }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-muted fw-medium">Pending Payment Endorsement</p>
                                        <h4 class="mb-0" style="color:orange;">{{ $fin_pay }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="50%">
                                                <p class="text-muted fw-medium">Pending Approval</p>
                                                <h4 class="mb-0" style="color:orange;">{{ $akc_app }}</h4>
                                                </td>                                                  
                                                <td width="50%">
                                                <p class="text-muted fw-medium">Pending Supp. Docs</p>
                                                <h4 class="mb-0" style="color:orange;">{{ $akc_doc }}</h4>
                                                </td>      
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 


                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">LITE</p>
                                                <h4 class="mb-0">{{ $tot_lite }}</h4>
                                                </td>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">BASIC</p>
                                                <h4 class="mb-0">{{ $tot_basic }}</h4>
                                                </td>                                                                                      
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-muted fw-medium">AMOUNT INVOICED</p>
                                        <h4 class="mb-0">RM {{ number_format((float)$amt_inv, 2, '.', ',') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-muted fw-medium">AMOUNT PAID</p>
                                        <h4 class="mb-0">RM {{ number_format((float)$amt_pay, 2, '.', ',') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                    <table width="100%">
                                            <tr>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">PCR</p>
                                                <h4 class="mb-0">{{ $tot_pcr }}</h4>
                                                </td>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">TPA</p>
                                                <h4 class="mb-0">{{ $tot_tpa }}</h4>
                                                </td>                                                                                    
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                      
                    
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <table width="100%">
                                            <tr>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">STANDARD</p>
                                                <h4 class="mb-0">{{ $tot_standard }}</h4>
                                                </td>
                                                <td width="40%">
                                                <p class="text-muted fw-medium">PREMIUM</p>
                                                <h4 class="mb-0">{{ $tot_premium }}</h4>
                                                </td>                                                                                        
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    @endif

    @if (auth()->user()->hasAnyRole('akc', 'fin'))
        <div class="row">
            <div class="col-md-12">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <p class="text-muted fw-medium">Search Record</p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="search_by">Search By</label>
                                        <select id="search_by" name="search_by" class="form-control select2-search-disable" required>
                                            <option value="name">Traveller Name</option>
                                            <option value="passport">Traveller Passport No</option>
                                            <option value="ic">Traveller IC No</option>
                                            <option value="agent_name">Travel Agent Name</option>
                                            <option value="ecert">E-Cert No</option>
                                            <option value="invoice">Invoice No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="plan">Search Text</label>
                                        <input type="text" class="form-control col-md-2" id="search_val" name="search_val" placeholder="Enter search text">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="plan">&nbsp;</label>
                                        <br>
                                        <button class="btn btn-primary waves-effect waves-light" type="button" title="Search Record" id="searchDash">Search</button>
                                    </div> 
                                    <div class="col-md-3"></div>                               
                                </div>
                                <!--
                                <br>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary waves-effect waves-light col-md-12" type="button" id="searchDash">Search</button>
                                    </div>
                                </div>
                                -->
                                <br>
                                <div id="table1">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th data-priority="0">#</th>
                                                <th data-priority="1">Name</th>
                                                <th data-priority="1">Passport No</th>
                                                <th data-priority="1">IC No</th>
                                                <th data-priority="3">DEP Date</th>
                                                <th data-priority="3">RTN Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="table2" style="display: none;">
                                    <table id="datatable2" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th data-priority="1">Filename</th>
                                                <th data-priority="3">Upload Date</th>
                                                <th data-priority="1">Submission Date</th>
                                                <th data-priority="1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{--
    @else
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>PLEASE CONTACT AL KHAIRI ADMIN</h1>
            </div>
        </div>
    --}}
    @endif

    @if (auth()->user()->hasAnyRole('mkh'))
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                {{-- <h4 class="card-title mb-4"></h4> --}}
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">&nbsp;</p>
                                    <h4 class="mb-0">&nbsp;</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">&nbsp;</p>
                                    <h4 class="mb-0">&nbsp;</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">&nbsp;</p>
                                    <h4 class="mb-0">&nbsp;</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">&nbsp;</p>
                                    <h4 class="mb-0">&nbsp;</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif  

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js" type="text/javascript"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#searchDash').click(function() {
                search_by = $('#search_by').val();
                search_val = $('#search_val').val();

                var form_data = new FormData();
                form_data.append("search_by", search_by);
                form_data.append("search_val", search_val);
                
                console.log(form_data);

                $.ajax({
                    url: '/search_dashboard',
                    type: 'POST',
                    data: form_data,
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (search_by == 'agent_name') {
                            $('#table1').hide();
                            $('#table2').show();
                            var table = $('#datatable2').DataTable();
                            table.clear().draw();
                            data.Data.forEach(function callback(e, index) {
                                var format_upload = e.upload_date.split("-");
                                var format_submit = e.submit_date.split("-");
                                upload_date = format_upload[2] + '-' + format_upload[1] + '-' + format_upload[0]
                                submit_date = format_submit[2] + '-' + format_submit[1] + '-' + format_submit[0]

                                var status
                                if (e.status == '0'){
                                    status = 'Pending Submission'
                                } else if (e.status == '2') {
                                    status = 'Pending AKC Approval'
                                } else if (e.status == '2.1') {
                                    status = 'Pending AKC Invoice'
                                } else if (e.status == '3') {
                                    status = 'Pending Payment'
                                } else if (e.status == '4') {
                                    status = 'Pending AKC (Payment) Endorsement'
                                } else if (e.status == '5') {
                                    status = 'COMPLETED'
                                } else if (e.status == '99') {
                                    status = 'REJECTED'
                                }

                                table.row.add( [
                                    index + 1,
                                    e.file_name,
                                    upload_date,
                                    submit_date,
                                    status
                                ]).draw();
                            });
                        } else {
                            $('#table2').hide();
                            $('#table1').show();
                            var table = $('#datatable').DataTable();
                            table.clear().draw();
                            data.Data.forEach(function callback(e, index) {
                                var format_dep = e.dep_date.split("-");
                                var format_return = e.return_date.split("-");
                                dep_date = format_dep[2] + '-' + format_dep[1] + '-' + format_dep[0]
                                return_date = format_return[2] + '-' + format_return[1] + '-' + format_return[0]

                                table.row.add( [
                                    index + 1,
                                    e.name,
                                    e.passport_no,
                                    e.ic_no,
                                    dep_date,
                                    return_date
                                ]).draw();
                            });
                        }
                    }
                });
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

    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
@endsection
