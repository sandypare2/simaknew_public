<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <title>@yield('title', ' SIMAK')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Monitoring & Analisa Kinerja">
    <meta name="keywords" content="pegawai, kinerja, simkp">
    <meta content="Paguntaka Cahaya Nusantara" name="author">
    <!-- <meta name="_token" content="{{ csrf_token() }}"> -->
    <link rel="shortcut icon" href="{{ asset('assets/images/pln.png') }}">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    @include('partials.datatable-css')
    @yield('css')
    @include('partials.head-css')
</head>

<body>
    @include('partials.header')
    @include('partials.sidebar')
    @include('partials.preloader')


    <main class="app-wrapper">
        <div class="app-container">
            @include('partials.breadcrumb')

            <!-- end page title -->

            @yield('content')
            @include('partials.bottom-wrapper')
            @include('partials.datatable-script')
            @stack('scripts')
            @yield('js')

</body>

</html>
