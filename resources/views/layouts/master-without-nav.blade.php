<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <title>Al Khairi Care</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content="AKC" name="description" />
        <meta content="AKC" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ URL::asset('assets/images/akc-logo.png')}}">
        @include('layouts.head-css')
  </head>

    @yield('body')
    
    @yield('content')

    @include('layouts.vendor-scripts')
    </body>
</html>