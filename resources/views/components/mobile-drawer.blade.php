<div class="mobile-drawer-right" id="mobileDrawerRight">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <span style="font-size: 11px; letter-spacing: 2px; color: var(--coffee-beige); font-weight: 600;">MENU</span>
        <button class="icon-btn" id="closeRightDrawer" aria-label="Close Right Navigation">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <ul class="drawer-top-nav-list">
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
        <li @class(['drawer-item-has-children', 'is-open' => request()->routeIs('features', 'features.show')])>
            <button type="button" class="drawer-submenu-toggle" aria-expanded="{{ request()->routeIs('features', 'features.show') ? 'true' : 'false' }}">
                Features
                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <ul class="drawer-submenu">
                <li><a href="{{ route('features.show', 'art-culture') }}">Art &amp; Culture</a></li>
                <li><a href="{{ route('features.show', 'experiences') }}">Experiences</a></li>
                <li><a href="{{ route('features.show', 'on-a-budget') }}">On A Budget</a></li>
                <li><a href="{{ route('features.show', 'luxury-escapes') }}">Luxury Escapes</a></li>
                <li><a href="{{ route('features.show', 'global-chapters') }}">Global Chapters</a></li>
                <li><a href="{{ route('features.show', 'not-on-the-atlas') }}">Not On The Atlas</a></li>
                <li><a href="{{ route('features.show', 'vineyard-tales') }}">Vineyard Tales</a></li>
                <li><a href="{{ route('features.show', 'coffee-classics') }}">Coffee &amp; Classics</a></li>
            </ul>
        </li>
        <li>
            <a
                href="{{ route('journal') }}"
                @class(['is-active' => request()->routeIs('journal')])
            >Journal</a>
        </li>
        <li><a href="javascript:void(0)">Conversations</a></li>
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
        <li><a href="javascript:void(0)">Poetry</a></li>
    </ul>
</div>
