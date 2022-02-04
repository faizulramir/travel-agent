@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login') 2
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

                                    <div class="p-4 mt-auto">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-7">
                                                <div class="text-center">
                                                    <span class="logo-lg">
                                                        {{-- <img src="{{ URL::asset ('/assets/images/akc.png') }}" alt="" height="100"> --}}
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
                                            <h5 class="text-primary">Welcome Back !</h5>
                                        </div>

                                        <div class="mt-4">
                                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Email</label>
                                                    <input name="email" type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('email') }}" id="username"
                                                        placeholder="Enter Email" autocomplete="email" autofocus>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        @if (Route::has('password.request'))
                                                            <a href="{{ route('password.request') }}"
                                                                class="text-muted">Forgot password?</a>
                                                        @endif
                                                    </div>
                                                    <label class="form-label">Password</label>
                                                    <div
                                                        class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                                        <input type="password" name="password"
                                                            class="form-control  @error('password') is-invalid @enderror"
                                                            id="userpassword" value="" placeholder="Enter password"
                                                            aria-label="Password" aria-describedby="password-addon">
                                                        <button class="btn btn-light " type="button" id="password-addon"><i
                                                                class="mdi mdi-eye-outline"></i></button>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remember"
                                                        {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remember">
                                                        Remember me
                                                    </label>
                                                </div>

                                                <div class="mt-3 d-grid">
                                                    <button class="btn btn-primary waves-effect waves-light"
                                                        type="submit">Log
                                                        In</button>
                                                </div>
                                            </form>
                                            <div class="mt-5 text-center">
                                                <p>Don't have an account ? <a href="{{ url('register') }}"
                                                        class="fw-medium text-primary"> Signup now </a>
                                                </p>
                                                <br>
                                                <p>
                                                    <span style="color:gray" clas="text-center">version 0.8</span>
                                                </p>

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
