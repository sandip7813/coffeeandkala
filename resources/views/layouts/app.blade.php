<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffee & Kala')</title>
    <meta name="description" content="@yield('meta_description', 'Coffee & Kala — an editorial journal of coffee culture and visual storytelling.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Coffee & Kala')">
    <x-favicon />

    <x-site-fonts />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-sidebar>@yield('sidebar')</x-sidebar>
    <x-mobile-drawer />

    <div class="app-wrapper">
        <main class="main-content">
            <x-header />
                @yield('content')
            <x-footer />
        </main>
    </div>

    <x-search-modal />
    <x-go-top />

    @stack('scripts')
</body>
</html>
