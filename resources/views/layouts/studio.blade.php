<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Studio — Coffee & Kala')</title>
    <x-favicon />

    <x-site-fonts />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/galleria/1.6.1/themes/classic/galleria.classic.min.css">

    @vite(['resources/css/app.css', 'resources/css/studio.css', 'resources/js/app.js', 'resources/js/gallery.js'])
    @stack('styles')
</head>
<body class="layout-no-sidebar about-body studio-body">
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <x-mobile-drawer />

    <div class="about-shell studio-shell">
        <div class="about-topbar studio-topbar">
            <x-header class="about-header studio-header" :show-sidebar-toggle="false" />
        </div>

        <main class="about-main studio-main" id="studioMain">
            @yield('content')
        </main>

        <x-footer />
    </div>

    <x-search-modal />
    <x-go-top />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/galleria/1.6.1/galleria.min.js"></script>
    @stack('scripts')
</body>
</html>
