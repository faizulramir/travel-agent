@extends('layouts.master')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6" style="text-align: left;">
                            <a href="{{ route('excel_list_finance') }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4 text-right">
                            <h4 class="card-title">Supporting Documents: {{ $uploads->supp_doc ? $uploads->supp_doc === '1' ? 'UPLOADED' : 'Not Uploaded' :  'Not Uploaded' }}</h4>
                            <h4 class="card-title">Payment: {{ $payment ? 'PAID' : '-' }}</h4>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
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
                                                @if ($order->plan_type != 'NO')
                                                    <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                        <i class="bx bx-food-menu font-size-24" title="Print ECert"></i>
                                                    </a>
                                                @endif                                                
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
