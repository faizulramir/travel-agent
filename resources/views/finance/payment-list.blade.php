@extends('layouts.master')

@section('title') PAYMENT FILE LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') FINANCE @endslot
        @slot('title') PAYMENT FILE LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session()->has('error'))
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label for="error" style="color: red;">{{ session()->get('error') }}</label>
                            </div>
                        </div>
                    @endif
                    @if(session()->has('success'))
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label for="success" style="color: green;">{{ session()->get('success') }}</label>
                            </div>
                        </div>
                    @endif
                    <table id="datatable" class="table table-bordered dt-responsive w-100">
                        <thead>
                            <tr>
                                <th data-priority="0" width="5%">#</th>
                                <th data-priority="1" width="50%">Filename</th>
                                <th data-priority="2" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files_arr as $i => $file)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $file }}</td>
                                    <td>
                                        <a class="btn btn-success btn-md" target="_blank" href="{{ route('supp_doc_download_admin', [$id, $i]) }}" type="submit">Download</a>
                                        <a class="btn btn-danger btn-md" href="{{ route('supp_doc_download_admin', [$id, $i.'-delete']) }}" type="submit">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 

@endsection
@section('script')
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
@endsection
