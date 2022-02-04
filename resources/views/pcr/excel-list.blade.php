@extends('layouts.master')

@section('title') EXCEL LIST @endsection

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
        @slot('li_1') PCR @endslot
        @slot('title') EXCEL LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Filename</th>
                                    <th data-priority="3">Upload Date</th>
                                    <th data-priority="1">Submission Date</th>
                                    <th data-priority="1">Status</th>
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($temp_file as $i => $file)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->upload_date ? date('d-m-Y', strtotime($file->upload_date)) : '' }}</td>
                                        <td>{{ $file->submit_date ? date('d-m-Y', strtotime($file->submit_date)) : '' }}</td>
                                        <td>
                                            @if ($file->status == '0')
                                                Pending Submission
                                            @elseif ($file->status == '2')
                                                <p>Pending AKC Approval</p>
                                            @elseif ($file->status == '3')
                                                Pending Payment
                                            @elseif ($file->status == '4')
                                                <p>Pending AKC (Payment) Endorsement</p>
                                            @elseif ($file->status == '5')
                                                COMPLETED
                                            @elseif ($file->status == '99')
                                                REJECTED
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('excel_detail_pcr', $file->id) }}" class="waves-effect" style="color:#ed2994;">
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
