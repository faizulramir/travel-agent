@extends('layouts.master')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') TRAVEL AGENT @endslot
        @slot('title') EXCEL DETAIL @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6" style="text-align: left;">
                            <a href="{{ route('excel_list') }}" class="btn btn-primary w-md">
                                <i class="bx bx-chevrons-left font-size-20" title="Back"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6 text-right">
                            <h4 class="card-title">Supporting Documents: {{ $uploads->supp_doc ? $uploads->supp_doc === '1' ? 'UPLOADED' : 'Not Uploaded' :  'Not Uploaded' }}</h4>
                            <h4 class="card-title">Payment: {{ $payment ? 'PAID' : '-' }}</h4>
                        </div>
                        <div class="col-md-6" style="text-align: right; display: {{ count($check) != 0 ? 'block' : 'none' }}">
                            <button type="submit" class="btn btn-primary w-md" id="download_cert">Download All Cert</button>
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
                                    <th data-priority="1">DEP Date</th>
                                    <th data-priority="3">RTN Date</th>
                                    <th data-priority="3">Action</th>
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
                                        <td>
                                            @if (!$payment)
                                                @if ($order->status == '1')
                                                    <a href="{{ route('update_detail_ta', [$order->id, '0'])}}" onclick="return confirm('Confirm to DISABLE Traveller?');" class="waves-effect" style="color: red;">
                                                        <i class="bx bx-trash-alt font-size-20" title="Disable Traveller"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('update_detail_ta', [$order->id, '1'])}}" onclick="return confirm('Confirm to ENABLE Traveller?');" class="waves-effect" style="color: green;">
                                                        <i class="bx bx-paper-plane font-size-20" title="Enable Traveller"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($order->status == '1' && $payment && $order->upload->status == '5')
                                                <a href="{{ route('create_invoice_ind', $order->id) }}" class="waves-effect" style="color: blue;" target="_blank">
                                                    <i class="bx bxs-printer font-size-20" title="Print Invoice"></i>
                                                </a>
                                                <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                    <i class="bx bx-food-menu font-size-20" title="Print E-Cert"></i>
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
