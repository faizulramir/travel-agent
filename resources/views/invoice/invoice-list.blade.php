@extends('layouts.master')

@section('title') INVOICE LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .action_icon {
            font-size: 1.55rem;
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') INVOICE LIST @endslot
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
                        <div class="col-md-6">
                            <a href="{{ route('invoice_add') }}" class="btn btn-primary w-md" >Create Invoice</a>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <button type="button" class="btn btn-primary w-md" id="refreshBtn" title="Refresh display">Refresh</button>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th data-priority="0" width="5%">#</th>
                                    <th data-priority="1" width="8%">Date</th>
                                    <th data-priority="1" width="8%">Invoice No</th>
                                    <th data-priority="1" width="15%">Invoice To</th>
                                    <th data-priority="1" width="30%">Remarks</th>
                                    <th data-priority="1" width="10%">Status</th>
                                    <th data-priority="3" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $i => $file)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ strtoupper($file->inv_date) }}</td>
                                        <td>{{ strtoupper($file->inv_no) }}</td>
                                        <td>{{ strtoupper($file->inv_company) }}</td>
                                        <td>{{ strtoupper($file->inv_remark) }}</td>
                                        <td>{{ $file->status == '1' ? 'Endorsed' : 'Draft' }}</td>
                                        <td>
                                            <a href="{{ route('print_invoice', $file->id) }}" class="waves-effect" style="color: black;" target="_blank">
                                                <i class="bx bxs-printer font-size-24" title="Print Invoice"></i>
                                            </a>
                                            <a href="{{ route('invoice_edit', $file->id) }}" class="waves-effect" style="color: black;">
                                                <i class="bx bx-edit-alt font-size-24" title="Edit Invoice"></i>
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

        $('#refreshBtn').click(function() {
            $('#datatable').DataTable().state.clear();
            location.reload();
        });

        $(document).ready(function() {
            $('#datatable').dataTable({
                stateSave: true,
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
@endsection
