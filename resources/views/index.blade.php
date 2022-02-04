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

    <a href="{{ route('excel_list_admin') }}">
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    {{-- <h4 class="card-title mb-4"></h4> --}}
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-muted fw-medium">Travel Agent Request</p>
                                        <h4 class="mb-0">{{ $tra_uploads }}</h4>
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
                                        <p class="text-muted fw-medium">DIY Agent Request</p>
                                        <h4 class="mb-0">{{ $agent_uploads }}</h4>
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
                                        <p class="text-muted fw-medium">DIY Individu Request</p>
                                        <h4 class="mb-0">{{ $diy_uploads }}</h4>
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
                                        <p class="text-muted fw-medium">All Request</p>
                                        <h4 class="mb-0">{{count($total_uploads)}}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </a>


    @if (auth()->user()->hasAnyRole('akc', 'fin'))
    <div class="row">
        <div class="col-md-12">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <p class="text-muted fw-medium">Search Record</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="search_by">Search By</label>
                                    <select id="search_by" name="search_by" class="form-control select2-search-disable" required>
                                        <option value="name">Traveller Name</option>
                                        <option value="passport">Traveller Passport</option>
                                        <option value="agent_name">Travel Agent Name</option>
                                        <option value="ecert">E-Cert Number</option>
                                        <option value="invoice">Invoice Number</option>
                                        <option value="ic">IC Number</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="plan">Search Text</label>
                                    <input type="text" class="form-control" id="search_val" name="search_val" placeholder="Enter search text">
                                </div>
                                <div class="col-md-4">
                                    <label for="plan">&nbsp;</label>
                                    <br>
                                    <button class="btn btn-primary waves-effect waves-light col-md-4" type="button" title="Search Record" id="searchDash">Search</button>
                                </div>                                
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
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th data-priority="1">Name</th>
                                        <th data-priority="3">Passport No.</th>
                                        <th data-priority="1">IC No.</th>
                                        <th data-priority="1">Dep. Date</th>
                                        <th data-priority="3">Return Date</th>
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
    @else
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>PLEASE CONTACT ADMIN</h1>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script src="http://www.datejs.com/build/date.js" type="text/javascript"></script>
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
                
                $.ajax({
                    url: '/search_dashboard',
                    type: 'POST',
                    data: form_data,
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
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
                });
            });
        });
        
        
        
        
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>

@endsection
