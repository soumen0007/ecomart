<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title','EcoMart')</title>

    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Website CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

@include('partials.navbar')

<main>

    @yield('content')

</main>

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('assets/js/api.js') }}"></script>
<script src="{{ asset('assets/js/auth.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

@stack('scripts')

</body>
</html>