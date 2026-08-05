<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Journal — Coffee & Kala')</title>
    <x-favicon />

    <x-site-fonts />

    @vite(['resources/css/app.css', 'resources/css/journal.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="layout-no-sidebar about-body journal-body">
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-mobile-drawer />

    <div class="about-shell journal-shell">
        <div class="about-topbar journal-topbar">
            <x-header class="about-header journal-header" :show-sidebar-toggle="false" />
        </div>

        <main class="about-main journal-main" id="journalMain">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <x-search-modal />
    <x-go-top />

    @stack('scripts')
</body>
</html>
