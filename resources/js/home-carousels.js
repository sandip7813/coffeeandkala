import EmblaCarousel from 'embla-carousel';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-home-carousel]').forEach((root) => {
        const viewport = root.querySelector('.home-embla__viewport');

        if (!viewport) {
            return;
        }

        const prevBtn = root.querySelector('[data-home-prev]');
        const nextBtn = root.querySelector('[data-home-next]');

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
