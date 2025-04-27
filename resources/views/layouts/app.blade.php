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
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">
    <!-- Vite Assets -->
    @vite(['resources/css/app.css'])
</head>
<body class="theme-black">

    @include('layouts.navigation')
    
    <!-- Main Content -->
    {{ $slot }}

    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted">&copy; 2025 Liga Futbol San Miguel. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-muted">Contacto</a></li>
                    </ul>
                </div>
            </div>
        </div>
      </footer>


    <!-- Scripts -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/knob.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>    
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    @auth
        <script>
            window.authUser = @json(auth()->user()->load('role'));
            window.authUser.apiToken = "{{ session('api_token') }}";
        </script>
    @else
        <script>
            window.authUser = null;
        </script>
    @endauth
</body>
</html>