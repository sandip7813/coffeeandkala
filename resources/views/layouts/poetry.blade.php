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

    @vite(['resources/css/app.css', 'resources/css/poetry.css', 'resources/js/app.js', 'resources/js/poetry.js'])
    @stack('styles')
</head>
<body class="layout-no-sidebar about-body poetry-body">
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-mobile-drawer />

    <div class="about-shell poetry-shell">
        <div class="about-topbar poetry-topbar">
            <x-header class="about-header poetry-header" :show-sidebar-toggle="false" />
        </div>

        <main class="about-main poetry-main" id="poetryMain">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <x-search-modal />
    <x-go-top />

    @stack('scripts')
</body>
</html>
