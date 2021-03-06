@extends('layouts.master')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') DETAIL @endslot
        @slot('title') EXCEL DETAIL @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6" style="text-align: left;">
                            @if (auth()->user()->hasAnyRole('tra'))
                                <a href="{{ route('excel_list') }}" class="btn btn-primary w-md" title="Back">
                                    <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                                </a>
                            @elseif (auth()->user()->hasAnyRole('ag'))
                                <a href="{{ route('excel_list_agent') }}" class="btn btn-primary w-md" title="Back">
                                    <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                                </a>
                            @elseif (auth()->user()->hasAnyRole('ind'))
                                <a href="{{ route('application_list') }}" class="btn btn-primary w-md" title="Back">
                                    <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                                </a>
                            @elseif (auth()->user()->hasAnyRole('akc'))
                                <a href="{{ route('excel_list_admin') }}" class="btn btn-primary w-md" title="Back">
                                    <i class="bx bx-chevrons-left font-size-24" title="Back"></i>
                                </a>
                            @endif
                        </div>
                        {{-- <div class="col-md-6 text-right">
                            <h4 class="card-title">Supporting Documents: {{ $uploads->supp_doc ? $uploads->supp_doc === '1' ? 'UPLOADED' : '' :  '' }}</h4>
                            <h4 class="card-title">Payment: {{ $payment ? 'PAID' : '' }}</h4>
                        </div> --}}
                        {{-- <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary w-md" id="download_cert" style="margin-left: 80%; display: {{ count($check) != 0 ? 'block' : 'none' }}">Download All Cert</button>
                        </div> --}}
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0" width="5%">#</th>
                                    <th data-priority="1" width="20%">Name</th>
                                    <th data-priority="1" width="10%">Passport No</th>
                                    <th data-priority="1" width="10%">IC No</th>
                                    <th data-priority="1" width="8%">Birth Date</th>
                                    <th data-priority="1" width="10%">DEP Date</th>
                                    <th data-priority="1" width="10%">RTN Date</th>
                                    <th data-priority="3" width="8%">ECare Plan</th>
                                    <th data-priority="3" width="5%">PCR</th>
                                    <th data-priority="3" width="10%">TPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $bil = 0;
                                @endphp
                                @foreach ($orders as $i => $order)
                                    @php
                                    $bil = $bil + 1;
                                    @endphp
                                    <tr>
                                        <td>{{ $bil }}</td>
                                        <td>{{ $order[1] ? strtoupper($order[1]) : $order[1] }}</td>
                                        <td>{{ $order[2] ? strtoupper($order[2]) : $order[2] }}</td>
                                        <td>{{ $order[3] }}</td>
                                        <td>
                                            
                                            @php
                                            $temp = $order[4];
                                            try {
                                                $temp = date('d-m-Y', $order[4]);
                                            }
                                            catch (\Exception $ex) {}
                                            @endphp
                                            {{ $temp }}
                                        
                                            {{-- {{ $order[4] ? date('d-m-Y', $order[4]) : '' }} --}}
                                        
                                        </td>
                                        <td>
                                            {{ $order[9] ? date('d-m-Y', $order[9]) : '' }}
                                        </td>
                                        <td>
                                            {{ $order[10] ? date('d-m-Y', $order[10]): '' }} 
                                        </td>
                                        <td>{{ $order[7] ? strtoupper($order[7]) : $order[7] }}</td>
                                        <td>{{ $order[11] ? strtoupper($order[11]) : $order[11] }}</td>
                                        <td>{{ $order[12] ? strtoupper($order[12]) : $order[12] }}</td>
                                        {{-- <td>
                                            @if (!$payment)
                                                @if ($order->status == '1')
                                                    <a href="{{ route('update_detail_ta', [$order->id, '0'])}}" onclick="return confirm('Do you really want to disable?');" class="waves-effect" style="color: red;">
                                                        <i class="bx bx-trash-alt font-size-24" title="Disable"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('update_detail_ta', [$order->id, '1'])}}" onclick="return confirm('Do you really want to disable?');" class="waves-effect" style="color: green;">
                                                        <i class="bx bx-paper-plane font-size-24" title="Enable"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($order->status == '1' && $payment && $order->upload->status == '5')
                                                <a href="{{ route('create_invoice_ind', $order->id) }}" class="waves-effect" style="color: blue;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                                <a href="{{ route('create_cert_ind', $order->id) }}" class="waves-effect" style="color: green;" target="_blank">
                                                    <i class="bx bx-food-menu font-size-24" title="Print E-Cert"></i>
                                                </a>
                                            @endif
                                        </td> --}}
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
