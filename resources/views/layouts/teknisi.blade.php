<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo_wag.png') }}">
    <title>@yield('title') - Call Me GA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    @if (!session('login_via_superapp'))
        @include('layouts.header')
    @endif

    <main class="container mx-auto p-4 {{ session('login_via_superapp') ? 'pt-4' : 'pt-20' }} pb-20">
        @yield('content')
    </main>

    @include('components.teknisi-bottom-nav')

    @stack('scripts')
</body>
</html>
