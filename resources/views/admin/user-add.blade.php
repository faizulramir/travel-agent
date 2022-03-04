@extends('layouts.master')

@section('title') USER @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') USER @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6" style="text-align: left;">
                        <a href="{{ route('user_list') }}" class="btn btn-primary w-md">
                            <i class="bx bx-chevrons-left font-size-20" title="Back"></i>
                        </a>
                    </div>
                    <br>
                    <form action="{{ route('user_add_post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">User Information</h4>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Email (will be used as Login ID)</label>
                                    <input class="form-control" type="email" name="email" value="" placeholder="Enter Email" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">User Name</label>
                                    <input class="form-control" type="text" name="name" value="" placeholder="Enter Username" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Password</label>
                                    <input class="form-control" type="password" name="password" value="" placeholder="Enter Password" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Date Of Birth</label>
                                    <input class="form-control" type="date" name="dob" value="" placeholder="Enter DOB" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Company Name</label>
                                    <input class="form-control" type="text" name="company_name" value="" placeholder="Enter Company Name">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Confirm Password</label>
                                    <input class="form-control" type="password" name="password_confirmation" value="" placeholder="Confirm Password" required>
                                </div>
                            </div>                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4">
                                <div>
                                    <label class="form-label">Contact Number</label>
                                    <input class="form-control" type="number" name="phone" value="" placeholder="Enter Contact Number" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Company Location</label>
                                    <input class="form-control" type="text" name="company_location" value="" placeholder="Enter Company Location">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="plan">Role</label>
                                    <select id="role" name="role" class="form-control select2-search-disable" required>
                                        <option value="" selected>Please Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">
                                                @if ($role->name == 'tra')
                                                    Travel Agent   
                                                @elseif ($role->name == 'ag')
                                                    DIY Agent                                                                                                 
                                                @elseif ($role->name == 'ind')
                                                    DIY Individu
                                                @elseif ($role->name == 'fin')
                                                    AKC Finance
                                                @elseif ($role->name == 'mkh')
                                                    AKC Makkah                                                    
                                                @elseif ($role->name == 'disabled')
                                                    Disabled
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                            
                        </div>
                        <br>
                        <div class="row">

                            <div class="col-lg-4" id="ssm_no" style="display: none;">
                                <div>
                                    <label for="plan">SSM Number</label>
                                    <input class="form-control" type="text" id="ssm_no_input" name="ssm_no" value="" placeholder="Enter SSM Number">
                                </div>
                            </div>
                            <div class="col-lg-4" id="ssm_cert" style="display: none;">
                                <div>
                                    <label for="plan">SSM Cert.</label>
                                    <input type="file" class="form-control" id="ssm_cert_input" name="ssm_cert">
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                            <p style="color:red;">{{$errors->first()}}</p>
                        @endif
                        <br>
                        <div class="col-lg-12">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')

    <script>
        $('#role').change(function() {
            if ($('#role').val() == 4 || $('#role').val() == '4' ) {
                $('#ssm_no').show();
                $('#ssm_cert').show();
                $("#ssm_no_input").prop('required',true);
                $("#ssm_cert_input").prop('required',true);
            } else {
                $('#ssm_no').hide();
                $('#ssm_cert').hide();
                $("#ssm_no_input").prop('required',false);
                $("#ssm_cert_input").prop('required',false);
            }
        });
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
