@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Recover_Password') 2
@endsection

@section('css')
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">

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

                                        <div>
                                            <h5 class="text-primary"> Reset Password</h5>
                                        </div>

                                        <div class="mt-4">
                                            <form class="form-horizontal" method="POST"
                                                action="{{ route('password.update') }}">
                                                @csrf
                                                <input type="hidden" name="token" value="{{ $token }}">
                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="useremail" name="email" placeholder="Enter email"
                                                        value="{{ $email ?? old('email') }}" id="email">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="userpassword">Password</label>
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        name="password" id="userpassword" placeholder="Enter password">
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="userpassword">Confirm Password</label>
                                                    <input id="password-confirm" type="password"
                                                        name="password_confirmation" class="form-control"
                                                        placeholder="Enter confirm password">
                                                </div>

                                                <div class="text-end">
                                                    <button class="btn btn-primary w-md waves-effect waves-light"
                                                        type="submit">Reset</button>
                                                </div>

                                            </form>
                                            <div class="mt-5 text-center">
                                                <p>Remember It ? <a href="{{ url('login') }}"
                                                        class="font-weight-medium text-primary"> Sign In here</a> </p>
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
        <!-- owl.carousel js -->
        <script src="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>
        <!-- auth-2-carousel init -->
        <script src="{{ URL::asset('/assets/js/pages/auth-2-carousel.init.js') }}"></script>
    @endsection
