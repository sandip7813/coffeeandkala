@props([
    'showSidebarToggle' => true,
])

@php
    $featureNavCategories = \App\Models\Category::query()->ofType(\App\Models\Category::TYPE_FEATURE)->active()->ordered()->get();
    $journalNavCategories = \App\Models\Category::query()->ofType(\App\Models\Category::TYPE_JOURNAL)->active()->ordered()->get();
@endphp

<header {{ $attributes->class(['site-header']) }}>
    <div class="header-brand">
        @if ($showSidebarToggle)
            <button class="icon-btn" id="toggleLeftDrawer" aria-label="Toggle left panel" title="Toggle left panel" aria-expanded="true">
                <i class="fa-solid fa-bars"></i>
            </button>
        @endif

        <a href="{{ route('home') }}" class="logo" aria-label="Coffee & Kala home">
            <img
                src="{{ \App\Support\BrandLogo::url() }}"
                alt="Coffee & Kala"
                class="logo-image"
                width="{{ \App\Support\BrandLogo::width() }}"
                height="{{ \App\Support\BrandLogo::height() }}"
            >
        </a>
    </div>

    <ul class="nav-links">
        <li>
            <a
                href="{{ route('home') }}"
                @class(['is-active' => request()->routeIs('home')])
            >Fresh Brew</a>
        </li>
        <li>
            <a
                href="{{ route('about') }}"
                @class(['is-active' => request()->routeIs('about')])
            >Our Story</a>
        </li>
        <li class="nav-item-has-dropdown">
            <a
                href="{{ route('features') }}"
                @class([
                    'nav-dropdown-toggle',
                    'nav-link-with-sub',
                    'is-active' => request()->routeIs('features', 'features.show'),
                ])
                aria-haspopup="true"
                aria-expanded="false"
            >
                Features
                <i class="fa-solid fa-chevron-down nav-dropdown-caret" aria-hidden="true"></i>
                <span class="nav-link-muted">Articles</span>
            </a>
            <ul class="nav-dropdown" aria-label="Features">
                @foreach ($featureNavCategories as $category)
                    <li><a href="{{ route('features.show', $category->slug) }}">{{ $category->title }}</a></li>
                @endforeach
            </ul>
        </li>
        <li class="nav-item-has-dropdown">
            <a
                href="{{ route('journal') }}"
                @class([
                    'nav-dropdown-toggle',
                    'nav-link-with-sub',
                    'is-active' => request()->routeIs('journal', 'journal.category'),
                ])
                aria-haspopup="true"
                aria-expanded="false"
            >
                Journal
                <i class="fa-solid fa-chevron-down nav-dropdown-caret" aria-hidden="true"></i>
                <span class="nav-link-muted">Blogs</span>
            </a>
            <ul class="nav-dropdown" aria-label="Journal">
                @foreach ($journalNavCategories as $category)
                    <li><a href="{{ route('journal.category', $category->slug) }}">{{ $category->title }}</a></li>
                @endforeach
            </ul>
        </li>
        <li>
            <a
                href="{{ route('studio') }}"
                @class(['is-active' => request()->routeIs('studio')])
            >Studio</a>
        </li>
        <li>
            <a
                href="{{ route('gallery') }}"
                @class(['is-active' => request()->routeIs('gallery')])
            >Gallery</a>
        </li>
        <li>
            <a
                href="{{ route('poetry') }}"
                @class(['is-active' => request()->routeIs('poetry', 'poetry.show')])
            >Poetry</a>
        </li>
    </ul>

    <div class="header-actions">
        <button class="icon-btn search-btn" id="openSearch" aria-label="Search">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <button class="icon-btn mobile-menu-btn" id="openRightDrawer" aria-label="Open Topbar Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>
