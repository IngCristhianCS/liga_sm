<!doctype html>
<html class="no-js " lang="es_MX">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Liga de fútbol San Miguel.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Liga San Miguel') }}</title>
    
    @vite(['resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/color_skins.css">
</head>
<body class="theme-black">

    @include('layouts.navigation')
    
    <!-- Main Content -->
    {{ $slot }}

    <!-- Scripts -->
    <script src="assets/bundles/libscripts.bundle.js"></script>
    <script src="assets/bundles/vendorscripts.bundle.js"></script>
    <script src="assets/bundles/mainscripts.bundle.js"></script>
    @auth
        <script>
            window.authUser = @json(auth()->user()->load('role'));
            window.authUser.apiToken = "{{ session('api_token') }}";
        </script>
        <script src="assets/bundles/datatablescripts.bundle.js"></script>
    @else
        <script>
            window.authUser = null;
        </script>
    @endauth
</body>
</html>