@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Register')
@endsection

@section('css')
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        .img2 {
            margin-top:10%;
        }
    </style>
@endsection

@section('body')

    <body class="auth-body-bg">
    @endsection

    @section('content')

        <div>
            <div class="container-fluid p-0">
                <div class="row g-0">

                    <div class="col-xl-9">
                        <div class="auth-full-bg pt-lg-5 p-4">
                            <div class="w-100">
                                <div class="bg-overlay">

                                    <div class="img2">
                                        <div style="float:left;margin-left:20px;">
                                            <img src="{{ URL::asset('/assets/images/wanita2.png') }}" alt="" class="auth-logo-dark">
                                        </div>
                                        <div style="float:left;margin-top:10%;color:#0f3e84;">
                                            <span style="font-size:2.05rem; font-weight:300;">Just care on your spiritual journey,<span><br>
                                            <span style="font-size:3.85rem; font-weight:900;margin-top:-30px;">Let us care about you.</span>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>

                                </div>
                                <div class="d-flex h-100 flex-column">

                                    <div class="p-4 mt-auto">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-7">
                                                <div class="text-center">
                                                    <span class="logo-lg">
                                                        {{-- <img src="{{ URL::asset ('/assets/images/myori.png') }}" alt="" height="100"> --}}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-3">
                        <div class="auth-full-page-content p-md-5 p-4">
                            <div class="w-100">

                                <div class="d-flex flex-column h-100">
                                    <div class="my-auto">
                                        <div class="mb-4 mb-md-5">
                                                <img src="{{ URL::asset('/assets/images/akc.png') }}" alt="" height="50" class="auth-logo-dark">
                                        </div>
                                        <div>
                                            <h5 class="text-primary">Register Account</h5>
                                        </div>

                                        <div class="mt-4">
                                            <form method="POST" class="form-horizontal" action="{{ route('register') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Email</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="useremail"
                                                    value="{{ old('email') }}" name="email" placeholder="Enter email" autofocus required>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
        
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">User Name</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}" id="username" name="name" autofocus required
                                                        placeholder="Enter username">
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Contact No</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                    value="{{ old('name') }}" id="phone" name="phone" autofocus required placeholder="Enter Contact Number">
                                                    @error('phone')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                {{--
                                                <div class="mb-3">
                                                    <label for="userdob">Date of Birth</label>
                                                    <div class="input-group" id="datepicker1">
                                                        <input type="text" class="form-control @error('dob') is-invalid @enderror" placeholder="dd-mm-yyyy"
                                                            data-date-format="dd-mm-yyyy" data-date-container='#datepicker1' data-date-end-date="0d" value="{{ old('dob') }}"
                                                            data-provide="datepicker" name="dob" autofocus required>
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                        @error('dob')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>   
                                                --}}                                             
        
                                                <div class="mb-3">
                                                    <label for="userpassword" class="form-label">Password</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="userpassword" name="password"
                                                        placeholder="Enter password" autofocus required>
                                                        @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
        
                                                <div class="mb-3">
                                                    <label for="confirmpassword" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="confirmpassword"
                                                    name="password_confirmation" placeholder="Enter Confirm password" autofocus required>
                                                    @error('password_confirmation')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
        
                                                <div class="mb-3">
                                                    <label for="role">Please Choose Registration Type</label>
                                                    <select id="role" name="role" class="form-control select2-search-disable" required>
                                                        <option value="" selected>Please Select</option>
                                                        <option value="4">As Travel Agent</option>
                                                        {{-- <option value="2">As DIY Agent</option> --}}
                                                        <option value="1">As DIY Individu</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3" id="comp_no_div" style="display: none;">
                                                    <label for="comp_no">SSM/Company No</label>
                                                    <input type="text" class="form-control" id="comp_no" name="comp_no" placeholder="Please Insert SSM/Company No">
                                                </div> 

                                                <div class="mb-3" id="ssm_cert_div" style="display: none;">
                                                    <label for="ssm_cert">SSM Certificate Upload </label>
                                                    <input type="file" class="form-control" id="ssm_cert" name="ssm_cert">
                                                </div>

                                                {{-- <input type="hidden" value="7" name="role"> --}}
                                                <div class="mt-4 d-grid">
                                                    <button class="btn btn-primary waves-effect waves-light"
                                                        type="submit">Register</button>
                                                </div>
                                            </form>

                                            <div class="mt-3 text-center">
                                                <p>Already have an account ? <a href="{{ url('login') }}"
                                                        class="fw-medium text-primary"> Login</a> </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid -->
        </div>

    @endsection
    @section('script')
        <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <!-- owl.carousel js -->
        <script src="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>
        <!-- auth-2-carousel init -->
        <script src="{{ URL::asset('/assets/js/pages/auth-2-carousel.init.js') }}"></script>
        <script>
            $('#role').change(function() {
                console.log($('#role').val())
                if ($('#role').val() == '4') {
                    $('#ssm_cert_div').show();
                    $('#comp_no_div').show();
                    $("#ssm_cert").prop('required',true);
                    $("#comp_no").prop('required',true);
                } else {
                    $('#ssm_cert_div').hide();
                    $('#comp_no_div').hide();
                    $("#ssm_cert").prop('required',false);
                    $("#comp_no").prop('required',false);
                }
            });
        </script>
    @endsection
