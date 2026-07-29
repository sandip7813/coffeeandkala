<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="footer-logo" aria-label="Coffee & Kala home">
                <img
                    src="{{ asset('images/logo/logo.png') }}"
                    alt="Coffee & Kala"
                    class="footer-logo-image"
                    width="404"
                    height="179"
                >
            </a>
            <p class="footer-tagline">
                An editorial journal of slow living, art, travel, and quiet stories — brewed with coffee and kala.
            </p>
            <div class="footer-social">
                <a href="#" class="footer-social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="footer-social-link" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                <a href="#" class="footer-social-link" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="footer-social-link" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
            </div>
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
                    <li><a href="#">Conversations</a></li>
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
                    <li><a href="#">Poetry</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Journal</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('journal') }}#travel-diaries">Travel Diaries</a></li>
                    <li><a href="{{ route('journal') }}#destination-guides">Destination Guides</a></li>
                    <li><a href="{{ route('journal') }}#local-stories">Local Stories</a></li>
                    <li><a href="{{ route('journal') }}#life-on-the-road">Life on the Road</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Stay Close</h3>
                <p class="footer-note">
                    New stories, quiet mornings, and studio notes — delivered occasionally.
                </p>
                <form class="footer-subscribe" action="#" method="post" onsubmit="return false;">
                    <label class="sr-only" for="footer-email">Email address</label>
                    <input
                        id="footer-email"
                        type="email"
                        name="email"
                        class="footer-email"
                        placeholder="Your email"
                        autocomplete="email"
                    >
                    <button type="submit" class="footer-subscribe-btn">Join</button>
                </form>
            </div>
        </nav>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Coffee &amp; Kala. All rights reserved.</p>
        <p>Designed by Sandip Nandy.</p>
    </div>
</footer>
