<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffee & Kala')</title>
    <x-favicon />

    <x-site-fonts />

    @vite(['resources/css/app.css', 'resources/css/error.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="layout-no-sidebar about-body error-body">
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-mobile-drawer />

    <div class="about-shell error-shell">
        <div class="about-topbar error-topbar">
            <x-header class="about-header error-header" :show-sidebar-toggle="false" />
        </div>

        <main class="about-main error-main" id="errorMain">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <x-search-modal />
    <x-go-top />

    @stack('scripts')
</body>
</html>
