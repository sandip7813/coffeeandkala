@php
    $items = app('adminlte')->menu('sidebar');
    $sidebarTheme = config('adminlte.sidebar_theme', 'dark');
    $sidebarClasses = config('adminlte.classes_sidebar', 'bg-body-secondary shadow');
@endphp
<aside class="app-sidebar {{ $sidebarClasses }}" @if ($sidebarTheme === 'dark') data-bs-theme="dark" @endif>
    <div class="sidebar-brand {{ config('adminlte.classes_brand') }}">
        <a href="{{ url('/') }}" class="brand-link">
            <img src="{{ \App\Support\BrandLogo::url() }}"
                 alt="{{ config('adminlte.logo_img_alt', 'Logo') }}"
                 class="{{ config('adminlte.logo_img_class', 'brand-image') }}">
            <span class="brand-text {{ config('adminlte.classes_brand_text', 'fw-light') }}">
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="{{ __('Main navigation') }}">
            <ul class="nav sidebar-menu flex-column {{ config('adminlte.classes_sidebar_nav') }}"
                data-lte-toggle="treeview"
                data-accordion="false"
                role="menu"
                id="navigation">
                @foreach ($items as $item)
                    @include('adminlte::partials.menu-item', ['item' => $item])
                @endforeach
            </ul>
        </nav>
    </div>

    <div class="sidebar-user-footer border-top border-secondary border-opacity-25">
        <ul class="nav sidebar-menu flex-column" role="menu" aria-label="{{ __('Account') }}">
            <li class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link">
                    <i class="nav-icon bi bi-person" aria-hidden="true"></i>
                    <p>Edit Profile</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.profile.password.edit') }}" class="nav-link">
                    <i class="nav-icon bi bi-key" aria-hidden="true"></i>
                    <p>Change Password</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('adminlte-sidebar-logout-form').submit();">
                    <i class="nav-icon bi bi-box-arrow-right" aria-hidden="true"></i>
                    <p>Logout</p>
                </a>
                <form id="adminlte-sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </div>
</aside>

@once
    <style>
        /*
         * Pin account links to the bottom of the sidebar panel.
         * AdminLTE scrolls .sidebar-wrapper; keep the footer as a sibling
         * so it stays visible while the menu scrolls above it.
         */
        .app-sidebar {
            display: flex;
            flex-direction: column;
            max-height: 100vh;
        }

        .app-sidebar .sidebar-wrapper {
            flex: 1 1 auto;
            height: auto !important;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar-user-footer {
            flex-shrink: 0;
            padding: .5rem;
            background: inherit;
        }

        /* Match AdminLTE .sidebar-wrapper nav link colors / hover. */
        .sidebar-user-footer .nav-item {
            max-width: 100%;
        }

        .sidebar-user-footer .nav-link {
            display: flex;
            justify-content: flex-start;
            color: var(--lte-sidebar-color);
        }

        .sidebar-user-footer .nav-link:hover,
        .sidebar-user-footer .nav-link:focus {
            color: var(--lte-sidebar-hover-color);
            background-color: var(--lte-sidebar-hover-bg);
        }

        .sidebar-user-footer .nav-link.active {
            color: var(--lte-sidebar-menu-active-color);
            background-color: var(--lte-sidebar-menu-active-bg);
        }

        .sidebar-user-footer .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            max-width: 1.5rem;
        }

        .sidebar-user-footer .nav-link p {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 0;
        }

        .sidebar-mini.sidebar-collapse .app-sidebar:not(:hover) .sidebar-user-footer p {
            width: 0;
            margin-left: 0;
            opacity: 0;
            overflow: hidden;
        }
    </style>
@endonce
