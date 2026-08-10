<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="footer-logo" aria-label="Coffee & Kala home">
                <img
                    src="{{ \App\Support\BrandLogo::url() }}"
                    alt="Coffee & Kala"
                    class="footer-logo-image"
                    width="{{ \App\Support\BrandLogo::width() }}"
                    height="{{ \App\Support\BrandLogo::height() }}"
                >
            </a>
            <p class="footer-tagline">
                An editorial journal of slow living, art, travel, and quiet stories — brewed with coffee and kala.
            </p>
            @php($contactInfo = \App\Support\ContactInfo::filled())
            @if ($contactInfo !== [])
                <div class="footer-contact-block">
                    <p class="footer-contact-label">Get in Touch</p>
                    <ul class="footer-contact">
                        @foreach ($contactInfo as $contact)
                            <li>
                                <a
                                    href="{{ $contact['href'] }}"
                                    class="footer-contact-link"
                                    @if ($contact['key'] === 'address') target="_blank" rel="noopener noreferrer" @endif
                                >
                                    <span class="footer-contact-icon">
                                        <i class="{{ $contact['icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <span>{{ $contact['value'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @php($socialLinks = \App\Support\SocialLinks::filled())
            @if ($socialLinks !== [])
                <div class="footer-social">
                    @foreach ($socialLinks as $social)
                        <a
                            href="{{ $social['url'] }}"
                            class="footer-social-link"
                            aria-label="{{ $social['label'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <nav class="footer-nav" aria-label="Footer">
            <div class="footer-col">
                <h3 class="footer-heading">Explore</h3>
                <ul class="footer-links">
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
                    <li>
                        <a
                            href="{{ route('features') }}"
                            @class(['is-active' => request()->routeIs('features', 'features.show')])
                        >Features</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('journal') }}"
                            @class(['is-active' => request()->routeIs('journal')])
                        >Journal</a>
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
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Articles</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('features.show', 'art-culture') }}">Art &amp; Culture</a></li>
                    <li><a href="{{ route('features.show', 'experiences') }}">Experiences</a></li>
                    <li><a href="{{ route('features.show', 'on-a-budget') }}">On A Budget</a></li>
                    <li><a href="{{ route('features.show', 'luxury-escapes') }}">Luxury Escapes</a></li>
                    <li><a href="{{ route('features.show', 'global-chapters') }}">Global Chapters</a></li>
                    <li><a href="{{ route('features.show', 'not-on-the-atlas') }}">Not On The Atlas</a></li>
                    <li><a href="{{ route('features.show', 'vineyard-tales') }}">Vineyard Tales</a></li>
                    <li><a href="{{ route('features.show', 'coffee-classics') }}">Coffee &amp; Classics</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Journal</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('journal.category', 'the-bigger-picture') }}">The Bigger Picture</a></li>
                    <li><a href="{{ route('journal.category', 'worth-knowing') }}">Worth Knowing</a></li>
                    <li><a href="{{ route('journal.category', 'chapters-over-coffee') }}">Chapters Over Coffee</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Coffee &amp; Kala. All rights reserved.</p>
        <p>Designed by Sandip Nandy.</p>
    </div>
</footer>
