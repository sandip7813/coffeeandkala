@php
    $title = trim(($title ?? config('adminlte.title', 'AdminLTE 4')));
    $authType = $authType ?? 'login'; // login | register
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <x-favicon />
    {{-- Bootstrap Icons ship via the Vite bundle (imported in resources/css/adminlte.css) --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    @stack('css')
</head>
<body class="{{ $authType }}-page bg-body-secondary">
    <div class="{{ $authType }}-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1 d-inline-flex flex-column align-items-center gap-2 text-decoration-none">
                    @if (config('adminlte.auth_logo.enabled'))
                        <img
                            src="{{ \App\Support\BrandLogo::url() }}"
                            alt="{{ config('adminlte.auth_logo.img.alt', 'Logo') }}"
                            class="{{ config('adminlte.auth_logo.img.class') }}"
                            width="{{ config('adminlte.auth_logo.img.width', 80) }}"
                            height="{{ config('adminlte.auth_logo.img.height', 80) }}"
                        >
                    @endif
                    {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                </a>
            </div>
            <div class="card-body">
                @yield('auth_body')
            </div>
        </div>
    </div>
    @stack('js')
</body>
</html>
