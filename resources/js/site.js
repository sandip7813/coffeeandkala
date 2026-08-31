import { smoothScrollTo } from './smooth-scroll';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('editorialSidebar');
    const rightDrawer = document.getElementById('mobileDrawerRight');
    const overlay = document.getElementById('drawerOverlay');
    const searchModal = document.getElementById('searchModal');
    const body = document.body;

    function isDesktop() {
        return window.innerWidth > 1024;
    }

    function isLeftPanelOpen() {
        if (!sidebar) {
            return false;
        }

        if (isDesktop()) {
            return !sidebar.classList.contains('is-collapsed');
        }

        return sidebar.classList.contains('left-open');
    }

    function syncSidebarToggleState() {
        const toggleBtn = document.getElementById('toggleLeftDrawer');

        if (!toggleBtn) {
            return;
        }

        toggleBtn.setAttribute('aria-expanded', String(isLeftPanelOpen()));
    }

    function openLeftDrawer() {
        if (!sidebar) {
            return;
        }

        closeRightDrawer();

        if (isDesktop()) {
            sidebar.classList.remove('is-collapsed');
            body.classList.remove('sidebar-collapsed');
            syncSidebarToggleState();
            return;
        }

        sidebar.classList.add('left-open');
        overlay?.classList.add('active');
        body.style.overflow = 'hidden';
        syncSidebarToggleState();
    }

    function closeLeftDrawer() {
        if (!sidebar) {
            return;
        }

        if (isDesktop()) {
            sidebar.classList.add('is-collapsed');
            body.classList.add('sidebar-collapsed');
            syncSidebarToggleState();
            return;
        }

        sidebar.classList.remove('left-open');
        checkCloseOverlay();
        syncSidebarToggleState();
    }

    function toggleLeftDrawer() {
        if (isLeftPanelOpen()) {
            closeLeftDrawer();
        } else {
            openLeftDrawer();
        }
    }

    function openRightDrawer() {
        closeLeftDrawer();
        rightDrawer?.classList.add('drawer-open');
        overlay?.classList.add('active');
        body.style.overflow = 'hidden';
    }

    function closeRightDrawer() {
        rightDrawer?.classList.remove('drawer-open');
        checkCloseOverlay();
    }

    function closeAllDrawers() {
        closeLeftDrawer();
        closeRightDrawer();
    }

    function checkCloseOverlay() {
        const leftOpen = sidebar?.classList.contains('left-open');
        const rightOpen = rightDrawer?.classList.contains('drawer-open');

        if (!leftOpen && !rightOpen) {
            overlay?.classList.remove('active');
            body.style.overflow = '';
        }
    }

    document.getElementById('toggleLeftDrawer')?.addEventListener('click', toggleLeftDrawer);
    document.getElementById('openRightDrawer')?.addEventListener('click', openRightDrawer);
    document.getElementById('closeRightDrawer')?.addEventListener('click', closeRightDrawer);
    overlay?.addEventListener('click', closeAllDrawers);
    window.addEventListener('resize', syncSidebarToggleState);
    syncSidebarToggleState();

    document.querySelectorAll('.nav-item-has-dropdown').forEach((item) => {
        const toggle = item.querySelector('.nav-dropdown-toggle');

        const closeItem = () => {
            item.classList.remove('is-open');
            toggle?.setAttribute('aria-expanded', 'false');
        };

        const openItem = () => {
            document.querySelectorAll('.nav-item-has-dropdown.is-open').forEach((openItem) => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                    openItem.classList.remove('is-dropdown-locked');
                    openItem.querySelector('.nav-dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                }
            });

            item.classList.remove('is-dropdown-locked');
            item.classList.add('is-open');
            toggle?.setAttribute('aria-expanded', 'true');
        };

        toggle?.addEventListener('click', (event) => {
            const href = toggle.getAttribute('href') ?? '';
            const isNavigable =
                href !== '' &&
                href !== 'javascript:void(0)' &&
                !href.startsWith('#');
            const clickedCaret = Boolean(event.target.closest('.nav-dropdown-caret'));

            // Clicking the Features label navigates; the caret only toggles the menu.
            if (isNavigable && !clickedCaret) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            if (item.classList.contains('is-open')) {
                item.classList.add('is-dropdown-locked');
                closeItem();
                return;
            }

            openItem();
        });

        item.addEventListener('mouseenter', () => {
            if (item.classList.contains('is-dropdown-locked')) {
                return;
            }

            openItem();
        });

        item.addEventListener('mouseleave', () => {
            item.classList.remove('is-dropdown-locked');
            closeItem();
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.nav-item-has-dropdown')) {
            document.querySelectorAll('.nav-item-has-dropdown').forEach((item) => {
                item.classList.remove('is-open');
                item.classList.remove('is-dropdown-locked');
                item.querySelector('.nav-dropdown-toggle')?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    document.querySelectorAll('.drawer-submenu-toggle').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const item = toggle.closest('.drawer-item-has-children');
            const isOpen = item?.classList.contains('is-open');

            item?.classList.toggle('is-open', !isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    document.querySelectorAll('.editorial-sidebar a, .mobile-drawer-right a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                closeAllDrawers();
            }
        });
    });

    document.querySelectorAll('.editorial-sidebar a').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            const targetId = link.getAttribute('data-target');
            const target = targetId ? document.getElementById(targetId) : null;

            if (target) {
                smoothScrollTo(target);

                if (window.location.hash) {
                    history.replaceState(null, '', window.location.pathname + window.location.search);
                }
            }
        });
    });

    function handleScroll() {
        const scrollPos = window.scrollY + window.innerHeight / 2;

        if (sidebar) {
            document.querySelectorAll('section[id]').forEach((section) => {
                const top = section.offsetTop;
                const bottom = top + section.offsetHeight;
                const id = section.id;

                if (scrollPos >= top && scrollPos <= bottom) {
                    document.querySelectorAll('.editorial-sidebar .nav-item').forEach((item) => {
                        item.classList.remove('active');
                    });

                    document
                        .querySelector(`.editorial-sidebar a[data-target="${id}"]`)
                        ?.classList.add('active');
                }
            });
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('load', handleScroll);
    handleScroll();

    const scrollReveals = Array.from(document.querySelectorAll('.animate-on-scroll'));

    function revealElement(element) {
        element.classList.add('visible');
    }

    function isElementInViewport(element) {
        const rect = element.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

        return rect.top < viewportHeight * 0.92 && rect.bottom > viewportHeight * 0.08;
    }

    function revealVisibleElements() {
        scrollReveals.forEach((element) => {
            if (!element.classList.contains('visible') && isElementInViewport(element)) {
                revealElement(element);
            }
        });
    }

    if (scrollReveals.length > 0) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            scrollReveals.forEach(revealElement);
        } else {
            const revealObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            revealElement(entry.target);
                            revealObserver.unobserve(entry.target);
                        }
                    });
                },
                {
                    root: null,
                    rootMargin: '0px 0px -6% 0px',
                    threshold: 0.05,
                },
            );

            scrollReveals.forEach((element) => revealObserver.observe(element));
            requestAnimationFrame(revealVisibleElements);
            window.addEventListener('scroll', revealVisibleElements, { passive: true });
            window.addEventListener('resize', revealVisibleElements);
            window.addEventListener('load', revealVisibleElements);
        }
    }

    document.getElementById('openSearch')?.addEventListener('click', () => {
        searchModal?.classList.add('active');
    });

    document.getElementById('closeSearch')?.addEventListener('click', () => {
        searchModal?.classList.remove('active');
    });

    const aboutReveals = document.querySelectorAll('.about-page .about-reveal');

    if (aboutReveals.length > 0 && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        revealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.15,
            },
        );

        aboutReveals.forEach((element) => revealObserver.observe(element));
    } else {
        aboutReveals.forEach((element) => element.classList.add('is-inview'));
    }

    const galleryReveals = document.querySelectorAll('.gallery-page .gallery-reveal');

    if (galleryReveals.length > 0 && 'IntersectionObserver' in window) {
        const galleryRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        galleryRevealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            },
        );

        galleryReveals.forEach((element) => galleryRevealObserver.observe(element));
    } else {
        galleryReveals.forEach((element) => element.classList.add('is-inview'));
    }

    const studioReveals = document.querySelectorAll('.studio-page .studio-reveal');

    if (studioReveals.length > 0 && 'IntersectionObserver' in window) {
        const studioRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        studioRevealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            },
        );

        studioReveals.forEach((element) => studioRevealObserver.observe(element));
    } else {
        studioReveals.forEach((element) => element.classList.add('is-inview'));
    }

    const studioIntroToggle = document.querySelector('[data-studio-intro-toggle]');
    const studioIntroMore = document.querySelector('[data-studio-intro-more]');

    if (studioIntroToggle && studioIntroMore) {
        studioIntroToggle.addEventListener('click', () => {
            const expanded = !studioIntroMore.classList.contains('is-open');

            if (expanded) {
                studioIntroMore.classList.add('is-open');
                studioIntroMore.style.maxHeight = `${studioIntroMore.scrollHeight}px`;
            } else {
                studioIntroMore.style.maxHeight = `${studioIntroMore.scrollHeight}px`;
                requestAnimationFrame(() => {
                    studioIntroMore.classList.remove('is-open');
                    studioIntroMore.style.maxHeight = '0px';
                });
            }

            studioIntroToggle.textContent = expanded ? 'Know Less' : 'Know More';
            studioIntroToggle.setAttribute('aria-expanded', String(expanded));
        });

        studioIntroMore.addEventListener('transitionend', (event) => {
            if (event.propertyName === 'max-height' && studioIntroMore.classList.contains('is-open')) {
                studioIntroMore.style.maxHeight = 'none';
            }
        });

        window.addEventListener('resize', () => {
            if (studioIntroMore.classList.contains('is-open') && studioIntroMore.style.maxHeight !== 'none') {
                studioIntroMore.style.maxHeight = `${studioIntroMore.scrollHeight}px`;
            }
        });
    }

    const journalReveals = document.querySelectorAll('.journal-page .journal-reveal');

    if (journalReveals.length > 0 && 'IntersectionObserver' in window) {
        const journalRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        journalRevealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            },
        );

        journalReveals.forEach((element) => journalRevealObserver.observe(element));
    } else {
        journalReveals.forEach((element) => element.classList.add('is-inview'));
    }

    const featuresReveals = document.querySelectorAll('.features-page .features-reveal');

    if (featuresReveals.length > 0 && 'IntersectionObserver' in window) {
        const featuresRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        featuresRevealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            },
        );

        featuresReveals.forEach((element) => featuresRevealObserver.observe(element));
    } else {
        featuresReveals.forEach((element) => element.classList.add('is-inview'));
    }

    const poetryReveals = document.querySelectorAll('.poetry-page .poetry-reveal');

    if (poetryReveals.length > 0 && 'IntersectionObserver' in window) {
        const poetryRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        poetryRevealObserver.unobserve(entry.target);
                    }
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            },
        );

        poetryReveals.forEach((element) => poetryRevealObserver.observe(element));
    } else {
        poetryReveals.forEach((element) => element.classList.add('is-inview'));
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            searchModal?.classList.remove('active');
            closeAllDrawers();
        }
    });
});
