<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Features — Coffee & Kala')</title>
    <x-favicon />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/features.css', 'resources/css/features-themes.css', 'resources/js/app.js', 'resources/js/features.js'])
    @stack('styles')
</head>
<body class="layout-no-sidebar about-body features-body">
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-mobile-drawer />

    <div class="about-shell features-shell">
        <div class="about-topbar features-topbar">
            <x-header class="about-header features-header" :show-sidebar-toggle="false" />
        </div>

        <main class="about-main features-main" id="featuresMain">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <x-search-modal />
    <x-go-top />

    @stack('scripts')
</body>
</html>
