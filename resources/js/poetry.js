import EmblaCarousel from 'embla-carousel';
import { PageFlip } from 'page-flip';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-poetry-carousel]').forEach((root) => {
        const viewport = root.querySelector('[data-poetry-viewport]');

        if (!viewport) {
            return;
        }

        const prevBtn = root.querySelector('[data-poetry-prev]');
        const nextBtn = root.querySelector('[data-poetry-next]');

        const embla = EmblaCarousel(viewport, {
            align: 'start',
            containScroll: 'trimSnaps',
            dragFree: false,
            loop: false,
            skipSnaps: false,
            duration: 40,
        });

        const syncButtons = () => {
            if (prevBtn) {
                prevBtn.disabled = !embla.canScrollPrev();
            }

            if (nextBtn) {
                nextBtn.disabled = !embla.canScrollNext();
            }
        };

        prevBtn?.addEventListener('click', () => {
            embla.scrollPrev();
        });

        nextBtn?.addEventListener('click', () => {
            embla.scrollNext();
        });

        embla.on('select', syncButtons);
        embla.on('reInit', syncButtons);
        syncButtons();
    });

    document.querySelectorAll('[data-poetry-book]').forEach((stage) => {
        const frame = stage.querySelector('[data-poetry-book-frame]');
        const pages = frame ? frame.querySelectorAll('.poetry-book-page') : null;

        if (!frame || !pages || pages.length === 0) {
            return;
        }

        const total = pages.length;
        const positionEl = document.querySelector('[data-poetry-book-position]');
        const totalEl = document.querySelector('[data-poetry-book-total]');
        const prevBtn = stage.querySelector('[data-poetry-book-prev]');
        const nextNavBtn = stage.querySelector('.poetry-book-nav--next');

        let startPage = parseInt(stage.dataset.start, 10);

        if (Number.isNaN(startPage) || startPage < 0 || startPage >= total) {
            startPage = 0;
        }

        if (totalEl) {
            totalEl.textContent = String(total);
        }

        function updateProgress(pageIndex) {
            if (positionEl) {
                positionEl.textContent = String(pageIndex + 1);
            }

            if (prevBtn) {
                prevBtn.disabled = pageIndex === 0;
            }

            if (nextNavBtn) {
                nextNavBtn.disabled = pageIndex === total - 1;
            }
        }

        // A real, physical page-flip — page-flip drives the 3D fold, crease
        // and shadow itself; slowed down (flippingTime) so it reads as an
        // unhurried turn rather than a snap.
        const pageFlip = new PageFlip(frame, {
            width: 460,
            height: 640,
            size: 'stretch',
            minWidth: 300,
            maxWidth: 620,
            minHeight: 440,
            maxHeight: 840,
            maxShadowOpacity: 0.6,
            showCover: true,
            usePortrait: true,
            flippingTime: 1000,
            startPage,
            mobileScrollSupport: false,
        });

        pageFlip.loadFromHTML(pages);

        pageFlip.on('flip', (event) => {
            updateProgress(event.data);
        });

        updateProgress(startPage);

        // The fanned page-block edges — page-flip only sizes .poetry-book
        // itself, so we measure its real rendered box (it changes with
        // viewport width, and again between landscape/portrait) and place
        // these two strips against it directly, rather than guessing at
        // its size in CSS.
        const leftEdge = stage.querySelector('[data-poetry-book-edge="left"]');
        const rightEdge = stage.querySelector('[data-poetry-book-edge="right"]');

        function syncEdges() {
            if (!leftEdge || !rightEdge) {
                return;
            }

            const stageBox = stage.getBoundingClientRect();
            const bookBox = frame.getBoundingClientRect();
            const top = bookBox.top - stageBox.top;
            const height = bookBox.height;
            const isPortrait = pageFlip.getOrientation ? pageFlip.getOrientation() === 'portrait' : false;
            const current = pageFlip.getCurrentPageIndex ? pageFlip.getCurrentPageIndex() : 0;

            leftEdge.style.top = `${top}px`;
            leftEdge.style.height = `${height}px`;
            leftEdge.style.left = `${bookBox.left - stageBox.left - 6}px`;

            rightEdge.style.top = `${top}px`;
            rightEdge.style.height = `${height}px`;
            rightEdge.style.left = `${bookBox.right - stageBox.left - 6}px`;

            // Each strip stands for the pages still stacked in that
            // direction — the cover has none behind it, the closing page
            // has none ahead, and a single page in portrait mode has no
            // opposite side at all.
            leftEdge.classList.toggle('is-hidden', current === 0);
            rightEdge.classList.toggle('is-hidden', isPortrait || current >= total - 1);
        }

        syncEdges();
        requestAnimationFrame(syncEdges);
        pageFlip.on('flip', syncEdges);
        pageFlip.on('changeOrientation', syncEdges);
        window.addEventListener('resize', syncEdges);
        window.addEventListener('load', syncEdges);

        prevBtn?.addEventListener('click', () => pageFlip.flipPrev());

        stage.querySelectorAll('[data-poetry-book-next]').forEach((btn) => {
            btn.addEventListener('click', () => pageFlip.flipNext());
        });

        document.querySelectorAll('[data-poetry-book-jump]').forEach((btn) => {
            const target = parseInt(btn.dataset.poetryBookJump, 10);

            if (Number.isNaN(target)) {
                return;
            }

            btn.addEventListener('click', () => pageFlip.flip(target));
        });

        document.addEventListener('keydown', (event) => {
            if (event.target instanceof HTMLElement && /input|textarea/i.test(event.target.tagName)) {
                return;
            }

            if (event.key === 'ArrowRight') {
                pageFlip.flipNext();
            } else if (event.key === 'ArrowLeft') {
                pageFlip.flipPrev();
            }
        });
    });
});
