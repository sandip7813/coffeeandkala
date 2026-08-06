import EmblaCarousel from 'embla-carousel';
import Autoplay from 'embla-carousel-autoplay';

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

    document.querySelectorAll('[data-visual-feature]').forEach((root) => {
        const viewport = root.querySelector('[data-visual-feature-viewport]');

        if (!viewport) {
            return;
        }

        const prevBtn = root.querySelector('[data-visual-feature-prev]');
        const nextBtn = root.querySelector('[data-visual-feature-next]');
        const slides = Array.from(root.querySelectorAll('.visual-feature-slide'));
        const autoplay = Autoplay({
            delay: 5500,
            stopOnInteraction: false,
            stopOnMouseEnter: true,
        });

        const embla = EmblaCarousel(
            viewport,
            {
                align: 'start',
                containScroll: false,
                dragFree: false,
                loop: true,
                skipSnaps: false,
                duration: 45,
            },
            [autoplay],
        );

        const syncActive = () => {
            const selected = embla.selectedScrollSnap();

            slides.forEach((slide, index) => {
                slide.classList.toggle('is-active', index === selected);
            });
        };

        prevBtn?.addEventListener('click', () => {
            embla.scrollPrev();
            autoplay.reset();
        });

        nextBtn?.addEventListener('click', () => {
            embla.scrollNext();
            autoplay.reset();
        });

        embla.on('select', syncActive);
        embla.on('reInit', syncActive);
        syncActive();
    });

    document.querySelectorAll('[data-gallery-story]').forEach((root) => {
        const viewport = root.querySelector('[data-gallery-story-viewport]');

        if (!viewport) {
            return;
        }

        const prevBtn = root.querySelector('[data-gallery-story-prev]');
        const nextBtn = root.querySelector('[data-gallery-story-next]');
        const indexButtons = Array.from(root.querySelectorAll('[data-gallery-story-index]'));
        const autoplay = Autoplay({
            delay: 4500,
            stopOnInteraction: false,
            stopOnMouseEnter: true,
        });

        const embla = EmblaCarousel(
            viewport,
            {
                align: 'start',
                containScroll: 'trimSnaps',
                dragFree: false,
                loop: true,
                skipSnaps: false,
                duration: 40,
            },
            [autoplay],
        );

        const syncControls = () => {
            const selected = embla.selectedScrollSnap();

            if (prevBtn) {
                prevBtn.disabled = !embla.canScrollPrev();
            }

            if (nextBtn) {
                nextBtn.disabled = !embla.canScrollNext();
            }

            indexButtons.forEach((button, index) => {
                button.classList.toggle('is-active', index === selected);
            });
        };

        prevBtn?.addEventListener('click', () => {
            embla.scrollPrev();
            autoplay.reset();
        });

        nextBtn?.addEventListener('click', () => {
            embla.scrollNext();
            autoplay.reset();
        });

        indexButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const index = Number(button.dataset.galleryStoryIndex);

                if (!Number.isNaN(index)) {
                    embla.scrollTo(index);
                    autoplay.reset();
                }
            });
        });

        embla.on('select', syncControls);
        embla.on('reInit', syncControls);
        syncControls();
    });

    document.querySelectorAll('[data-journal-feature]').forEach((root) => {
        const viewport = root.querySelector('[data-journal-feature-viewport]');

        if (!viewport) {
            return;
        }

        const prevBtn = root.querySelector('[data-journal-feature-prev]');
        const nextBtn = root.querySelector('[data-journal-feature-next]');
        const autoplay = Autoplay({
            delay: 5500,
            stopOnInteraction: false,
            stopOnMouseEnter: true,
        });

        const embla = EmblaCarousel(
            viewport,
            {
                align: 'start',
                containScroll: false,
                dragFree: false,
                loop: true,
                skipSnaps: false,
                duration: 40,
            },
            [autoplay],
        );

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
            autoplay.reset();
        });

        nextBtn?.addEventListener('click', () => {
            embla.scrollNext();
            autoplay.reset();
        });

        embla.on('select', syncButtons);
        embla.on('reInit', syncButtons);
        syncButtons();
    });
});
