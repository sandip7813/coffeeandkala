<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Features — Coffee & Kala')</title>
    <x-favicon />

    <x-site-fonts />

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
