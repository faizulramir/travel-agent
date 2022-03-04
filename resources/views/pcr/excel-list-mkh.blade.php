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
                        @if (Session::has('success'))
                            <div class="alert alert-success text-center alert-dismissible" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif
                        @if (Session::has('error'))
                            <div class="alert alert-warning text-center alert-dismissible" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <p>{{ Session::get('error') }}</p>
                            </div>
                        @endif                         
                        <div class="col-md-6"></div>
                        <div class="col-md-3"></div>                        
                        <div class="col-md-3" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">Refresh</button>
                        </div>
                    </div>
                    <br>

                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0" width="5%">#</th>
                                    <th data-priority="1" width="15%">Travel Agent</th>
                                    <th data-priority="1" width="25%">Filename</th>
                                    <th data-priority="1" width="5%">Jemaah</th>
                                    <th data-priority="1" width="10%">Submission</th>
                                    <th data-priority="1" width="5%">Status</th>
                                    <th data-priority="3" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uploads as $i => $upload)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ strtoupper($upload->user->name) }}</td>
                                        <td>{{ $upload->file_name }}</td>
                                        <td>
                                            @php
                                                $applicant = count(\App\Models\Order::where([['file_id', $upload->id], ['status', '=', '1']])->get());
                                            @endphp
                                            {{$applicant}}
                                        </td>                                        
                                        <td>{{ $upload->submit_date ? date('d-m-Y H:i:s', strtotime($upload->submit_date)) : '' }}</td>
                                        <td>
                                            @if ($upload->status == '0' || $upload->status == '2')
                                                Pending AKC Approval
                                            @elseif ($upload->status == '2.1')
                                                <p>Pending AKC (Invoice) </p>
                                            @elseif ($upload->status == '3')
                                                Pending Payment
                                            @elseif ($upload->status == '4')
                                                Pending AKC (Payment) Endorsement
                                            @elseif ($upload->status == '5')
                                                COMPLETED
                                            @elseif ($upload->status == '99')
                                                REJECTED
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('excel_detail_mkh', $upload->id) }}" class="waves-effect" style="color:#ed2994;">
                                                <i class="bx bxs-collection font-size-24" title="Show Detail"></i>
                                            </a>
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

        $('#refreshBtn').click(function() {
            $('#datatable').DataTable().state.clear();
            location.reload();
        });

        //enabling datatable filters
        $(document).ready(function() {
            $('#datatable').DataTable( {
                saveState: true,
                initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        if (column[0]==1 || column[0]==4 || column[0]==7) {
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
