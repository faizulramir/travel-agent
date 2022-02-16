@extends('layouts.master')

@section('title') EXCEL LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') EXCEL LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">
                                Refresh
                                <!--<i class="bx bx-loader-circle font-size-24" title="Refresh"></i>-->
                            </button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0">#</th>
                                    <th data-priority="1">Requester</th>
                                    <th data-priority="1">Filename</th>
                                    {{--<th data-priority="3">Upload Date</th>--}}
                                    <th data-priority="1">Submission Date</th>
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
                                        <td>{{ $upload->submit_date ? date('d-m-Y H:i:s', strtotime($upload->submit_date)) : '' }}</td>
                                        <td>{{ $upload->status == '5' || $upload->status == '4' ? 'PAID' : 'UNPAID' }}</td>
                                        <td>
                                            @if ($upload->status == '4')
                                                Pending (Payment) Endorsement
                                            @elseif ($upload->status == '5')
                                                ENDORSED
                                            @elseif ($upload->status == '2.1')
                                                Pending (Invoice) Endorsement
                                            @endif
                                        </td>
                                        <td>

                                            {{--<a href="{{ route('upload_detail', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                            </a>--}}

                                            <a href="{{ route('excel_detail_finance', $upload->id) }}" class="waves-effect" style="color: #ed2994;">
                                                    <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                            </a>

                                            @if ($upload->status == '5')
                                                <a href="{{ route('create_invoice', $upload->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                    <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('payment_detail', $upload->id) }}" class="waves-effect" style="color: green;">
                                                <i class="bx bx-money font-size-24" title="Payment Detail"></i>
                                            </a>

                                            {{-- @if ($upload->payment)
                                                <a href="{{ route('download_payment', [$upload->user_id, $upload->id]) }}" class="waves-effect" style="color: green;">
                                                    <i class="bx bx-food-menu font-size-24" title="Show Payslip/Receipt"></i>
                                                </a>
                                            @endif --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th data-priority="0"></th>
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
                                    {{--<th data-priority="3">Upload Date</th>--}}
                                    <th data-priority="1"></th>
                                    <th data-priority="1"></th>
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

        function approved(e)
        {
            if(!confirm('Are you sure to Approve?')) {
                e.preventDefault();
            }
        }

        function reject(e)
        {
            if(!confirm('Are you sure to Reject?')) {
                e.preventDefault();
            }
        }

        //enabling datatable filters
        $(document).ready(function() {
            $('#datatable').DataTable( {
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if (column[0]==1 || column[0]==2 || column[0]==3 || column[0]==5) {
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
