<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Al Khairi Care</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="AKC" name="description" />
    <meta content="AKC" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/akc-logo.png') }}">
    @include('layouts.head-css')

    <style>
    .dropdown-menu-end-noti[style] {
        width: 270px !important;
    }
    </style>

</head>

@section('body')
    <body data-sidebar="dark">
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="notificationModalTitle">Task Notifications</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @if(auth()->user()->hasAnyRole('akc'))
                                    <input type="hidden" id="rolesId" name="rolesId" value="akc">
                                @elseif(auth()->user()->hasAnyRole('fin'))
                                    <input type="hidden" id="rolesId" name="rolesId" value="fin">
                                @else
                                    <input type="hidden" id="rolesId" name="rolesId" value="">
                                @endif
                                <div class="modal-body" id="notificationModalbody"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

</html>
