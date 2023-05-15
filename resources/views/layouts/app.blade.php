<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">       
    <div id="loader" style="display: none;">
        <div class="d-flex justify-content-center align-items-center w-100" style="height: 100vh;">
            <span style="width: 50px;height: 50px;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </div> 
    </div>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
