import EmblaCarousel from 'embla-carousel';

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
});
