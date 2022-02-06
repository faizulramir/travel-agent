@extends('layouts.master-without-nav')

@section('title') EXCEL DETAIL @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="background-color:#d4daf9;">
                            <div style="height:200px;width:100%;">
                                <div style="float:left;">
                                    <img src="{{ URL::asset('/assets/images/wanita2.png') }}" height="200" alt="" class="auth-logo-dark" style="padding:10px;">
                                </div>
                                <div style="float:left;">
                                    <br><br>
                                    <a href="https://alkhairicare.com/" target="_blank">
                                        <img src="{{ URL::asset('/assets/images/akc.png') }}" width="100%" alt="" height="50" class="auth-logo-dark">
                                    </a>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <br>
                            <div class="row">
                                <form method="POST" action="{{ route('post_cert_public') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="passport">Passport No</label>
                                        <input class="form-control input-md" type="text" name="passport" id="passport" placeholder="Enter Passport Number" value="{{ old('passport') }}"/>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="depart_date">Departure Date</label>
                                        <input class="form-control input-md" type="date" name="depart_date" id="depart_date" placeholder="Enter Departure Date (dd/mm/yyyy)" value="{{ old('depart_date') }}"/>
                                    </div>
                                    <br>
                                    @if(session()->has('error'))
                                        <div class="row">
                                            <div class="col-md-12">
                                                <hr/>
                                                <label for="depart_date">Result: {{ session()->get('error') }}</label>
                                            </div>
                                        </div>
                                    @endif
                                    @if(session()->has('ecert'))
                                        <div class="row">
                                            <div class="col-md-12">
                                                <hr/>
                                                <h4>Result for {{ session()->get('passport') }}</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="ecert_no" style="font-size:1.05rem;">Traveller: {{ session()->get('name') }}</label><br>
                                                <label for="ecert_no" style="font-size:1.05rem;">ECert No: <a href="{{ route('download_cert_public', session()->get('order_id')) }}" title="Download ECert">{{ session()->get('ecert') }} <i class="bx bx-cloud-download font-size-24"></i></a></label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group text-center">
                                        <input type="submit" name="submit" class="btn btn-success btn-md" value="Search" />
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <script>document.write(new Date().getFullYear())</script> Copyright © AL-KHAIRI CARE | All rights reserved.
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-4"></div>
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
