// Article detail page: Table of Contents scroll-spy + smooth scroll (no
// hash ever touches the URL), a sliding FAQ accordion, and a gentle
// reveal-on-scroll for the Words & Images sections.

import { smoothScrollTo } from './smooth-scroll';

(function () {
    // ─── Table of Contents ───
    // Links use data-scroll-target rather than href="#id", so nothing here
    // ever writes a hash to the URL. Kept as a small lookup (id -> element)
    // so the target list could later be swapped for one fetched dynamically
    // without touching the scrolling/spy logic below.
    const tocLinks = Array.from(document.querySelectorAll('[data-toc-link]'));

    if (tocLinks.length) {
        tocLinks.forEach((link) => {
            link.addEventListener('click', () => {
                const targetId = link.getAttribute('data-scroll-target');
                const target = targetId ? document.getElementById(targetId) : null;

                // The site's own eased slide (used by the header nav too)
                // rather than the browser's native smooth-scroll, which
                // varies in speed/feel across browsers.
                smoothScrollTo(target);
            });
        });

        const sections = tocLinks
            .map((link) => document.getElementById(link.getAttribute('data-scroll-target')))
            .filter(Boolean);

        if (sections.length && 'IntersectionObserver' in window) {
            const setActive = (id) => {
                tocLinks.forEach((link) => {
                    link.classList.toggle('is-active', link.getAttribute('data-scroll-target') === id);
                });
            };

            const spy = new IntersectionObserver(
                (entries) => {
                    const visible = entries
                        .filter((entry) => entry.isIntersecting)
                        .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);

                    if (visible.length) {
                        setActive(visible[0].target.id);
                    }
                },
                { rootMargin: '-20% 0px -65% 0px', threshold: [0, 1] }
            );

            sections.forEach((section) => spy.observe(section));
        }
    }

    // ─── FAQ accordion ───
    // A real height transition (max-height, from measured scrollHeight)
    // rather than the instant snap of native <details>.
    const faqList = document.querySelector('[data-faq]');

    if (faqList) {
        const items = Array.from(faqList.querySelectorAll('.article-faq-item'));

        const openItem = (item) => {
            const panel = item.querySelector('[data-faq-panel]');
            const trigger = item.querySelector('[data-faq-trigger]');

            item.setAttribute('data-open', '');
            trigger.setAttribute('aria-expanded', 'true');
            panel.style.maxHeight = `${panel.scrollHeight}px`;
        };

        const closeItem = (item) => {
            const panel = item.querySelector('[data-faq-panel]');
            const trigger = item.querySelector('[data-faq-trigger]');

            item.removeAttribute('data-open');
            trigger.setAttribute('aria-expanded', 'false');
            panel.style.maxHeight = '0px';
        };

        items.forEach((item) => {
            const trigger = item.querySelector('[data-faq-trigger]');

            if (item.hasAttribute('data-open')) {
                openItem(item);
            }

            trigger.addEventListener('click', () => {
                const isOpen = item.hasAttribute('data-open');

                items.forEach((other) => {
                    if (other !== item) {
                        closeItem(other);
                    }
                });

                if (isOpen) {
                    closeItem(item);
                } else {
                    openItem(item);
                }
            });
        });

        // Keep the open panel's max-height correct if content reflows
        // (fonts loading, window resize).
        window.addEventListener('resize', () => {
            const open = faqList.querySelector('.article-faq-item[data-open]');

            if (open) {
                open.querySelector('[data-faq-panel]').style.maxHeight = `${open.querySelector('[data-faq-panel]').scrollHeight}px`;
            }
        });
    }

    // ─── Section reveal ───
    const revealTargets = document.querySelectorAll('.article-section');

    if (revealTargets.length && 'IntersectionObserver' in window) {
        const reveal = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-inview');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15 }
        );

        revealTargets.forEach((target) => reveal.observe(target));
    } else {
        revealTargets.forEach((target) => target.classList.add('is-inview'));
    }

    // ─── Mobile "Explore the Sections" / "Recently Published" fabs ───
    // Each panel slides in from its own screen edge; opening one closes
    // the other, and the shared overlay + Escape key close whichever is open.
    const mobilePanels = [
        { fab: document.getElementById('articleExploreFab'), panel: document.getElementById('articleExplorePanel') },
        { fab: document.getElementById('articleRecentFab'), panel: document.getElementById('articleRecentPanel') },
    ].filter((entry) => entry.fab && entry.panel);

    if (mobilePanels.length) {
        const overlay = document.getElementById('articleMobileOverlay');
        const body = document.body;

        const closePanel = (entry) => {
            entry.panel.classList.remove('is-open');
            entry.fab.classList.remove('is-active');
            entry.fab.setAttribute('aria-expanded', 'false');
        };

        const closeAllPanels = () => {
            mobilePanels.forEach(closePanel);
            overlay?.classList.remove('is-active');
            body.style.overflow = '';
        };

        const openPanel = (entry) => {
            mobilePanels.forEach((other) => {
                if (other !== entry) {
                    closePanel(other);
                }
            });

            entry.panel.classList.add('is-open');
            entry.fab.classList.add('is-active');
            entry.fab.setAttribute('aria-expanded', 'true');
            overlay?.classList.add('is-active');
            body.style.overflow = 'hidden';
        };

        mobilePanels.forEach((entry) => {
            entry.fab.addEventListener('click', () => {
                if (entry.panel.classList.contains('is-open')) {
                    closeAllPanels();
                } else {
                    openPanel(entry);
                }
            });

            entry.panel.querySelector('[data-article-panel-close]')?.addEventListener('click', closeAllPanels);
        });

        overlay?.addEventListener('click', closeAllPanels);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeAllPanels();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 960) {
                closeAllPanels();
            }
        });
    }
})();
